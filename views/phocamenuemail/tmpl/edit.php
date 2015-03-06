<?php defined('_JEXEC') or die;
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
		if (task == '<?php echo $this->t['task'] ?>.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			
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

echo '<input type="hidden" name="language" value="'. $filterLang .'" />'. "\n";
echo '<input type="hidden" name="jform[language]" value="'. $filterLang .'" />'. "\n";

//echo '<input type="hidden" name="task" value="phocamenuemail.edit" />'. "\n";
echo '<input type="hidden" name="admintool" value="'. (int)$this->t['admintool'].'" />'. "\n";
echo '<input type="hidden" name="atid" value="'. (int)$this->t['atid'].'" />'. "\n";
//echo JHtml::_('form.token');
echo $r->formInputs();

echo '</div>'. "\n";

				
echo '</div>';//end tab content
echo '</div>';//end span10

// Second Column
echo '<div class="span2">';

if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
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
	echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'.(int)$this->item->type)). "\n";
	echo '</select>'. "\n";
} 

echo '</div>';//end span2

/*
echo $r->formInputs();
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
*/


//echo '<input type="hidden" name="task" value="phocamenuemail.edit" />'. "\n"; 
//echo JHtml::_('form.token');

echo $r->endForm();

echo '<div id="sending-email"><div class="loading"><center>'. JHTML::_('image', 'media/com_phocamenu/images/administrator/icon-sending.gif', '' ) . ' &nbsp; &nbsp; '. JText::_('COM_PHOCAMENU_SENDING_MESSAGE').'</center></div></div>';


echo '</div>'. "\n";

/*
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'phocamenuemail.cancel' || document.formvalidator.isValid(document.id('phocamenuemail-form'))) {
			Joomla.submitform(task, document.getElementById('phocamenuemail-form'));
			if (task == 'phocamenuemail.send' || task == 'phocamenuemail.sendandsave') {
				document.getElementById('sending-email').style.display='block';
			}
		}
		else {
			alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
		}
	}
</script>


<form action="<?php JRoute::_('index.php?option=com_phocamenu'); ?>" method="post" name="adminForm" id="phocamenuemail-form" class="form-validate">

<div class="filter-select fltrt">
			
			<div style="position:relative;float:right;width:auto;margin-righ:10px;padding:5px;">
			<?php
			if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
				jimport('joomla.language.helper');
				$code = JLanguageHelper::getLanguages('lang_code');
				if (isset($code[$this->bodytext['itemlanguage']]->title)) {
					echo JText::_('COM_PHOCAMENU_LANGUAGE') . ': '. $code[$this->bodytext['itemlanguage']]->title;
				}
			} else { 
				
				echo JText::_('COM_PHOCAMENU_SELECT_LANGUAGE'); ?>: 
				<select name="filter_language" class="inputbox" onchange="this.form.submit()">
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
				</select><?php
				
				// MUST BE SET AT THE BOTTOM
				//<input type="hidden" name="task" value="phocamenuemail.edit" />
				
				echo '<span class="hasTip" title="'.JText::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'">'.JHtml::_('image', 'administrator/components/com_phocamenu/assets/images/icon-16-warning.png', '' )
				.'</span>';
			} ?>
			</div>
			<div style="clear:both"></div>

		</div>

	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PHOCAMENU_EMAIL_DETAILS_CAN_BE_SAVED'); ?></legend>
		
		<ul class="adminformlist">
			<?php

			$formArray 		= array ('fromname', 'from', 'to', 'cc', 'bcc', 'subject');
			
			foreach ($formArray as $value) {
				echo '<li>'.$this->form->getLabel($value) . $this->form->getInput($value).'</li>' . "\n";
			} 
			
			echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.$this->type['value'].'" />';
			echo '<input type="hidden" name="jform[id]" id="jform_id" value="'.$this->form->getValue('id').'" />';
			?>
		</ul>
			
	</fieldset>
</div>

<div class="clearfix ph-clearfix"></div>
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_PHOCAMENU_EMAIL_DETAILS_CANNOT_BE_SAVED'); ?></legend>
		
		<?php
		
			$method = $this->typeinfo['render'];
			$messageOutput = PhocaMenuRenderViews::$method($this->bodytext, $this->t, $this->params, null, 1);

			
			echo '<label title="'.JText::_('COM_PHOCAMENU_BODY_DESC').'" class="hasTip">'.JText::_('COM_PHOCAMENU_BODY_LABEL').'</label>';
			echo '<div class="clearfix ph-clearfix"></div>';
			if ($this->t['enableeditoremail'] == 1) {
				echo $this->editor->display( 'message',htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8'), '100%', '450', '0', '0', array('pagebreak', 'phocadownload', 'readmore', 'image') );
			
			} else {
				echo '<textarea class="text_area" id="message" name="message" style="width:100%;height:450px">'.htmlspecialchars($messageOutput, ENT_COMPAT, 'UTF-8').'</textarea>';
			}
			echo '<div class="clearfix ph-clearfix"></div>';
		
		?>
		
	</fieldset>
</div>

<?php
if (isset($this->bodytext['itemlanguage']) && $this->bodytext['itemlanguage'] != '') {
	$filterLang = $this->bodytext['itemlanguage'];
} else {
	$filterLang = $this->state->get('filter.language');
	if ($filterLang == '') {
		$filterLang = '*';
	}
} ?>
<input type="hidden" name="language" value="<?php echo $filterLang .'" />
<input type="hidden" name="jform[language]" value="<?php echo $filterLang .'" />

<input type="hidden" name="task" value="phocamenuemail.edit" />
<input type="hidden" name="admintool" value="<?php echo (int)$this->t['admintool'].'" />
<input type="hidden" name="atid" value="<?php echo (int)$this->t['atid'].'" />
<?php echo JHtml::_('form.token'); ?>
</form>
<div id="sending-email"><div class="loading"><center><?php echo JHTML::_('image', 'administrator/components/com_phocamenu/assets/images/icon-sending.gif', '' ) . ' &nbsp; &nbsp; '. JText::_('COM_PHOCAMENU_SENDING_MESSAGE'); ?></center></div></div>

*/ ?>


	
