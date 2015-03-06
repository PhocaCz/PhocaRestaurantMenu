<?php
defined('_JEXEC') or die('Restricted access');
echo '<div id="phocarestaurantmenu" class="dailymenu'.$this->params->get( 'pageclass_sfx' ).'">';
if ( $this->params->get( 'show_page_heading' ) ) { 
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}

echo PhocaMenuFrontRender::renderFrontIcons($this->params->get('pdf'), $this->params->get('print'), $this->params->get('email'), $this->t['printview'], $this->t['displayrss'], $this->params->get('icons'));

echo PhocaMenuRenderViews::renderDailyMenu($this->data, $this->t, $this->params,$this->paramsg);

echo '</div>';
?>