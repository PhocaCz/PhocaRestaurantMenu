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

class PhocaMenuRender
{
	public static function renderFormInput($id, $title, $value, $size = 50, $maxlength = 250, $style = '') {
		
		$styleOutput = '';
		if ($style != '') {
			$styleOutput = 'style="'.$style.'"';
		}
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$id.'">'.JText::_($title).':</label>'
				 .'</td><td>'
				 .'<input class="text_area" type="text" name="'.$id.'" id="'.$id.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.$value.'" '.$styleOutput.' />'
				.'</td></tr>';
		return $output;
	}
	
	public static function renderFormTextArea($id, $title, $value, $cols = 60, $rows = 5, $style = '') {
		
		$styleOutput = '';
		if ($style != '') {
			$styleOutput = 'style="'.$style.'"';
		}
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$id.'">'.JText::_($title).':</label>'
				 .'</td><td>'
				 .'<textarea class="text_area" cols="'.$cols.'" rows="'.$rows.'" name="'.$id.'" id="'.$id.'" '.$styleOutput.'>'.$value.'</textarea>'
				.'</td></tr>';
		return $output;
	}

	
	public static function renderFormItemSpecial($id, $title, $special) {
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$id.'">'.JText::_($title).':</label>'
				 .'</td><td>'
				 . $special
				 .'</td></tr>';
		return $output;
	}
	
	public static function renderFormItemImageButton($id, $title, $value, $size = 50, $maxlength = 250, $button) {
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$id.'">'.JText::_($title).':</label>'
				 .'</td><td>'
				 .'<input class="text_area" type="text" name="'.$id.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'" />'
				 .'</td>'
				 .'<td align="left" valign="middle">'
				 .'<div class="button2-left" style="display:inline">'
				 .'<div class="'.$button->name.'">'
				 .'<a class="'.$button->modalname.'" title="'.$button->text.'" href="'.$button->link.'" rel="'.$button->options.'">'.$button->text.'</a>'
				 .'</div>'
				 .'</div>'
				 .'</td></tr>';
		return $output;
	}
	
	
	public static function renderFormStyle() {
	
		$output = '<style type="text/css">'
				.'table.paramlist td.paramlist_key {'
				.'width: 92px;'
				.'text-align: left;'
				.'height: 30px;'
				.'}'
				.'</style>';
		return $output;
	}
	
	public static function renderSubmitButtonJs($itemArray) {
	
		$output = "\n" .'<script language="javascript" type="text/javascript">' . "\n";
		$output .= 'function submitbutton(pressbutton) {' ."\n"
				.'	var form = document.adminForm;' ."\n"
				.'	if (pressbutton == \'cancel\') {' ."\n"
				.'		submitform( pressbutton );' ."\n"
				.'		return;' ."\n"
				.'	}' . "\n\n";
		
		if (is_array($itemArray)) {
		
			foreach ($itemArray as $key => $value) {
			
				if ($value[3] == 0) {
					$equal = '0';
				} else {
					$equal = '""';
				}
				
				if ($key == 0) {
					$output .= 'if (form.'.$value[0].'.value == '.$equal.'){' . "\n"
							.'    alert( "'.JText::_($value[1], $value[2] ).'" )' . "\n"
							.' }';
				
				}
				if ($key > 0) {
					$output .= ' else if (form.'.$value[0].'.value == '.$equal.'){' . "\n"
							.'    alert( "'.JText::_($value[1], $value[2] ).'" )' . "\n"
							.' }';
				
				}
			}
		}
		
		$output .= ' else {' . "\n"
				.'    submitform( pressbutton );'."\n"
				.' }'. "\n"
				.'}'. "\n";
		$output .= '</script>';
		
		return $output;
	}
	
