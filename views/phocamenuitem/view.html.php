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

class PhocaMenuCpViewPhocaMenuItem extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $tpl;
	protected $type;
	
	
	public function display($tpl = null) {
		
		$this->t		= PhocaMenuUtils::setVars('item');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('item');
		
		JHTML::stylesheet( $this->t['s'] );

		// Set type for JForm
		$this->item->type = $this->type['value'];

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocamenuitems.php';
		JRequest::setVar('hidemainmenu', true);
		//$bar 		= JToolBar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuItemsHelper::getActions($this->t, $this->state->get('filter.item_id'));
		$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolBarHelper::title(   JText::_( 'COM_PHOCAMENU_ITEM' ).': <small><small>[ ' . $text.' ]</small></small>' , 'file-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::apply('phocamenuitem.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('phocamenuitem.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::addNew('phocamenuitem.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::custom('phocamenuitem.save2copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('phocamenuitem.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('phocamenuitem.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocamenu', true );
	}
}
?>