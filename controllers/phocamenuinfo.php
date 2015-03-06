<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

class PhocaMenuCpControllerPhocaMenuInfo extends PhocaMenuCpController
{
	function __construct() {
		parent::__construct();
	}
	
	function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_phocamenu' );
	}
}
?>
