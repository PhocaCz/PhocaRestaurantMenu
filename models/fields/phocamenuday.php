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

class JFormFieldPhocaMenuDay extends JFormField
{
	protected $type 		= 'PhocaMenuDay';

	protected function getInput() {
	
		$html = array();
		$attr = '';
		$warning	= ( (string)$this->element['phocawarning'] ? $this->element['phocawarning'] : '' );
		$phocaDay	= ( (string)$this->element['phocaday'] ? $this->element['phocaday'] : '' );
		$attr 	   .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		
		$db = JFactory::getDBO();

		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocamenu_day AS a'
		. ' WHERE a.published = 1'
		. ' AND a.type = '.(int)$phocaDay
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$days = $db->loadObjectList();

		$html[] = JHtml::_('select.genericlist', $days, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		
		if ($warning != '') {
			$html[] ='<div style="position:relative;margin-left:250px;margin-top:-28px;" class="error hasTip" title="'.JText::_($warning).'">'.JHtml::_('image', 'media/com_phocamenu/images/icon-16-warning.png', '',array('style' => 'margin:0;padding:0;margin-right:5px;') ).'</div><div style="clear:both"></div>';
		}
		
		return implode($html);
	}
}