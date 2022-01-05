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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );

class PhocaMenuCpViewPhocaMenuConfig extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;
	protected $type;


	public function display($tpl = null) {

		$this->t		= PhocaMenuUtils::setVars('config');
		$this->r		= new PhocaMenuRenderAdminView();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->type		= PhocaMenuHelper::getUrlType('config');

		// Set type for JForm
		$this->item->type = $this->type['value'];



		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamenuconfig.php';
		Factory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuConfigHelper::getActions($this->t, $this->state->get('filter.config_id'));
		$paramsC 	= ComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? Text::_( 'COM_PHOCAMENU_NEW' ) : Text::_('COM_PHOCAMENU_EDIT');
		ToolbarHelper::title(   $this->type['info']['text'].': <small><small>[ ' . $text.' ]</small></small>' , 'cogs');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocamenuconfig.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocamenuconfig.save', 'JToolbar_SAVE');
			//JToolbarHelper::addNew('phocamenuconfig.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocamenuconfig.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocamenuconfig.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocamenuconfig.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
