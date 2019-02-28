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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$class		= $this->t['n'] . 'RenderAdminView';
$r 			=  new $class();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == '<?php echo $this->t['task'] ?>.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {

			Joomla.submitform(task, document.getElementById('adminForm'));
			if (task == '<?php echo $this->t['task'] ?>.send' || task == '<?php echo $this->t['task'] ?>.sendandsave') {

				if (tinyMCE.get("message").isHidden()) {tinyMCE.get("message").show()};
				tinyMCE.get("message").save();
				if (tinyMCE.get("message").isHidden()) {tinyMCE.get("message").show()}; tinyMCE.get("message").save();

				document.getElementById('sending-email').style.display='block';
			}
		}
		else {
			alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
		}
	}
</script><?php

echo '<div id="prm-box-edit">'. "\n";
echo $r->startFormRoute($this->t['o'], '', 'adminForm', 'adminForm');
// First Column
echo '<div class="span10 form-horizontal">';
$tabs = array (
'general' 		=> JText::_($this->t['l'].'_SEND_EMAIL')
);
echo $r->navigation($tabs);

echo '<div class="tab-content">'. "\n";

echo '<div class="tab-pane active" id="general">'."\n";

echo '<div class="ph-can-be-saved">'. "\n";
echo '<h3>'.JText::_('COM_PHOCAMENU_EMAIL_DETAILS_CAN_BE_SAVED').'</h3>'."\n";

$formArray 		= array ('fromname', 'from', 'to', 'cc', 'bcc', 'subject');
echo $r->group($this->form, $formArray);
echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.$this->type['value'].'" />';
echo '<input type="hidden" name="jform[id]" id="jform_id" value="'.$this->form->getValue('id').'" />';

echo '</div>'. "\n";

echo '<div class="ph-cannot-be-saved">'. "\n";
echo '<h3>'.JText::_('COM_PHOCAMENU_EMAIL_DETAILS_CANNOT_BE_SAVED').'</h3>'."\n";


$method 		= $this->typeinfo['render'];
$messageOutput 	= PhocaMenuRenderViews::$method($this->bodytext, $this->t, $this->params, null, 1);

echo '<p title="'.JText::_('COM_PHOCAMENU_BODY_DESC').'" class="hasTip">'.JText::_('COM_PHOCAMENU_BODY_LABEL').'</p>';
echo '<div class="clearfix ph-clearfix"></div>';
if ($this->t['enableeditoremail'] == 1) {
	echo $this->editor->display( 'message',htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8'), '100%', '450', '0', '0', array('pagebreak', 'phocadownload', 'readmore', 'image') );

} else {
	echo '<textarea class="text_area" id="message" name="message" style="width:100%;height:450px">'.htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8').'</textarea>';
}
echo '</div>'. "\n";
echo '<div class="clearfix ph-clearfix"></div>'. "\n";




if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
	$filterLang = $this->bodytext['itemlanguage'];
} else {
	$filterLang = $this->state->get('filter.language');
	if ($filterLang == '') {
		$filterLang = '*';
	}
}


//echo $r->formInputs();

echo '</div>'. "\n";


echo '</div>';//end tab content
echo '</div>';//end span10

// Second Column
echo '<div class="span2">';
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


echo '</div>';//end span2

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

echo '<div id="sending-email"><div class="loading"><center>'. JHTML::_('image', 'media/com_phocamenu/images/administrator/icon-sending.gif', '' ) . ' &nbsp; &nbsp; '. JText::_('COM_PHOCAMENU_SENDING_MESSAGE').'</center></div></div>';


echo '</div>'. "\n";

 ?>



