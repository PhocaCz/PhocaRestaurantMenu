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
defined('JPATH_BASE') or die;
jimport('joomla.application.component.controller');

class PhocaMenuControllerAdmin extends JControllerAdmin
{
	protected $option;
	protected $text_prefix;
	protected $view_list;
	protected $typeview;

	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('unpublish',	'publish');	// value = 0
		$this->registerTask('archive',		'publish');	// value = 2
		$this->registerTask('trash',		'publish');	// value = -2
		$this->registerTask('report',		'publish');	// value = -3
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');

		// Guess the option as com_NameOfController.
		if (empty($this->option)) {
			$this->option = 'com_'.strtolower($this->getName());
		}

		if (empty($this->typeview)) {
			//$this->typeview = 'group';
			$this->typeview = '';
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the list view as the suffix, eg: OptionControllerSuffix.
		if (empty($this->view_list)) {
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r)) {

				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->view_list = strtolower($r[2]);
		}
	}

	function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JFactory::getApplication()->input->get('cid', array(), '', 'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		if (!is_array($cid) || count($cid) < 1) {
			throw new Exception(JText::_($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
	}

	public function display($cachable = false, $urlparams = false)
	{
		return $this;
	}

	function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid	= JFactory::getApplication()->input->get('cid', array(), '', 'array');
		$data	= array('publish' => 1, 'unpublish' => 0, 'archive'=> 2, 'trash' => -2, 'report'=>-3);
		$task 	= $this->getTask();
		$value	= \Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);



		if (empty($cid)) {
			throw new Exception(JText::_($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value)) {
				throw new Exception($model->getError(), 500);
			} else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_PUBLISHED';
				} else if ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_UNPUBLISHED';
				} else if ($value == 2) {
					$ntext = $this->text_prefix.'_N_ITEMS_ARCHIVED';
				} else {
					$ntext = $this->text_prefix.'_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
	}

	public function reorder()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JFactory::getApplication()->input->get('cid', null, 'post', 'array');
		$inc	= ($this->getTask() == 'orderup') ? -1 : +1;
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		if ($return === false) {
			// Reorder failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else {
			// Reorder succeeded.
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message);
			return true;
		}
	}

	public function saveorder()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks	= JFactory::getApplication()->input->get('cid',	null,	'post',	'array');
		$order	= JFactory::getApplication()->input->get('order',	null,	'post',	'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		// Sanitize the input
		\Joomla\Utilities\ArrayHelper::toInteger($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else
		{
			// Reorder succeeded.
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
			return true;
		}
	}

	public function checkin()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JFactory::getApplication()->input->get('cid', null, 'post', 'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);
		$model 	= $this->getModel();
		$return = $model->checkin($ids);
		if ($return === false) {
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else {
			// Checkin succeeded.
			$message =  JText::plural($this->text_prefix.'_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message);
			return true;
		}
	}

}
