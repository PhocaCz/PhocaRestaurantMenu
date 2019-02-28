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

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaListWarning extends JFormField
{

	protected $type = 'PhocaListWarning';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		$warning	= ( (string)$this->element['phocawarning'] ? $this->element['phocawarning'] : '' );
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		if ($warning != '') {
			//$html .= '<div style="margin-left:10px;">'.JHtml::_('image', 'administrator/components/com_phocamaps/assets/images/icon-16-warning.png', '' ) . '</div><div>' . JText::_($warning).'</div>';
			
			//$html[] ='<div style="position:relative;float:left;width:auto;margin-left:10px">'.JHtml::_('image', 'administrator/components/com_phocamenu/assets/images/icon-16-warning.png', '',array('style' => 'margin:0;padding:0;margin-right:5px;') ).' '.JText::_($warning).'</div><div style="clear:both"></div>';
			
			$html[] ='<div style="position:relative;margin-left:250px;margin-top:-28px;" class="error hasTip" title="'.JText::_($warning).'">'.JHtml::_('image', 'media/com_phocamenu/images/icon-16-warning.png', '',array('style' => 'margin:0;padding:0;margin-right:5px;') ).'</div><div style="clear:both"></div>';
			
			//$html[] = '<span class="error hasTip" title="'.JText::_( 'Warning' ).'::'.JText::_($warningText).'">'. $icon . '</span>';
		}
		
		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option) {

			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['value'], JText::_(trim((string) $option)), 'value', 'text', ((string) $option['disabled']=='true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
