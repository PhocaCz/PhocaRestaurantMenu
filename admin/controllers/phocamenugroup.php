<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
//jimport('joomla.application.component.controllerform');
//class PhocaMenuCpControllerPhocaMenuGroup extends JControllerLegacy
class PhocaMenuCpControllerPhocaMenuGroup extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'group';
	public $typeAlias 	= 'com_phocamenu.phocamenugroup';
	
	public function batch($model = null) {
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$model	= $this->getModel('phocamenugroup', '', array());
		$this->setRedirect(Route::_('index.php?option=com_phocamenu&view=phocamenugroups'.$this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}

}

?>
