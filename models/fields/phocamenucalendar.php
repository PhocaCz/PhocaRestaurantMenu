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

class JFormFieldPhocaMenuCalendar extends JFormField
{
	public $type = 'PhocaMenuCalendar';

	protected function getInput()
	{
		// Initialize some field attributes.
		$format 	= $this->element['format'] ? (string) $this->element['format'] : 'Y-m-d';
		$dayType 	= $this->element['daytype'] ? (string) $this->element['daytype'] : 'day';

		$params = JComponentHelper::getParams( 'com_phocamenu' );
		$tmpl	= array();
		$tmpl['dateclass']		= $params->get( 'date_class', 0 );
		
		switch($dayType) {
			case 'week':
				$tmpl['weekdateformat']	= $params->get( 'week_date_format', 'l, d. F Y' );
				
			break;
			case 'day':
			default:
				$tmpl['daydateformat']	= $params->get( 'day_date_format', 'l, d. F Y' );
				$date = PhocaMenuHelper::getDate($this->value, $tmpl['daydateformat'], $tmpl['dateclass']);
			break;
		}
	

		
		
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';

		// Build the attributes array.
		$attributes = array();
		if ($this->element['size'])
		{
			$attributes['size'] = (int) $this->element['size'];
		}
		if ($this->element['maxlength'])
		{
			$attributes['maxlength'] = (int) $this->element['maxlength'];
		}
		if ($this->element['class'])
		{
			$attributes['class'] = (string) $this->element['class'];
		}
		if ((string) $this->element['readonly'] == 'true')
		{
			$attributes['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true')
		{
			$attributes['disabled'] = 'disabled';
		}
		if ($this->element['onchange'])
		{
			$attributes['onchange'] = (string) $this->element['onchange'];
		}
		if ($this->required)
		{
			$attributes['required'] = 'required';
			$attributes['aria-required'] = 'true';
		}
		
		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW')
		{
			$this->value = strftime($format);
		}

		// Get some system objects.
		$config = JFactory::getConfig();
		$user	= JFactory::getUser();

		// If a known filter is given use it.
		switch (strtoupper((string) $this->element['filter']))
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if ((int) $this->value)
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($config->get('offset')));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone.
				if ((int) $this->value)
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;
		}

		$output = ''.$date . '<br />';
		$output .= JHtml::_('calendar', $this->value, $this->name, $this->id, $format, $attributes).'<div class="clearfix ph-clearfix"></div>';
		return $output;
	}
	
}
