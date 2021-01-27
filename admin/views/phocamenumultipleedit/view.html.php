<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenurenderviews.php' );

class PhocaMenuCpViewPhocaMenuMultipleEdit extends JViewLegacy

{
	protected $state;
	//protected $item;
	//protected $form;
	protected $t;
	protected $r;
	protected $type;
	protected $typeinfo;
	protected $formdata;
	protected $params;

	public function display($tpl = null) {

		$this->t		= PhocaMenuUtils::setVars('multipleedit');
		$this->r		= new PhocaMenuRenderAdminView();
		$this->state	= $this->get('State');
		$this->type		= PhocaMenuHelper::getUrlType('multipleedit');
		$this->formdata	= $this->get('FormData');
		$this->typeinfo	= PhocaMenuHelper::getTypeInfo('multipleedit',$this->type['value'] );


		$document		= JFactory::getDocument();
		$document->addScript(JURI::root(true).'/media/com_phocamenu/js/addrow.js');
		JHTML::stylesheet('media/com_phocamenu/css/phocamenu.css' );


		$this->params 						= JComponentHelper::getParams( 'com_phocamenu' );
		$this->t['enableeditoremail']		= $this->params->get( 'enable_editor_email', 1 );
		$this->t['enabledescmultiple']		= $this->params->get( 'enable_desc_multiple', 1 );
		$this->t['enabledescmultiplegroup']	= $this->params->get( 'enable_desc_multiple_group', 1 );
		$this->t['dateclass']				= $this->params->get( 'date_class', 0 );
		$this->t['daydateformat']			= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->t['weekdateformat']			= $this->params->get( 'week_date_format', 'l, d. F Y' );
		$this->t['priceprefix']				= $this->params->get( 'price_prefix', '...' );
		$this->t['admintool'] 				= JFactory::getApplication()->input->get('admintool', 0, '', 'int');
		$this->t['atid']					= JFactory::getApplication()->input->get( 'atid', 0, '', 'int' );

		if ($this->t['enabledescmultiple'] == 0) {
			$document->addCustomTag( "<style type=\"text/css\"> \n"
			." .pmdesc, .pmdesctr {display: none;}"
			." </style> \n");
		}

		if ($this->t['enabledescmultiplegroup'] == 0) {
			$document->addCustomTag( "<style type=\"text/css\"> \n"
			." .pm-message {display: none;}"
			." </style> \n");
		}

		$this->paramsg 					= NULL;
		$this->t['phocagallery'] 		= 0;
		$this->t['customclockcode'] 	= '';

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamenumultipleedit.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= JToolbar::getInstance('toolbar');
		$user		= JFactory::getUser();
		//$isNew		= ($this->item->id == 0);
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuMultipleEditHelper::getActions($this->t);
		//$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		//$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolbarHelper::title(   $this->type['info']['text']  , 'edit');

		if ($canDo->get('core.edit')){
			//JToolbarHelper::apply('phocamenumultipleedit.apply', 'JToolbar_APPLY');
			//JToolbarHelper::save('phocamenumultipleedit.save', 'JToolbar_SAVE');
			// Some items can be marked for removing
			$dhtml = '<a class="btn btn-small btn-success" href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0) {submitbutton(\'phocamenumultipleedit.apply\');} else {if(confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\')) {submitbutton(\'phocamenumultipleedit.apply\');}}" class="toolbar"> <i class="icon-apply icon-white" title="'.JText::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'"></i>'.JText::_('COM_PHOCAMENU_SAVE').'</a>';

			$bar->appendButton('Custom', $dhtml);

			$dhtml = '<a class="btn btn-small" href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0) {submitbutton(\'phocamenumultipleedit.save\');} else {if(confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\')) {submitbutton(\'phocamenumultipleedit.save\');}}"> <i class="icon-save" title="'.JText::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'"></i>'.JText::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'</a>';


			$bar->appendButton('Custom', $dhtml);


		}

		JToolbarHelper::cancel('phocamenumultipleedit.cancel', 'JToolbar_CLOSE');

		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
