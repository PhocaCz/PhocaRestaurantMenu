<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
jimport('joomla.form.formfield');

class JFormFieldPhocaTextArea extends FormField
{
	protected $type 		= 'PhocaTextArea';
	protected $phocaParams 	= null;

	protected function getInput() {
	
		$document		= Factory::getDocument();
		$option 		= Factory::getApplication()->input->get('option');
		$globalValue 	= $this->_getPhocaParams( $this->element['name'] );
		
		// Initialize some field attributes.
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$columns	= $this->element['cols'] ? ' cols="'.(int) $this->element['cols'].'"' : '';
		$rows		= $this->element['rows'] ? ' rows="'.(int) $this->element['rows'].'"' : '';

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		$value 		= htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		
		// TODO 1.6
		// MENU - Set Default value to "" because of saving "" value into the menu link ( use global = "")
		if ($option == "com_menus") {
			$DefaultValue	= (string)$this->element['default'];
			if ($value == $DefaultValue) {
				$value = '';
			}
		}
		
		// MENU - Display the global value
		if ($option == "com_menus") {
			$html ='<table><tr><td colspan="3"><textarea name="'.$this->name.'" id="'.$this->id.'"' .
				$columns.$rows.$class.$disabled.$onchange.'>' .
				$value .
				'</textarea></td></tr>';		
			$html .='<tr><td>[</td><td><input  type="text"  value="'. $globalValue .'" style="width:15em;border:1px solid #fff;background:#fff;" /></td><td>]</td></tr></table>'; 
		} else {
			$html = '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
				$columns.$rows.$class.$disabled.$onchange.'>' .
				$value .
				'</textarea>';
		}
		return $html;
	}
	
	protected function getLabel() {
		echo '<div class="clearfix"></div>';
		return parent::getLabel();
		echo '<div class="clearfix"></div>';
	}
	
	protected function _setPhocaParams(){
		$component 			= 'com_phocamenu';
		$paramsC			= ComponentHelper::getParams($component) ;
		$this->phocaParams	= $paramsC;
	}

	protected function _getPhocaParams( $name ){
	
		// Don't call sql query by every param item (it will be loaded only one time)
		if (!$this->phocaParams) {
			$params = $this->_setPhocaParams();
		}
		$globalValue 	= $this->phocaParams->get( $name, '' );	
		return $globalValue;
	}
}
?>