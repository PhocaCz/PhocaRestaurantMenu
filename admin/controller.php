<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;
jimport('joomla.application.component.controller');

// Submenu view
$view	= Factory::getApplication()->input->get( 'view', '', '', 'string' );
$type	= Factory::getApplication()->input->get( 'type', '', '', 'string');
$url	= 'index.php?option=com_phocamenu';
$url2	= 'index.php?option=com_phocamenu&view=';

$items 		= array();
$items[0]	= array('COM_PHOCAMENU_CONTROLPANEL', '');
$items[1]	= array('COM_PHOCAMENU_DAILY_MENU', 'phocamenugroups&type=1');
$items[9]	= array('COM_PHOCAMENU_ALL_ITEMS', 'phocamenuallitems');
$items[10]	= array('COM_PHOCAMENU_INFO', 'phocamenuinfo');

foreach ($items as $key => $value) {
	if ($view == '' || $view == 'phocamenucp') {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url, true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 1) && ($view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 1) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 2) && ($view == 'phocamenudays'  || $view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 2) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 3) && ($view == 'phocamenulists' || $view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 3) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 4) && ($view == 'phocamenulists'  || $view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 4) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 5) && ($view == 'phocamenulists' || $view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 5) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 6) && ($view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 6) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 7) && ($view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 7) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if (($type == 8) && ($view == 'phocamenugroups' || $view == 'phocamenuitems')) {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 8) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if ($view == 'phocamenuallitems') {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 9) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}

	if ($view == 'phocamenuinfo') {
		if ($key == 0) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url);
		} else if ($key == 10) {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1], true);
		} else {
			JHtmlSidebar::addEntry(Text::_($value[0]), $url2 . $value[1]);
		}
	}
}

class PhocaMenuCpController extends BaseController
{
	function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}

}
?>
