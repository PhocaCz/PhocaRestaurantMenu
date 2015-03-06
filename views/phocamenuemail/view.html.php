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
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenurenderviews.php' );

class PhocaMenuCpViewPhocaMenuEmail extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $type;
	protected $typeinfo;
	protected $bodytext;
	protected $params;
	protected $editor;
	
	
	public function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$this->t		= PhocaMenuUtils::setVars('email');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->editor 	= JFactory::getEditor();
		$this->type		= PhocaMenuHelper::getUrlType('email');
		$this->bodytext	= $this->get('BodyText');
		$this->typeinfo	= PhocaMenuHelper::getTypeInfo('email',$this->type['value'] );
		
		// Set type for JForm
		$this->item->type = $this->type['value'];

		JHTML::stylesheet( $this->t['s'] );

		$this->params 					= JComponentHelper::getParams( 'com_phocamenu' );
		$this->t['enableeditoremail']	= $this->params->get( 'enable_editor_email', 1 );
		$this->t['phocagallery'] 		= 0;
		$this->t['customclockcode'] 	= '';
		$this->t['dateclass']			= $this->params->get( 'date_class', 0 );
		$this->t['daydateformat']		= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->t['weekdateformat']		= $this->params->get( 'week_date_format', 'l, d. F Y' );
		$this->t['priceprefix']			= $this->params->get( 'price_prefix', '...' );
		$this->t['admintool'] 			= $app->input->get('admintool', 0, 'int');
		$this->t['atid']				= $app->input->get( 'atid', 0, 'int' );
		

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocamenuemail.php';
		JRequest::setVar('hidemainmenu', true);
		$bar 		= JToolBar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuEmailHelper::getActions($this->t, $this->state->get('filter.email_id'));
		//$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolBarHelper::title(   $this->type['info']['text']  , 'envelope');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.manage')){
			JToolBarHelper::custom('phocamenuemail.send', 'ph-email', '', 'COM_PHOCAMENU_SEND', false);

		}
		
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::custom('phocamenuemail.sendandsave', 'ph-emailsave', '', 'COM_PHOCAMENU_SEND_AND_SAVE', false);
			JToolBarHelper::apply('phocamenuemail.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('phocamenuemail.save', 'JTOOLBAR_SAVE');
			//JToolBarHelper::addNew('phocamenuemail.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::custom('phocamenuemail.save2copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('phocamenuemail.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('phocamenuemail.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocamenu', true );
	}
}
?>
