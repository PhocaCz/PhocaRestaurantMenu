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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
jimport('joomla.form.formfield');

class JFormFieldPhocaSelectImage extends FormField
{
	public $type = 'PhocaSelectImage';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$textButton	= 'COM_PHOCAMENU_SELECT_IMAGE';


		$link = 'index.php?option=com_phocamenu&amp;view=phocamenugallery&amp;tmpl=component&amp;field='.$this->id;

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'form-control';
		$size = $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$required = '';

		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];

		$idA		= 'phImageNameModal';

		// Load the modal behavior script.
		/*JHtml::_('behavior.modal', 'a.modal_'.$this->id);

		// If external image, we don't need the filename will be required
		$extId		= (int) $this->form->getValue('extid');
		if ($extId > 0) {
			$readonly	= ' readonly="readonly"';
			return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="-" '.$attr.$readonly.' />';
		}

		// Build the script.
		$script = array();
		$script[] = '	function phocaSelectImage_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));*/

		HTMLHelper::_('jquery.framework');

		Factory::getDocument()->addScriptDeclaration('
			function phocaSelectImage_' . $this->id . '(name) {
				document.getElementById("' . $this->id . '").value = name;
				jQuery(\'#'.$idA.'\').modal(\'toggle\');
			}
		');

		$html[] = '<div class="input-append input-group">';
		$html[] = '<span class="input-append input-group"><input type="text" ' . $required . ' id="' . $this->id . '" name="' . $this->name . '"'
			. ' value="' . $this->value . '"' . $size . $class . ' />';
		$html[] = '<a href="'.$link.'" role="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#'.$idA.'" title="' . Text::_($textButton) . '">'
			. '<span class="icon-image icon-white"></span> '
			. Text::_($textButton) . '</a></span>';
		$html[] = '</div>'. "\n";

		$html[] = HTMLHelper::_(
			'bootstrap.renderModal',
			$idA,
			array(
				'url'    => $link,
				'title'  => Text::_($textButton),
				'width'  => '',
				'height' => '',
				'modalWidth' => '80',
				'bodyHeight' => '80',
                'footer' => '<div  class="ph-info-modal"></div><button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-hidden="true">'
					. Text::_('COM_PHOCAMENU_CLOSE') . '</button>'
			)
		);


		/*$html[] = '<div class="fltlft">';
		$html[] = '	<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .
					' '.$attr.' />';
		$html[] = '</div>';

		// Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '		<a class="modal_'.$this->id.'" title="'.Text::_($textButton).'"' .
							' href="'.($this->element['readonly'] ? '' : $link).'"' .
							' rel="{handler: \'iframe\', size: {x: 780, y: 560}}">';
		$html[] = '			'.Text::_($textButton).'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';*/

		/*$html[] = '<div class="input-append">';
		$html[] = '<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .' '.$attr.' />';
		$html[] = '<a class="modal_'.$this->id.' btn" title="'.Text::_($textButton).'"'
				.' href="'.($this->element['readonly'] ? '' : $link).'"'
				.' rel="{handler: \'iframe\', size: {x: 780, y: 560}}">'
				. Text::_($textButton).'</a>';
		$html[] = '</div>'. "\n";*/


		return implode("\n", $html);
	}
}
