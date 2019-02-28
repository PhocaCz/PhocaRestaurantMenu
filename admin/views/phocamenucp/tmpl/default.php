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
defined('_JEXEC') or die('Restricted access'); ?>
<div id="prm-box">
<div id="j-sidebar-container" class="span2"><?php echo JHtmlSidebar::render(); ?></div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" name="adminForm">
	<div class="adminform">
		<div class="ph-cpanel-left">
			<div id="cpanel"><?php

$class	= $this->t['n'] . 'RenderAdmin';
$link	= 'index.php?option='.$this->t['o'].'&view=';
foreach ($this->views as $k => $v) {
	$linkV	= $link . $this->t['c'] . $k;
	echo $class::quickIconButton( $linkV, 'icon-48-'.$v[0].'.png', JText::_($v[1]), $this->t['i']);
}
				?><div style="clear:both">&nbsp;</div>
			</div>
		</div>

		<div class="ph-cpanel-right">
			<div class="prm-well">
                <?php echo '<div class="ph-cpanel-logo">'.JHtml::_('image', 'media/com_phocamenu/images/administrator/logo-phoca-menu.png', 'Phoca.cz') . '</div>'; ?>
				<div style="float:right;margin:10px;"><?php echo JHTML::_('image', $this->t['i'] . 'logo-phoca-chef.png', 'Phoca.cz' );?></div><?php
echo '<h3>'.  JText::_($this->t['l'] . '_VERSION').' Lite</h3>'
.'<p>'.  $this->t['version'] .'</p>';
echo '<h3>'.  JText::_($this->t['l'] . '_COPYRIGHT').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' Jan Pavelka</p>'
.'<p><a href="https://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>';
echo '<h3>'.  JText::_($this->t['l'] . '_LICENSE').'</h3>'
.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';
echo '<h3>'.  JText::_($this->t['l'] . '_TRANSLATION').': '. JText::_($this->t['l'] . '_TRANSLATION_LANGUAGE_TAG').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' '. JText::_($this->t['l'] . '_TRANSLATER'). '</p>'
.'<p>'.JText::_($this->t['l'] . '_TRANSLATION_SUPPORT_URL').'</p>';
		echo '<div style="border-top:1px solid #c2c2c2"></div><p>&nbsp;</p>'
.'<div class="btn-group"><a class="btn btn-large btn-default" href="https://www.phoca.cz/version/index.php?'.$this->t['c'].'='.  $this->t['version'] .'" target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_UPDATE') .'</a></div>'

            .'<div style="float:right; margin: 0 10px"><a href="https://www.phoca.cz/" target="_blank">'.JHTML::_('image', $this->t['i'] . 'logo.png', 'Phoca.cz' ).'</a></div>';

			?></div>
		</div>

		<div style="clear:both">&nbsp;</div>
		<p>&nbsp;</p>
			<div class="prm-well">
				<?php echo $class::getLinks(); ?>
			</div>

	</div>
	<input type="hidden" name="option" value="<?php echo $this->t['c'] ?>" />
	<input type="hidden" name="view" value="<?php echo $this->t['c'] ?>cp" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>
</div>
