<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

use Joomla\CMS\Session\Session;
use Phoca\Render\Adminview;
use Joomla\CMS\Factory;

class PhocaMenuRenderAdminView extends AdminView
{
	public $view 			= '';
	public $viewtype		= 2;
	public $option			= '';
	public $optionLang  	= '';
	public $compatible		= false;
	public $sidebar 		= true;
	protected $document		= false;

	public function __construct(){
		parent::__construct();
		$this->loadMedia();

	}

	public function loadMedia() {
		$urlEip = Uri::base(true).'/index.php?option='.$this->option.'&task='.str_replace('com_', '', $this->option).'editinplace.editinplacetext&format=json&'. Session::getFormToken().'=1';

		HTMLHelper::_('jquery.framework', false);
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.autogrow.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.autogrowtextarea.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.phocajeditable.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.masked.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.maskedinput.min.js', array('version' => 'auto'));
		HTMLHelper::_('stylesheet', 'media/'.$this->option.'/js/jeditable/phocajeditable.css', array('version' => 'auto'));


		$this->document->addScriptOptions('phLang', array(
			'PHOCA_CLICK_TO_EDIT' => Text::_('COM_PHOCAMENU_CLICK_TO_EDIT'),
			'PHOCA_CANCEL' => Text::_('COM_PHOCAMENU_CANCEL'),
			'PHOCA_SUBMIT' => Text::_('COM_PHOCAMENU_SUBMIT'),
			'PHOCA_PLEASE_RELOAD_PAGE_TO_SEE_UPDATED_INFORMATION' => Text::_('COM_PHOCAMENU_PLEASE_RELOAD_PAGE_TO_SEE_UPDATED_INFORMATION')
		));
		$this->document->addScriptOptions('phVars', array('token' => Session::getFormToken(), 'urleditinplace' => $urlEip));

	}
}
?>
