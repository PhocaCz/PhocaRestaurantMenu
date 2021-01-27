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
$r 			=  $this->r;

$js ='
Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'] .'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {
		Joomla.renderMessages({"error": ["'. JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true).'"]});
	}
}
';
JFactory::getDocument()->addScriptDeclaration($js);

echo '<div id="prm-box-edit">'. "\n";
echo $r->startFormRoute($this->t['o'], '', 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> JText::_($this->t['l'].'_EDIT')
);
echo $r->navigation($tabs);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');

$method = $this->typeinfo['render'];
$output = PhocaMenuRenderViews::$method($this->formdata, $this->t, $this->params, null, 3);


if (isset($output) && $output != '') {
	echo $output;
}


echo '<div class="ph-float-right ph-admin-additional-box">';
if ($this->t['admintool'] == 1 && (int)$this->t['atid'] > 0) {
	// Don't select language as we asked the specific id
} else if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
	jimport('joomla.language.helper');
	$code = JLanguageHelper::getLanguages('lang_code');
	if (isset($code[$this->bodytext['itemlanguage']]->title)) {
		echo JText::_('COM_PHOCAMENU_LANGUAGE') . ': '. $code[$this->bodytext['itemlanguage']]->title;
	}
} else {
	$warning = '<span style="float:right;margin-right:5px;margin-top:-5px;" class="hasTip" title="'.JText::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'">'.JHtml::_('image', 'media/com_phocamenu/images/administrator/icon-16-warning.png', '' ).'</span>'. "\n";
	// MUST BE SET AT THE BOTTOM
	//<input type="hidden" name="task" value="phocamenuemail.edit" />

	echo JText::_('COM_PHOCAMENU_SELECT_LANGUAGE'). ''.$warning.' :'. "\n";
	echo '<select name="filter_language" class="inputbox" onchange="this.form.submit()">'. "\n";
	echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')). "\n";
	echo '</select>'. "\n";
}
echo '</div>';

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
echo JHtml::_('form.token')."\n";

echo $r->endForm();

echo '</div>'. "\n";
?>

