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
echo $r->startForm($this->t['o'], $this->t['task'], $this->item->id, 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> Text::_($this->t['l'].'_GENERAL_OPTIONS'),
'publishing' 	=> Text::_($this->t['l'].'_PUBLISHING_OPTIONS')
);
echo $r->navigation($tabs);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');


$formArray 		= array ('title', 'ordering');
echo $r->group($this->form, $formArray);
echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.(int)$this->item->type.'" />';

//$formArray = array('message');
//echo $r->group($this->form, $formArray, 1);
echo $r->endTab();

echo $r->startTab('publishing', $tabs['publishing']);
foreach($this->form->getFieldset('publish') as $field) {
	echo '<div class="control-group">';
	if (!$field->hidden) {
		echo '<div class="control-label">'.$field->label.'</div>';
	}
	echo '<div class="controls">';
	echo $field->input;
	echo '</div></div>';
}
echo $r->endTab();


echo $r->endTabs();
echo '</div>';//end span10
// Second Column
//echo '<div class="span2"></div>';//end span2
echo $r->formInputs($this->t['task']);
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
echo $r->endForm();
echo '</div>'. "\n";
/*
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;


?>
<script type="text/javascript">
<!--
	function submitbutton(task)
	{
		if (task == 'phocamenulist.cancel' || document.formvalidator.isValid(document.id('phocamenulist-form'))) {
			submitform(task);
		}

		// @to do Deal with the editor methods
		Joomla.submitform(task);
	}
// -->
</script>

<form action="<?php Route::_('index.php?option=com_phocamenulist'); ?>" method="post" name="adminForm" id="phocamenulist-form" class="form-validate">
	<div class="width-60 fltlft">

		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? Text::_('COM_PHOCAMENU_NEW_LIST') : Text::sprintf('COM_PHOCAMENU_EDIT_LIST', $this->item->id); ?></legend>


		<ul class="adminformlist">
			<?php
			// Extid is hidden - only for info if this is an external image (the filename field will be not required)
			$formArray = array ('title', 'ordering');
			foreach ($formArray as $value) {
				echo '<li>'.$this->form->getLabel($value) . $this->form->getInput($value).'</li>' . "\n";
			} ?>
		</ul>
		<input type="hidden" name="jform[type]" id="jform_type" value="<?php echo (int)$this->item->type; ?>" />

			<?php echo $this->form->getLabel('message'); ?>
			<div class="clearfix ph-clearfix"></div>
			<?php echo $this->form->getInput('message'); ?>

		<div class="clearfix ph-clearfix"></div>
		</fieldset>
	</div>

<div class="width-40 fltrt">
	<div style="text-align:right;margin:5px;"></div>
	<?php echo HTMLHelper::_('sliders.start','phocamenux-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo HTMLHelper::_('sliders.panel',Text::_('COM_PHOCAMENU_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
		<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field) {
				echo '<li>';
				if (!$field->hidden) {
					echo $field->label;
				}
				echo $field->input;
				echo '</li>';
			} ?>
			</ul>
		</fieldset>

	<?php echo HTMLHelper::_('sliders.end'); ?>
</div>

<div class="clearfix ph-clearfix"></div>


<input type="hidden" name="type" value="<?php echo (int)$this->item->type; ?>" />
<input type="hidden" name="<?php echo $this->type['info']['catid'];?>" value="<?php echo (int)$this->type['valuecatid'];?>" />
<input type="hidden" name="task" value="" />
<?php echo HTMLHelper::_('form.token'); ?>
</form> */
?>
