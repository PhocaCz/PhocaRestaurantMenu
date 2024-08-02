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
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
$r = $this->r;

$js ='
Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'] .'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {
		Joomla.renderMessages({"error": ["'. Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true).'"]});
	}
}
';
Factory::getDocument()->addScriptDeclaration($js);

echo '<div id="prm-box-edit">'. "\n";
echo $r->startFormRoute($this->t['o'], '', 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> Text::_($this->t['l'].'_SETTINGS')
);
echo $r->navigation($tabs);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');

echo '<div class="ph-admin-additional-box">';

	echo '<i class="icon-exclamation-triangle" title="'.Text::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'" ></i> '. "\n";
	// MUST BE SET AT THE BOTTOM
	//<input type="hidden" name="task" value="phocamenuemail.edit" />
	echo Text::_('COM_PHOCAMENU_SELECT_LANGUAGE').':'. "\n";
	echo '<select name="filter_language" class="form-select" onchange="this.form.submit()">'. "\n";
	echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'.(int)$this->item->type)). "\n";
	echo '</select>'. "\n";

echo '</div>';

switch($this->type['value']) {
	case 1:
		$formArray 		= array ('date');
		$hiddenArray	= '<input type="hidden" name="jform[date_from]" id="jform_date_from" value="0" />'."\n"
						 .'<input type="hidden" name="jform[date_to]" id="jform_date_to" value="0" />';
	break;
	case 2:
		$formArray 		= array ('date_from', 'date_to');
		$hiddenArray	= '<input type="hidden" name="jform[date]" id="jform_date" value="0" />';

	break;
	case 3:
	case 4:
	case 5:
	case 6:
	case 7:
	default:
		$formArray 		= array ();
		$hiddenArray	= '<input type="hidden" name="jform[date]" id="jform_date" value="0" />'."\n"
						 .'<input type="hidden" name="jform[date_from]" id="jform_date_from" value="0" />'."\n"
						 .'<input type="hidden" name="jform[date_to]" id="jform_date_to" value="0" />';
	break;
}

echo $r->group($this->form, $formArray);
echo $hiddenArray;
echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.$this->type['value'].'" />';

$formArray 		= array ('header', 'footer');




echo $r->group($this->form, $formArray, 1);
//echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.(int)$this->item->type.'" />';


echo $r->endTab();


echo $r->endTabs();
echo '</div>';//end span10

// Second Column
//echo '<div class="span2">';





//echo '</div>';//end span2

/*
echo $r->formInputs();
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
*/

$filterLang = $this->state->get('filter.language'.(int)$this->item->type);
if ($filterLang == '') { $filterLang = '*';}
echo '<input type="hidden" name="jform[language]" value="'.$filterLang.'" />'. "\n";
echo '<input type="hidden" name="task" value="phocamenuconfig.edit" />'. "\n";
echo HTMLHelper::_('form.token');

echo $r->endForm();
echo '</div>'. "\n";
?>


