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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
jimport('joomla.application.component.controller');

class PhocaMenuControllerAdmin extends AdminController
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

				throw new Exception(Text::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->view_list = strtolower($r[2]);
		}
	}

	function delete()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= Factory::getApplication()->input->get('cid', array(), '', 'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		if (!is_array($cid) || count($cid) < 1) {
			throw new Exception(Text::_($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				$this->setMessage(Text::plural($this->text_prefix.'_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
	}

	public function display($cachable = false, $urlparams = false)
	{
		return $this;
	}

	function publish()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid	= Factory::getApplication()->input->get('cid', array(), '', 'array');
		$data	= array('publish' => 1, 'unpublish' => 0, 'archive'=> 2, 'trash' => -2, 'report'=>-3);
		$task 	= $this->getTask();
		$value	= ArrayHelper::getValue($data, $task, 0, 'int');

		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);



		if (empty($cid)) {
			throw new Exception(Text::_($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

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
				$this->setMessage(Text::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
	}

	public function reorder()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= Factory::getUser();
		$ids	= Factory::getApplication()->input->get('cid', null, 'post', 'array');
		$inc	= ($this->getTask() == 'orderup') ? -1 : +1;
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		if ($return === false) {
			// Reorder failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else {
			// Reorder succeeded.
			$message = Text::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message);
			return true;
		}
	}

	public function saveorder()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get the input
		$pks	= Factory::getApplication()->input->get('cid',	null,	'post',	'array');
		$order	= Factory::getApplication()->input->get('order',	null,	'post',	'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else
		{
			// Reorder succeeded.
			$this->setMessage(Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false));
			return true;
		}
	}

	public function checkin()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= Factory::getUser();
		$ids	= Factory::getApplication()->input->get('cid', null, 'post', 'array');
		$aUrl	= PhocaMenuHelper::getUrlApend($this->typeview);
		$model 	= $this->getModel();
		$return = $model->checkin($ids);
		if ($return === false) {
			// Checkin failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message, 'error');
			return false;
		} else {
			// Checkin succeeded.
			$message =  Text::plural($this->text_prefix.'_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list.$aUrl, false), $message);
			return true;
		}
	}

}
