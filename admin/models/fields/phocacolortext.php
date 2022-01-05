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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
jimport('joomla.form.formfield');

class JFormFieldPhocaColorText extends FormField
{
	protected $type 		= 'PhocaColorText';
	protected $phocaParams 	= null;

	protected function getInput() {
	
		$document		= Factory::getDocument();
		$app			= Factory::getApplication();
		$option 		= $app->input->get('option');
		$globalValue 	= $this->_getPhocaParams( $this->element['name'] );
		
		HTMLHelper::stylesheet( 'administrator/components/com_phocamenu/assets/jcp/picker.css' );
		$document->addScript(Uri::base(true).'/components/com_phocamenu/assets/jcp/picker.js');
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
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

		
		// Color Picker
		$nameCP = str_replace('[', '_', $this->name);
		$nameCP = str_replace(']', '', $nameCP);
		//$html .= '<span style="position:relative;float:left;margin-left:10px;margin-top:3px;" onclick="openPicker(\''.$nameCP.'\')"  class="picker_buttons">' . JText::_('com_phocamenu_PICK_COLOR') . '</span>';
		// MENU - Display the global value
		/*if ($option == "com_menus") {
			$html .= '<span style="margin-left:10px;">[</span><span style="background:#fff"> ' . $globalValue . ' </span><span>]</span>';
		}*/
		
		$html[] = '<div class="input-append">';
		$html[] = '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'"'
			   .$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
		$html[] = '<a class="btn" title="'.Text::_('COM_PHOCAMENU_PICK_COLOR').'"'
				.' onclick="openPicker(\''.$nameCP.'\')">'
				. Text::_('COM_PHOCAMENU_PICK_COLOR').'</a>';
		$html[] = '</div>'. "\n";
			
		return implode("\n", $html);

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