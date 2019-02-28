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

class PhocaMenuCpViewPhocaMenuGroup extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $type;


	public function display($tpl = null) {

		$this->t		= PhocaMenuUtils::setVars('group');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('group');

		JHTML::stylesheet( $this->t['s'] );

		// Set type for JForm
		$this->item->type = $this->type['value'];

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamenugroups.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		//$bar 		= JToolbar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuGroupsHelper::getActions($this->t, $this->state->get('filter.category_id'));
		$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? JText::_( 'COM_PHOCAMENU_NEW' ) : JText::_('COM_PHOCAMENU_EDIT');
		JToolbarHelper::title(   JText::_( 'COM_PHOCAMENU_GROUP' ).': <small><small>[ ' . $text.' ]</small></small>' , 'file-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolbarHelper::apply('phocamenugroup.apply', 'JToolbar_APPLY');
			JToolbarHelper::save('phocamenugroup.save', 'JToolbar_SAVE');
			JToolbarHelper::addNew('phocamenugroup.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocamenugroup.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolbarHelper::cancel('phocamenugroup.cancel', 'JToolbar_CANCEL');
		}
		else {
			JToolbarHelper::cancel('phocamenugroup.cancel', 'JToolbar_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
