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
defined('_JEXEC') or die();

class JFormFieldPhocaMenuCategory extends JFormField
{
	protected $type 		= 'PhocaMenuCategory';

	protected function getInput() {
		
		$db 		= JFactory::getDBO();
		$list		= '';
		$typeView	= $this->element['menutype'] ? (string)$this->element['menutype'] : 'group';
		$hideSelect	= $this->element['hideselect'] &&  $this->element['hideselect'] == 1 ? (int)$this->element['hideselect'] : 0;
		$catid 		= $this->form->getValue('catid') ? (int) $this->form->getValue('catid') : 0;
		if ($this->form->getValue('type') != 0) {
			$type['value']	= (int)$this->form->getValue('type');
			
		} else {
			$type	= PhocaMenuHelper::getUrlType($typeView);
			if ((int)$catid == 0) {
				$catid = $type['valuecatid'];
			}
		}
		
		if ($this->value == 0) {
			$this->value = $catid;
		}
		
		$attr 		= '';
		$attr 		.= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'" ' : ' ';
		$attr		.= ' class="inputbox"';
		
		$query = '';
		$output= '';
		
		if ($typeView == 'item') {
			// Select groups which have the same day or the same list
			$query 		= 'SELECT a.title AS text, a.id AS value, a.catid as category_id'
					. ' FROM #__phocamenu_group AS a'
					. ' WHERE a.type = '.(int)$type['value']
					
					. ' AND a.catid = (SELECT ag.catid'
									. ' FROM #__phocamenu_group AS ag'
									. ' WHERE ag.id = '.(int)$catid.')'
				//	. ' WHERE a.published = 1'
					. ' ORDER BY a.ordering';
			$selectText = JText::_('COM_PHOCAMENU_SELECT_GROUP');
		} else {
			
			switch ($type['value']){
				case 2:
					$selectText = JText::_('COM_PHOCAMENU_SELECT_DAY');
				break;
				case 3:
				case 4:
				case 5:
				default:
					$selectText = JText::_('COM_PHOCAMENU_SELECT_LIST');
				break;
			}
			
			switch ($type['value']){
				case 2:
				case 3:
				case 4:
				case 5:
					$tableName	= PhocaMenuHelper::getTypeTable($type['value']);
					$query 		= 'SELECT a.title AS text, a.id AS value, a.catid as category_id'
					. ' FROM '.$tableName.' AS a'
					. ' WHERE a.type = '.(int)$type['value']
				//	. ' WHERE a.published = 1'
					. ' ORDER BY a.ordering';
				
					$db->setQuery( $query );
					$itemList = $db->loadObjectList();
					if ($hideSelect != 1) {
						array_unshift($itemList, JHTML::_('select.option', '', '- '.$selectText.' -', 'value', 'text'));
					}
					$list = JHTML::_( 'select.genericlist', $itemList, $this->name, $attr, 'value', 'text', $this->value, $this->id);
					
				break;
			}
		}
			
		if ($query != '') {
			$db->setQuery( $query );
			$categories = $db->loadObjectList();
			
			if ($hideSelect != 1) {
				array_unshift($categories, JHTML::_('select.option', '', '- '.$selectText.' -', 'value', 'text'));
			}
			$output = JHTML::_( 'select.genericlist', $categories, $this->name, $attr, 'value', 'text', $this->value, $this->id);
		}
		
		return $output;
	}
	
	protected function getLabel() {
		echo '<div class="clearfix ph-clearfix"></div>';
		
		$typeView	= $this->element['menutype'] ? (string)$this->element['menutype'] : 'group';
		if ($this->form->getValue('type') != 0) {
			$type['value']	= (int)$this->form->getValue('type');
			
		} else {
			$type	= PhocaMenuHelper::getUrlType($typeView);
		}
		
		switch ($typeView){
			case 2:
				$this->element['label']	= 'COM_PHOCAMENU_FIELD_DAY_LABEL';
				$this->description		= 'COM_PHOCAMENU_FIELD_DAY_DESC';
			break;
			case 3:
			case 4:
			case 5:
			default:
				$this->element['label']	= 'COM_PHOCAMENU_FIELD_GROUP_LABEL';
				$this->description		= 'COM_PHOCAMENU_FIELD_GROUP_DESC';
			break;
		}
		
		switch ($type['value']){
			case 2:
				if ($typeView == 'group') {
					$this->element['label']	= 'COM_PHOCAMENU_FIELD_DAY_LABEL';
					$this->description		= 'COM_PHOCAMENU_FIELD_DAY_DESC';
				} else if ($typeView == 'item'){
					$this->element['label']	= 'COM_PHOCAMENU_FIELD_GROUP_LABEL';
					$this->description		= 'COM_PHOCAMENU_FIELD_GROUP_DESC';
				}
			break;
			case 3:
			case 4:
			case 5:
				if ($typeView == 'group') {
					$this->element['label']	= 'COM_PHOCAMENU_FIELD_LIST_LABEL';
					$this->description		= 'COM_PHOCAMENU_FIELD_LIST_DESC';
				} else if ($typeView == 'item'){
					$this->element['label']	= 'COM_PHOCAMENU_FIELD_GROUP_LABEL';
					$this->description		= 'COM_PHOCAMENU_FIELD_GROUP_DESC';
				}
			break;
			default:
				$this->element['label']	= 'COM_PHOCAMENU_FIELD_GROUP_LABEL';
				$this->description		= 'COM_PHOCAMENU_FIELD_GROUP_DESC';
			break;
		}
		
		
		return parent::getLabel();
		echo '<div class="clearfix ph-clearfix"></div>';
	}
	/*
	protected function getPhocaMenuLabel()
	{
		// Initialize variables.
		$label = '';

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class.' required' : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="'.$this->id.'-lbl" for="'.$this->id.'" class="'.$class.'"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description)) {
			$label .= ' title="'.htmlspecialchars(trim($text, ':').'::' .
						($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8').'"';
		}

		// Add the label text and closing tag.
		$label .= '>'.$text.'</label>';

		return $label;
	}*/
}
?>