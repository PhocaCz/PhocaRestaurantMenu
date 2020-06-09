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

defined('_JEXEC') or die('Restricted access');
echo '<div id="phocarestaurantmenu" class="dailymenu'.$this->params->get( 'pageclass_sfx' ).'">';
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}

echo PhocaMenuFrontRender::renderFrontIcons($this->params->get('pdf'), $this->params->get('print'), $this->params->get('email'), $this->t['printview'], $this->t['displayrss'], $this->params->get('icons'));
echo PhocaMenuRenderViews::renderDailyMenu($this->data, $this->t, $this->params,$this->paramsg);
echo '</div>';
?>
