<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocamenu.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocamenuutils.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadmin.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminview.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminviews.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocamenurender.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocamenuextension.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'html'.DS.'batch.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'controllers'.DS.'controlleradmin.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'controllers'.DS.'controllerform.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaMenuCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>