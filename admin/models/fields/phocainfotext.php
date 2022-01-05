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
use Joomla\CMS\Form\FormField;

jimport('joomla.form.formfield');

class JFormFieldPhocaInfoText extends FormField
{

	protected $type = 'PhocaInfoText';


	protected function getInput()
	{
		$class = 'inputbox';
		if ((string) $this->element['class'] != '') {
			$class = $this->element['class'];
		}
	
		return  '<div class="'.$class.'" style="padding-top:5px">'.$this->value.'</div>';
	}


	protected function getLabel()
	{
		echo '<div class="clearfix ph-clearfix"></div>';
		
			return parent::getLabel();
		
		echo '<div class="clearfix ph-clearfix"></div>';
	}

}