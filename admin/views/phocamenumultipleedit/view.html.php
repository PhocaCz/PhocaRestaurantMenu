<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
jimport( 'joomla.application.component.view' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenurenderviews.php' );

class PhocaMenuCpViewPhocaMenuMultipleEdit extends HtmlView

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


		$document		= Factory::getDocument();
		$document->addScript(Uri::root(true).'/media/com_phocamenu/js/addrow.js');
		HTMLHelper::stylesheet('media/com_phocamenu/css/phocamenu.css' );


		$this->params 						= ComponentHelper::getParams( 'com_phocamenu' );
		$this->t['enableeditoremail']		= $this->params->get( 'enable_editor_email', 1 );
		$this->t['enabledescmultiple']		= $this->params->get( 'enable_desc_multiple', 1 );
		$this->t['enabledescmultiplegroup']	= $this->params->get( 'enable_desc_multiple_group', 1 );
		$this->t['dateclass']				= $this->params->get( 'date_class', 0 );
		$this->t['daydateformat']			= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->t['weekdateformat']			= $this->params->get( 'week_date_format', 'l, d. F Y' );
		$this->t['priceprefix']				= $this->params->get( 'price_prefix', '...' );
		$this->t['admintool'] 				= Factory::getApplication()->input->get('admintool', 0, '', 'int');
		$this->t['atid']					= Factory::getApplication()->input->get( 'atid', 0, '', 'int' );

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
		Factory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		//$isNew		= ($this->item->id == 0);
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuMultipleEditHelper::getActions($this->t);
		//$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		//$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		ToolbarHelper::title(   $this->type['info']['text']  , 'edit');

		if ($canDo->get('core.edit')){
			//JToolbarHelper::apply('phocamenumultipleedit.apply', 'JToolbar_APPLY');
			//JToolbarHelper::save('phocamenumultipleedit.save', 'JToolbar_SAVE');
			// Some items can be marked for removing
			$dhtml = '<joomla-toolbar-button><a class="btn btn-small btn-apply btn-success" href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0) {Joomla.submitbutton(\'phocamenumultipleedit.apply\');} else {if(confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\')) {Joomla.submitbutton(\'phocamenumultipleedit.apply\');}}" class="toolbar"> <span class="icon-apply" title="'.Text::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'"></span>'.Text::_('COM_PHOCAMENU_SAVE').'</a></joomla-toolbar-button>';

			$bar->appendButton('Custom', $dhtml);

			$dhtml = '<joomla-toolbar-button><a class="btn btn-small btn-apply btn-success" href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0) {Joomla.submitbutton(\'phocamenumultipleedit.save\');} else {if(confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\')) {Joomla.submitbutton(\'phocamenumultipleedit.save\');}}"> <span class="icon-apply" title="'.Text::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'"></span>'.Text::_('COM_PHOCAMENU_SAVE_AND_CLOSE').'</a></joomla-toolbar-button>';


			$bar->appendButton('Custom', $dhtml);


		}

		ToolbarHelper::cancel('phocamenumultipleedit.cancel', 'JToolbar_CLOSE');

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
