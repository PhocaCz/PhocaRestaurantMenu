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

class PhocaMenuCpControllerPhocaMenuMultipleEdit extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'multipleedit';
	public $typeAlias 	= 'com_phocamenu.phocamenumultipleedit';
	
	
	public function cancel($key = NULL)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= JRequest::getString('tmpl');
		$layout		= JRequest::getString('layout', 'edit');
		$append		= '';

		// Clean the session data and redirect.
		$app->setUserState($context.'.id',		null);
		$app->setUserState($context.'.data',	null);

		$this->setRedirect(JRoute::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
	}
	
	public function edit()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= JRequest::getString('tmpl');
		$layout		= JRequest::getString('layout', 'edit');
		$append		= '';


		$app->setUserState($context.'.id',	$recordId);
		$app->setUserState($context.'.data', null);
		$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend());
		return true;
	}

	
	
	function save() {
		$post		= JRequest::get('post');
		$app		= JFactory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= JRequest::getString('tmpl');
		$layout		= JRequest::getString('layout', 'edit');
		$append		= '';

		$errorMsg			= '';
		$model 	= $this->getModel( 'phocamenumultipleedit' );		
		$return	= $model->save($post, $errorMsg);
		if ($return) {
			$msg 	= JText::_( 'COM_PHOCAMENU_SUCCESS_MODIFICATION_SAVED_MULTIPLE' );
		} else {
			$msg 	= JText::_( 'COM_PHOCAMENU_ERROR_MODIFICATION_SAVED_MULTIPLE' );
		}
		
		if ($errorMsg != '') {
			$msg .= '. '.$errorMsg. '.'; 
		}
		
		$this->setMessage($msg);
		
		switch ( JRequest::getCmd('task') ) {
			case 'apply':
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.$this->getRedirectToItemAppend(), false));
			break;
			
			case 'save':
			default:
				$this->setRedirect(JRoute::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
			break;
		}
		
		return true;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $key = 'id', $bUrlUse = 0)
	{
		$tmpl		= JRequest::getString('tmpl');
		$layout		= JRequest::getString('layout', 'edit');
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
		$tmpl		= JRequest::getString('tmpl');
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
