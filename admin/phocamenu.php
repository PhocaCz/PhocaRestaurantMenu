<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
JLoader::registerPrefix('Phocamenu', JPATH_ADMINISTRATOR . '/components/com_phocamenu/libraries/phocamenu');
require JPATH_ADMINISTRATOR . '/components/com_phocamenu/libraries/autoloadPhoca.php';
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamenu.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamenuutils.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamenurender.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamenuextension.php' );
require_once( JPATH_COMPONENT.'/helpers/html/batch.php' );
require_once( JPATH_COMPONENT.'/helpers/html/new.php' );
require_once( JPATH_COMPONENT.'/helpers/controllers/controlleradmin.php' );
require_once( JPATH_COMPONENT.'/helpers/controllers/controllerform.php' );
jimport('joomla.application.component.controller');
$controller	= BaseController::getInstance('PhocaMenuCp');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
?>
