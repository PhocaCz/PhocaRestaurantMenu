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

class JFormFieldPhocaMenuOrdering extends JFormField
{

	protected $type = 'PhocaMenuOrdering';

	protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';

		// Get some field values from the form.
		$id			= (int) $this->form->getValue('id');
		
		if ($this->element['menutype']) {
			switch (strtolower($this->element['menutype'])) {
			
				case "day":
					$whereLabel	=	'';
					$whereValue	=	'';
					$table		=	'#__phocamenu_day';
				break;
				case "list":
					$whereLabel	=	'';
					$whereValue	=	'';
					$table		=	'#__phocamenu_list';
				break;
				case "item":
					$whereLabel	=	'catid';
					$whereValue	=	(int) $this->form->getValue('catid');
					$table		=	'#__phocamenu_item';
				break;
				default:
				case "group":
					$whereLabel	=	'catid';
					$whereValue	=	(int) $this->form->getValue('catid');
					$table		=	'#__phocamenu_group';
				break;
				
			}
		} else {
			$whereLabel	=	'catid';
			$whereValue	=	(int) $this->form->getValue('catid');
			$table		=	'#__phocamenu_group';
		}
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';


		$wheres		= array();
		$wheres[] 	= ' type = '.(int) $this->form->getValue('type');
		if ($whereLabel != '') {	
			$wheres[] = ' '.$whereLabel.' = ' . (int) $whereValue;
		}
		
		
		// Build the query for the ordering list.
		$query = ' SELECT ordering AS value, title AS text'
				.' FROM ' . $table
				.' WHERE ' . implode( ' AND ', $wheres )
				.' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $id ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $id ? 0 : 1);
		}

		return implode($html);
	}
}