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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class PhocaMenuCpViewPhocaMenuGroup extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;
	protected $type;


	public function display($tpl = null) {


		$this->r		= new PhocaMenuRenderAdminView();
		$this->t		= PhocaMenuUtils::setVars('group');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('group');



		// Set type for JForm
		$this->item->type = $this->type['value'];

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamenugroups.php';
		Factory::getApplication()->input->set('hidemainmenu', true);
		//$bar 		= JToolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuGroupsHelper::getActions($this->t, $this->state->get('filter.category_id'));
		$paramsC 	= ComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? Text::_( 'COM_PHOCAMENU_NEW' ) : Text::_('COM_PHOCAMENU_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAMENU_GROUP' ).': <small><small>[ ' . $text.' ]</small></small>' , 'file-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocamenugroup.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocamenugroup.save', 'JToolbar_SAVE');
			ToolbarHelper::addNew('phocamenugroup.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocamenugroup.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocamenugroup.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocamenugroup.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
