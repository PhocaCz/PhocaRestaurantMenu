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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JFormFieldPhocaMenuList extends FormField
{
	protected $type 		= 'PhocaMenuList';

	protected function getInput() {

	    HTMLHelper::_('stylesheet', 'media/com_phocamenu/css/administrator/phocamenu.css', array('version' => 'auto'));

		$html = array();
		$attr = '';
		$warning	= ( (string)$this->element['phocawarning'] ? $this->element['phocawarning'] : '' );
		$phocaList	= ( (string)$this->element['phocalist'] ? $this->element['phocalist'] : '' );
		$attr 	   .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="form-select"';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';


		$db = Factory::getDBO();

		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocamenu_list AS a'
		. ' WHERE a.published = 1'
		. ' AND a.type = '.(int)$phocaList
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$lists = $db->loadObjectList();

		$html[] = HTMLHelper::_('select.genericlist', $lists, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		if ($warning != '') {
			$html[] ='<div><i class="icon-exclamation-triangle phi-fc-yd" title="'.Text::_($warning).'" ></i></div>';
		}

		return implode($html);
	}
}