	public static function getIconPDFAdministrator($view, $link = 0, $langSuffix = '') {
		
		//Phoca PDF Restaurant Menu Plugin:
		//$pluginPDF	 	=JPluginHelper::getPlugin('phocapdf', 'restaurantmenu');
		//$componentPDF	=JComponentHelper::getComponent('com_phocapdf', true);
		$pluginPDF	 	=PhocaMenuExtensionHelper::getExtensionInfo('restaurantmenu', 'plugin', 'phocapdf');
		$componentPDF	=PhocaMenuExtensionHelper::getExtensionInfo('com_phocapdf');
	
		// Plugin is installed, Plugin is enabled
		//if(!empty($pluginPDF) && (!empty($componentPDF) && (isset($componentPDF->enabled)) && $componentPDF->enabled == 1)) {
		if ($pluginPDF == 1 && $componentPDF == 1) {
			
			$pluginPDFP	 	=JPluginHelper::getPlugin('phocapdf', 'restaurantmenu');
			
			$pluginP = new JRegistry;
			$pluginP->loadString($pluginPDFP->params);
			//$pluginP = $registry->toArray();
		
			//$pluginP 		= new JParameter( $pluginPDFP->params );
			$pdfDestination	= $pluginP->get('pdf_destination', 'S');
			
			$url		= JURI::root().'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&format=pdf&admin=1'. $langSuffix;
			$urlTools	= JURI::root().'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&format=pdf&admintool=1'.$langSuffix;
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			
			//$text = '<span class="icon-32-pdf" title="'.JText::_('COM_PHOCAMENU_PRINT_PDF').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_PRINT_PDF');
			
			$text = ' <i class="icon-ph-pdf" title="'.JText::_('COM_PHOCAMENU_PRINT_PDF').'"></i> '.JText::_('COM_PHOCAMENU_PRINT_PDF');
			
			$attribs['title']	= JText::_( 'COM_PHOCAMENU_PDF' );
			
			$browser = PhocaMenuHelper::PhocaMenuBrowserDetection('browser');
			if ($browser == 'msie7') {
				$attribs['target'] 	= "_blank";
				$attribute			= 'target="_blank"';
			} else {
				$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
				$attribute			= 'onclick="'.$attribs['onclick'].'"';
			}	
			
			//$output					= JHTML::_('link', JRoute::_($url), $text, $attribs);
			
			$output = '<a href="'.JRoute::_($url).'" class="btn btn-small" '.$attribute.'> <i class="icon-ph-pdf" title="'.JText::_('COM_PHOCAMENU_PRINT_PDF').'"></i > '.JText::_('COM_PHOCAMENU_PRINT_PDF'). '</a>';
			
			
			// Administration Print PDF - Row Tools (used by lists and days)
			$outputTools['url']		= $urlTools;
			$outputTools['attribs']	= $attribute;
		
		} else {
			
			$url	= '#';
			$attribs= array();
		//$text 	= '<span class="icon-32-pdf-dis hasTip" title="'.JText::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_PRINT_PDF');
			
			//$output = ' <i class="icon-ph-pdf-dis" title="'.JText::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'"></i> '.JText::_('COM_PHOCAMENU_PRINT_PDF');
			
			//$output	= JHTML::_('link', JRoute::_($url), $text, $attribs);
			$output = '<a href="#" class="btn btn-small btn-disabled disabled hasTip" title="'.JText::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'" > <i class="icon-ph-pdf-dis"></i > '.JText::_('COM_PHOCAMENU_PRINT_PDF'). '</a>';
			// Administration Print PDF - Row Tools (used by lists and days)
			$outputTools['url']		= '';
			$outputTools['attribs']	= '';
		
		}
		
		// Toolbar
		if ($link ==0) {
			return $output;
		} else {
			return $outputTools;
		}
	}
	
	public static function quickIconButton( $component, $link, $image, $text ) {
		
		$lang	= JFactory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div class="icon-wrapper">';
		} else {
			$button .= '<div class="icon-wrapper">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'">'
				   .JHTML::_('image', 'administrator/components/'.$component.'/assets/images/'.$image, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
}
?>