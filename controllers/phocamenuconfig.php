<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die();
class PhocaMenuCpControllerPhocaMenuConfig extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'config';
	
	
	/*
	 * changed redirects
	 */
	 public function cancel($key = null)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId	= JRequest::getInt($key);

		// Attempt to check-in the current record.
		if ($recordId) {
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId)) {
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
				$this->setMessage($this->getError(), 'error');
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

				return false;
			}

			if ($checkin) {
				if ($model->checkin($recordId) === false) {
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1));

					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context.'.data',	null);
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

		return true;
	}
	
	/*
	 * added loading of id if there is no cid
	 * changed redirects
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		//$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$cid		= $app->input->get('cid', array(),'array');
		$context	= "$this->option.edit.$this->context";
		$append		= '';
	

		if (empty($key)) {
			$key = $table->getKeyName();
		}
		
		//Language
		//$filterLanguage		= JRequest::getVar('filter_language', array(), 'post', 'string');
		$filterLanguage	= $app->input->get('filter_language', '', 'string');
		
		$model->setLangAndLoadContent($filterLanguage);
		// Try to find config by type (only one id used)
		$existingId = 0;
		if(empty($cid) || (isset($cid[0]) && (int)$cid == 0)) {
			$existingId = $this->getExistingId();
		}
		if ((int)$existingId > 0) {
			$cid[0] = $existingId;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId	= (int) (count($cid) ? $cid[0] : 0);
		//$recordId	= (int) (count($cid) ? $cid[0] : JRequest::getInt($key));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId)) {
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key));

			return false;
		}
		else {
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context.'.data', null);
			
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key));

			return true;
		}
	}
	
	/*
	 * changed redirects
	 */
	
	public function save($key = NULL, $urlVar = NULL)
	{
		
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$model		= $this->getModel();
		$table		= $model->getTable();
		//$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$data		= $app->input->get('jform', array(),'array');
		
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$task		= $this->getTask();

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId	= JRequest::getInt($key);

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		if (!$this->checkEditId($context, $recordId)) {
			// Somehow the person just went to the form and saved it - we don't allow that.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

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
				$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId,'id',1));

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
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

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
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key,1), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1), false));

			return false;
		}

		// Save succeeded, check-in the record.
		
		if ($checkin && $model->checkin($validData[$key]) === false) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Check-in failed, go back to the record and display a notice.
			$this->setError(JText::sprintf('JError_Checkin_saved', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1));

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
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key), false));
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToItemAppend(null, $key, 1), false));
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model);

		return true;
	}
	
	protected function getExistingId() {
		// Only one id for the one type
		$app = JFactory::getApplication();
		//$typeValue	= JRequest::getVar('type', 0, '', 'int');
		$typeValue	= $app->input->get('type', 0, 'int');
		
		//Language
		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 	= JFactory::getApplication('administrator');
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		
		$db = JFactory::getDBO();
		//Possible old format of data
		
		if ($language == '*' || $language == '') {
			$wheresLang = ' AND (a.language ='.$db->Quote('*').' OR a.language ='.$db->Quote('').');';
		} else {
			$wheresLang = ' AND a.language ='.$db->Quote($language).';';
		}
		
		$query = 'SELECT a.id'
				.' FROM #__phocamenu_config AS a'
				.' WHERE a.type = '.(int)$typeValue
				. $wheresLang;
		$db->setQuery($query);
		$dataId = $db->loadObject();
	
	
		if (isset($dataId->id) && (int)$dataId->id > 0) {
			return $dataId->id;
		}
		
		return false;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $key = 'id', $bUrlUse = 0)
	{
		
		//$tmpl		= JRequest::getString('tmpl');
		//$layout		= JRequest::getString('layout', 'edit');
		$app = JFactory::getApplication();
		$tmpl	= $app->input->get('tmpl', '', 'string');
		$layout	= $app->input->get('layout', 'edit', 'string');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);
		$bUrl		= PhocaMenuHelper::getUrlApend($this->typeview, 1);
		
		

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
		
		if ($bUrlUse == 1) {
			return $append . $bUrl;
		} else {
			return '&view='.$this->view_item.$append . $aUrl;
		}
	}
	
	protected function getRedirectToListAppend($bUrlUse = 0)
	{
		$app = JFactory::getApplication();
		$tmpl	= $app->input->get('tmpl', '', 'string');
		//$tmpl		= JRequest::getString('tmpl');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);
		$bUrl		= PhocaMenuHelper::getUrlApend($this->typeview, 1);

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		if ($bUrlUse == 1) {
			return $append . $bUrl;
		} else {
			return '&view='.$this->view_list.$append . $aUrl;
		}
	}
}

?>
