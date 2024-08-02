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
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\HTML\HTMLHelper;
$r 			=  $this->r;

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
'general' 		=> Text::_($this->t['l'].'_EDIT')
);
echo $r->navigation($tabs);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');

echo '<div class="ph-admin-additional-box">';
if ($this->t['admintool'] == 1 && (int)$this->t['atid'] > 0) {
	// Don't select language as we asked the specific id
} else if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
	jimport('joomla.language.helper');
	$code = LanguageHelper::getLanguages('lang_code');
	if (isset($code[$this->bodytext['itemlanguage']]->title)) {
		echo Text::_('COM_PHOCAMENU_LANGUAGE') . ': '. $code[$this->bodytext['itemlanguage']]->title;
	}
} else {
	echo '<i class="icon-exclamation-triangle" title="'.Text::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'" ></i> '. "\n";
	// MUST BE SET AT THE BOTTOM
	//<input type="hidden" name="task" value="phocamenuemail.edit" />
	echo Text::_('COM_PHOCAMENU_SELECT_LANGUAGE').':'. "\n";
	echo '<select name="filter_language" class="form-select" onchange="this.form.submit()">'. "\n";
	echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')). "\n";
	echo '</select>'. "\n";
}
echo '</div>';

$method = $this->typeinfo['render'];
$output = PhocaMenuRenderViews::$method($this->formdata, $this->t, $this->params, null, 3);


if (isset($output) && $output != '') {
	echo $output;
}

echo $r->endTab();


echo $r->endTabs();
echo '</div>';//end span10

// Second Column
//echo '<div class="span2">';



/*
echo $r->formInputs();
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
*/



//echo '</div>';//end span2

if (isset($this->formdata['itemlanguage']) && $this->formdata['itemlanguage'] != '') {
	$filterLang = $this->formdata['itemlanguage'];
} else {
	$filterLang = $this->state->get('filter.language');
	if ($filterLang == '') {
		$filterLang = '*';
	}
}
if ($this->t['admintool'] == 1 && (int)$this->t['atid'] > 0) {
	// Don't select language as we asked the specific id
} else {
	echo '<input type="hidden" name="language" value="'. $filterLang .'" />'."\n";
}

echo '<input type="hidden" name="task" value="" />'."\n";
echo '<input type="hidden" name="admintool" value="'. (int)$this->t['admintool'].'" />'."\n";
echo '<input type="hidden" name="atid" value="'.(int)$this->t['atid'].'" />'."\n";
echo '<input type="hidden" name="boxchecked" value="0" />'."\n";
echo HTMLHelper::_('form.token')."\n";

echo $r->endForm();

echo '</div>'. "\n";
?>

