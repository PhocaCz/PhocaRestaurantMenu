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
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenu.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenuextension.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenufrontrender.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenurenderviews.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'views'.DS.'phocamenufrontview.html.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'views'.DS.'phocamenufrontview.pdf.php' );

if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

$classname    = 'PhocaMenuController'.ucfirst($controller);
$controller   = new $classname( );
$controller->execute( JRequest::getVar( 'task' ) );
$controller->redirect();
?>