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

class PhocaMenuCpViewPhocaMenuConfig extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $type;
	
	
	public function display($tpl = null) {
		
		$this->t		= PhocaMenuUtils::setVars('config');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('config');
		
		// Set type for JForm
		$this->item->type = $this->type['value'];

		JHTML::stylesheet( $this->t['s'] );

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocamenuconfig.php';
		JRequest::setVar('hidemainmenu', true);
		$bar 		= JToolBar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuConfigHelper::getActions($this->t, $this->state->get('filter.config_id'));
		$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolBarHelper::title(   $this->type['info']['text'].': <small><small>[ ' . $text.' ]</small></small>' , 'cogs');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::apply('phocamenuconfig.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('phocamenuconfig.save', 'JTOOLBAR_SAVE');
			//JToolBarHelper::addNew('phocamenuconfig.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::custom('phocamenuconfig.save2copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('phocamenuconfig.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('phocamenuconfig.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocamenu', true );
	}
}
?>