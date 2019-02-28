<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenu.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenuextension.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenufrontrender.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenurenderviews.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/views/phocamenufrontview.html.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/views/phocamenufrontview.pdf.php' );

if($controller = JFactory::getApplication()->input->get('controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

$classname    = 'PhocaMenuController'.ucfirst($controller);
$controller   = new $classname( );
//$controller = JControllerLegacy::getInstance('PhocaMenu');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

?>