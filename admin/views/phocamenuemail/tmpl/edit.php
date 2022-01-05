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

$js = '
	Joomla.submitbutton = function(task)
	{
		if (task == \''. $this->t['task'] .'.cancel\' || document.formvalidator.isValid(document.getElementById(\'adminForm\'))) {

			Joomla.submitform(task, document.getElementById(\'adminForm\'));
			if (task == \''. $this->t['task'] .'.send\' || task == \''.  $this->t['task'] .'.sendandsave\') {

				if (tinyMCE.get("message").isHidden()) {tinyMCE.get("message").show()};
				tinyMCE.get("message").save();
				if (tinyMCE.get("message").isHidden()) {tinyMCE.get("message").show()}; tinyMCE.get("message").save();

				document.getElementById(\'sending-email\').style.display=\'block\';
			}
		}
		else {
			alert(\''. Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true).'\');
		}
	}';

JFactory::getDocument()->addScriptDeclaration($js);

echo '<div id="prm-box-edit">'. "\n";
echo $r->startFormRoute($this->t['o'], '', 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> Text::_($this->t['l'].'_SEND_EMAIL')
);
echo $r->navigation($tabs);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');

echo '<div class="ph-can-be-saved">'. "\n";
echo '<h3>'.Text::_('COM_PHOCAMENU_EMAIL_DETAILS_CAN_BE_SAVED').'</h3>'."\n";

$formArray 		= array ('fromname', 'from', 'to', 'cc', 'bcc', 'subject');
echo $r->group($this->form, $formArray);
echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.$this->type['value'].'" />';
echo '<input type="hidden" name="jform[id]" id="jform_id" value="'.$this->form->getValue('id').'" />';

echo '</div>'. "\n";

echo '<div class="ph-cannot-be-saved">'. "\n";
echo '<h3>'.Text::_('COM_PHOCAMENU_EMAIL_DETAILS_CANNOT_BE_SAVED').'</h3>'."\n";


$method 		= $this->typeinfo['render'];
$messageOutput 	= PhocaMenuRenderViews::$method($this->bodytext, $this->t, $this->params, null, 1);

echo '<p title="'.Text::_('COM_PHOCAMENU_BODY_DESC').'" class="hasTip">'.Text::_('COM_PHOCAMENU_BODY_LABEL').'</p>';
echo '<div class="clearfix ph-clearfix"></div>';
if ($this->t['enableeditoremail'] == 1) {
	echo $this->editor->display( 'message',htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8'), '100%', '450', '0', '0', array('pagebreak', 'phocadownload', 'readmore', 'image') );

} else {
	echo '<textarea class="text_area" id="message" name="message" style="width:100%;height:450px">'.htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8').'</textarea>';
}
echo '</div>'. "\n";
echo '<div class="clearfix ph-clearfix"></div>'. "\n";



//echo $r->formInputs();

echo '<div class="ph-float-right ph-admin-additional-box">';
if ($this->t['admintool'] == 1 && (int)$this->t['atid'] > 0) {
	// Don't select language as we asked the specific id
} else if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
	jimport('joomla.language.helper');
	$code = LanguageHelper::getLanguages('lang_code');
	if (isset($code[$this->bodytext['itemlanguage']]->title)) {
		echo Text::_('COM_PHOCAMENU_LANGUAGE') . ': '. $code[$this->bodytext['itemlanguage']]->title;
	}
} else {
	$warning = '<span style="float:right;margin-right:5px;margin-top:-5px;" class="hasTip" title="'.Text::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'">'.HTMLHelper::_('image', 'media/com_phocamenu/images/administrator/icon-16-warning.png', '' ).'</span>'. "\n";
	// MUST BE SET AT THE BOTTOM
	//<input type="hidden" name="task" value="phocamenuemail.edit" />

	echo Text::_('COM_PHOCAMENU_SELECT_LANGUAGE'). ''.$warning.' :'. "\n";
	echo '<select name="filter_language" class="form-control" onchange="this.form.submit()">'. "\n";
	echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')). "\n";
	echo '</select>'. "\n";
}
echo '</div>';

echo $r->endTab();


echo $r->endTabs();
echo '</div>';//end span10

// Second Column
//echo '<div class="span2">';



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

echo '<div id="sending-email"><div class="loading"><center>'. HTMLHelper::_('image', 'media/com_phocamenu/images/administrator/icon-sending.gif', '' ) . ' &nbsp; &nbsp; '. Text::_('COM_PHOCAMENU_SENDING_MESSAGE').'</center></div></div>';


echo '</div>'. "\n";

 ?>



