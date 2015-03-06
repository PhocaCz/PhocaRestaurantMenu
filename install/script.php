<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport( 'joomla.filesystem.folder' );

class com_phocamenuInstallerScript
{
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_phocamenu');
	}
	function uninstall($parent) {
	}

	function update($parent) {
		$msg =  JText::_('COM_PHOCAMENU_UPDATE_TEXT');
		$msg .= ' (' . JText::_('COM_PHOCAMENU_VERSION'). ': ' . $parent->get('manifest')->version . ')';
		$app		= JFactory::getApplication();
		$app->enqueueMessage($msg, 'message');
		$app->redirect(JRoute::_('index.php?option=com_phocamenu'));
	}

	function preflight($type, $parent) {
	}

	function postflight($type, $parent)  {
	}
}