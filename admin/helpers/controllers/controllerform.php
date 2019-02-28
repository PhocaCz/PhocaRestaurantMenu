<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
//jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

class PhocaMenuControllerForm extends JControllerForm
{

	protected $context;
	protected $option;
	protected $view_item;
	protected $view_list;
	protected $text_prefix;
	protected $typeview;

	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the option as com_NameOfController
		if (empty($this->option)) {
			$this->option = 'com_'.strtolower($this->getName());
		}
		
		if (empty($this->typeview)) {
			$this->typeview = 'group';
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context)) {
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r)) {
				
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->context = strtolower($r[2]);
		}

		// Guess the item view as the context.
		if (empty($this->view_item)) {
			$this->view_item = $this->context;
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->view_list)) {
		

			// Simple pluralisation based on public domain snippet by Paul Osman
			// For more complex types, just manually set the variable in your class.
			$plural = array(
				array( '/(x|ch|ss|sh)$/i',		"$1es"),
				array( '/([^aeiouy]|qu)y$/i',	"$1ies"),
				array( '/([^aeiouy]|qu)ies$/i',	"$1y"),
				array( '/(bu)s$/i',				"$1ses"),
				array( '/s$/i',					"s"),
				array( '/$/',					"s")
			);

			// check for matches using regular expressions
			foreach ($plural as $pattern) {
				if (preg_match($pattern[0], $this->view_item)) {
					$this->view_list = preg_replace( $pattern[0], $pattern[1], $this->view_item);
					break;
				}
			}
		}

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply',		'save');
		$this->registerTask('save2new',		'save');
		$this->registerTask('save2copy',	'save');
		$this->registerTask('sendandsave',	'save');
	}

	public function add()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";

		$tmpl		= JFactory::getApplication()->input->get('tmpl');
		$layout		= JFactory::getApplication()->input->get('layout', 'edit');
		$append		= '';

		// Access check.
		if (!$this->allowAdd()) {
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));
			
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 403);
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.id', null);
		$app->setUserState($context.'.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend(), false));
	}

	protected function allowAdd($data = array())
	{
		return JFactory::getUser()->authorise('core.create', $this->option);
	}


	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}
	
	

	protected function allowSave($data, $key = 'id')
	{
		// Initialise variables.
		$recordId	= isset($data[$key]) ? $data[$key] : '0';

		if ($recordId) {
			return $this->allowEdit($data, $key);
		} else {
			return $this->allowAdd($data);
		}
	}


	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId	= $app->input->getInt($key);

		// Attempt to check-in the current record.
		if ($recordId) {
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId)) {
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
				$this->setMessage($this->getError(), 'error');
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

				return false;
			}

			if ($checkin) {
				if ($model->checkin($recordId) === false) {
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key));

					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context.'.data',	null);
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

		return true;
	}

	public function edit($key = NULL, $urlVar = NULL)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$append		= '';

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		// Get the previous record id (if any) and the current record id.
		$recordId	= (int) (count($cid) ? $cid[0] : $app->input->getInt($key));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId)) {
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key));

			return false;
		}
		else {
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context.'.data', null);
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key));

			return true;
		}
	}

	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
	}


	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$model		= $this->getModel();
		$table		= $model->getTable();
		//$data		= JFactory::getApplication()->input->get('jform', array(), 'post', 'array');
		$data		= JFactory::getApplication()->input->get('jform', array(), 'raw');
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$task		= $this->getTask();

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId	= $app->input->getInt($key);

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		if (!$this->checkEditId($context, $recordId)) {
			// Somehow the person just went to the form and saved it - we don't allow that.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Check-in the original row.
			if ($checkin  && $model->checkin($data[$key]) === false) {
				// Check-in failed, go back to the item and display a notice.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');
				$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId));

				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data[$key]	= 0;
			$task		= 'apply';
		}

		// Access check.
		if (!$this->allowSave($data)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test if the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Redirect back to the edit screen.
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));

			return false;
		}

		// Save succeeded, check-in the record.
		
		if ($checkin && $model->checkin($validData[$key]) === false) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Check-in failed, go back to the record and display a notice.
			
			$app->enqueueMessage(JText::sprintf('ERROR_CHECKIN_SAVED', $model->getError()), 'error');
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key));

			return false;
		}

		$this->setMessage(JText::_(($lang->hasKey($this->text_prefix.'_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') .  '_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context.'.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend(null, $key), false));
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $key = 'id')
	{
		$tmpl		= JFactory::getApplication()->input->get('tmpl');
		$layout		= JFactory::getApplication()->input->get('layout', 'edit');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		if ($layout) {
			$append .= '&layout='.$layout;
		}

		if ($recordId) {
			$append .= '&'.$key.'='.$recordId;
		}

		return $append . $aUrl;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToListAppend()
	{
		$tmpl		= JFactory::getApplication()->input->get('tmpl');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		return $append . $aUrl;
	}
	
	public function batch($model = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		return parent::batch($model);
	}
}
