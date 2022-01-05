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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenurenderviews.php' );

use Joomla\CMS\Editor\Editor;

class PhocaMenuCpViewPhocaMenuEmail extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;
	protected $type;
	protected $typeinfo;
	protected $bodytext;
	protected $params;
	protected $editor;


	public function display($tpl = null) {

		$app = Factory::getApplication();
		$this->t		= PhocaMenuUtils::setVars('email');
		$this->r		= new PhocaMenuRenderAdminView();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$editor 		= Factory::getConfig()->get('editor');
		$this->editor 	= Editor::getInstance($editor);


		$this->type		= PhocaMenuHelper::getUrlType('email');
		$this->bodytext	= $this->get('BodyText');
		$this->typeinfo	= PhocaMenuHelper::getTypeInfo('email',$this->type['value'] );

		// Set type for JForm
		$this->item->type = $this->type['value'];



		$this->params 					= ComponentHelper::getParams( 'com_phocamenu' );
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

		require_once JPATH_COMPONENT.'/helpers/phocamenuemail.php';
		Factory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaMenuEmailHelper::getActions($this->t, $this->state->get('filter.email_id'));
		//$paramsC 	= JComponentHelper::getParams('com_phocamenu');

		$text = $isNew ? Text::_( 'COM_PHOCAMENU_NEW' ) : Text::_('COM_PHOCAMENU_EDIT');
		ToolbarHelper::title(   $this->type['info']['text']  , 'envelope');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.manage')){
			ToolbarHelper::custom('phocamenuemail.send', 'ph-email', '', 'COM_PHOCAMENU_SEND', false);

		}

		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::custom('phocamenuemail.sendandsave', 'ph-emailsave', '', 'COM_PHOCAMENU_SEND_AND_SAVE', false);
			ToolbarHelper::apply('phocamenuemail.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocamenuemail.save', 'JToolbar_SAVE');
			//JToolbarHelper::addNew('phocamenuemail.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocamenuemail.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocamenuemail.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocamenuemail.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamenu', true );
	}
}
?>
