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
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaHeadExpert extends JFormField
{
	protected $type = 'PhocaHeadExpert';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		
		echo '<div class="clearfix ph-clearfix"></div>';
		
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		
		$image 		= '';
		$style		= 'background: #FFC2C2; color: #a60000;padding:5px;margin:5px 0;font-weight:bold';
		
		if ($phocaImage != ''){
			$image 	= JHTML::_('image', 'media/com_phocamenu/images/'. $phocaImage, '' );
		}
		
		if ($this->element['default']) {
		
			if ($image != '') {
				return '<div style="'.$style.'">'
				.'<table border="0"><tr>'
				.'<td valign="middle" align="center">'. $image.'</td>'
				.'<td valign="middle" align="center">'
				.'<strong>'. JText::_($this->element['default']) . '</strong></td>'
				.'</tr></table>'
				.'</div>';
			} else {
				return '<div style="'.$style.'">'
				.'<strong>'. JText::_($this->element['default']) . '</strong>'
				.'</div>';
			}
		} else {
			return parent::getLabel();
		}
		echo '<div class="clearfix ph-clearfix"></div>';
	}
}
?>