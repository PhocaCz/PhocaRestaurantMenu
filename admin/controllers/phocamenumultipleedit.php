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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class PhocaMenuCpControllerPhocaMenuMultipleEdit extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'multipleedit';
	public $typeAlias 	= 'com_phocamenu.phocamenumultipleedit';


	public function cancel($key = NULL)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= Factory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->getInput()->get('tmpl');
		$layout		= Factory::getApplication()->getInput()->get('layout', 'edit');
		$append		= '';

		// Clean the session data and redirect.
		$app->setUserState($context.'.id',		null);
		$app->setUserState($context.'.data',	null);

		$this->setRedirect(Route::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
	}

	public function edit($key = NULL, $urlVar = NULL)
	{
		// Initialise variables.
		$app		= Factory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JFactory::getApplication()->getInput()->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->getInput()->get('tmpl');
		$layout		= Factory::getApplication()->getInput()->get('layout', 'edit');
		$append		= '';
		$recordId	= '';

		$app->setUserState($context.'.id',	$recordId);
		$app->setUserState($context.'.data', null);
		$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend());
		return true;
	}



	function save($key = NULL, $urlVar = NULL) {

		$app		= Factory::getApplication();
		//$post 		= $app->getInput()->post->getArray();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JFactory::getApplication()->getInput()->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->getInput()->get('tmpl');
		$layout		= Factory::getApplication()->getInput()->get('layout', 'edit');
		$append		= '';

		$data['itemdesc'] 	= $app->getInput()->get('itemdesc',array(),'array');
		$data['itemadditionalinfo'] 	= $app->getInput()->get('itemadditionalinfo',array(),'array');
		$data['message'] 	= $app->getInput()->get('message',array(),'array');
		$post				= $app->getInput()->post->getArray();


		$post['itemadditionalinfo']	= $data['itemadditionalinfo'];
		$post['itemdesc']	= $data['itemdesc'];
		$post['message']	= $data['message'];



		$errorMsg			= '';
		$model 	= $this->getModel( 'phocamenumultipleedit' );
		$return	= $model->save($post, $errorMsg);
		if ($return) {
			$msg 	= Text::_( 'COM_PHOCAMENU_SUCCESS_MODIFICATION_SAVED_MULTIPLE' );
		} else {
			$msg 	= Text::_( 'COM_PHOCAMENU_ERROR_MODIFICATION_SAVED_MULTIPLE' );
		}

		if ($errorMsg != '') {
			$msg .= '. '.$errorMsg. '.';
		}

		$this->setMessage($msg);

		switch ( Factory::getApplication()->getInput()->get('task') ) {
			case 'apply':
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToItemAppend(), false));
			break;

			case 'save':
			default:
				$this->setRedirect(Route::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
			break;
		}

		return true;
	}

	protected function getRedirectToItemAppend($recordId = null, $key = 'id', $bUrlUse = 0)
	{
		$tmpl		= Factory::getApplication()->getInput()->get('tmpl');
		$layout		= Factory::getApplication()->getInput()->get('layout', 'edit');
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
		$tmpl		= Factory::getApplication()->getInput()->get('tmpl');
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
