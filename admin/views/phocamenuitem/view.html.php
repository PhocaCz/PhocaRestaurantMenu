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
	protected $r;
	protected $t;


	public function display($tpl = null) {

		$app			= JFactory::getApplication();
		$this->t		= PhocaMenuUtils::setVars('item');
		$this->r		= new PhocaMenuRenderAdminView();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('item');


		// Possible non existing type when creating item from all items view
		//if ((int)$this->type['value'] < 1) {
			//$app->redirect(JRoute::_('index.php?option=com_phocamenu', false), JText::_('COM_PHOCAMENU_ERROR_NO_MENU_TYPE_VIEW_FOUND'), 'error');
		//};

		// Set type for JForm
		$this->item->type = $this->type['value'];
		$this->t['typeback']   = $app->input->get('typeback', '', 'string');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamenuitems.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		//$bar 		= JToolbar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuItemsHelper::getActions($this->t, $this->state->get('filter.item_id'));
		$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolbarHelper::title(   JText::_( 'COM_PHOCAMENU_ITEM' ).': <small><small>[ ' . $text.' ]</small></small>' , 'file-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolbarHelper::apply('phocamenuitem.apply', 'JToolbar_APPLY');
			JToolbarHelper::save('phocamenuitem.save', 'JToolbar_SAVE');
			JToolbarHelper::addNew('phocamenuitem.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocamenuitem.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolbarHelper::cancel('phocamenuitem.cancel', 'JToolbar_CANCEL');
		}
		else {
			JToolbarHelper::cancel('phocamenuitem.cancel', 'JToolbar_CLOSE');

			//JToolbarHelper::cancel('phocamenuallitem.cancel', 'JToolbar_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
