<?php defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$class		= $this->t['n'] . 'RenderAdminView';
$r 			=  new $class();

?>
<script type="text/javascript">
Joomla.submitbutton = function(task){
	if (task == '<?php echo $this->t['task'] ?>.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		<?php echo $this->form->getField('message')->save(); ?>
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
	else {
		alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
	}
}
</script><?php

echo '<div id="prm-box-edit">'. "\n";
echo $r->startForm($this->t['o'], $this->t['task'], $this->item->id, 'adminForm', 'adminForm');
// First Column
echo '<div class="span10 form-horizontal">';
$tabs = array (
'general' 		=> JText::_($this->t['l'].'_GENERAL_OPTIONS'),
'publishing' 	=> JText::_($this->t['l'].'_PUBLISHING_OPTIONS'),
'advanced' 		=> JText::_($this->t['l'].'_ADVANCED_OPTIONS')
);
echo $r->navigation($tabs);

echo '<div class="tab-content">'. "\n";

echo '<div class="tab-pane active" id="general">'."\n"; 

switch($this->type['value']) {
	case 2:
	
	case 3:
	case 4:
	case 5:
		$formArray 		= array ('title', 'catid', 'ordering');
		$hiddenArray	= '';
	break;
	default:
		$formArray 		= array ('title', 'ordering');
		$hiddenArray	= '<input type="hidden" name="jform[catid]" id="jform_catid" value="0" />';
	break;
}
echo $r->group($this->form, $formArray);
echo $hiddenArray;
echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.(int)$this->item->type.'" />';

$formArray = array('message');
echo $r->group($this->form, $formArray, 1);
echo '</div>'. "\n";

echo '<div class="tab-pane" id="publishing">'."\n"; 
foreach($this->form->getFieldset('publish') as $field) {
	echo '<div class="control-group">';
	if (!$field->hidden) {
		echo '<div class="control-label">'.$field->label.'</div>';
	}
	echo '<div class="controls">';
	echo $field->input;
	echo '</div></div>';
}
echo '</div>';
	/*			
echo '<div class="tab-pane" id="metadata">'. "\n";
echo $this->loadTemplate('metadata');
echo '</div>'. "\n";
*/
echo '<div class="tab-pane" id="advanced">'. "\n";
$formArray 	= array ('display_second_price', 'header_price', 'header_price2');
echo $r->group($this->form, $formArray);
echo '</div>'. "\n";

	
				
echo '</div>';//end tab content
echo '</div>';//end span10
// Second Column
echo '<div class="span2"></div>';//end span2
echo $r->formInputs();
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
echo $r->endForm();
echo '</div>'. "\n";


/*
('_JEXEC') or die;
JHtml::_('behavior.tooltip');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'phocamenugroup.cancel' || document.formvalidator.isValid(document.id('phocamenugroup-form'))) {
			Joomla.submitform(task, document.getElementById('phocamenugroup-form'));
		}
		else {
			alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
		}
	}
</script>

<form action="<?php JRoute::_('index.php?option=com_phocamenugroup'); ?>" method="post" name="adminForm" id="phocamenugroup-form" class="form-validate">
	<div class="width-60 fltlft">
		
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_PHOCAMENU_NEW_GROUP') : JText::sprintf('COM_PHOCAMENU_EDIT_GROUP', $this->item->id); ?></legend>
			
						
		<ul class="adminformlist">
			<?php
			// Extid is hidden - only for info if this is an external image (the filename field will be not required)
			switch($this->type['value']) {
				case 2:
				
				case 3:
				case 4:
				case 5:
					$formArray 		= array ('title', 'catid', 'ordering');
					$hiddenArray	= '';
				break;
				default:
					$formArray 		= array ('title', 'ordering');
					$hiddenArray	= '<input type="hidden" name="jform[catid]" id="jform_catid" value="0" />';
				break;
			}
			
		
			foreach ($formArray as $value) {
				echo '<li>'.$this->form->getLabel($value) . $this->form->getInput($value).'</li>' . "\n";
			} ?>
		</ul>
		<?php
			echo $hiddenArray;
			echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.(int)$this->item->type.'" />';
		
			echo $this->form->getLabel('message');
			echo '<div class="clearfix ph-clearfix"></div>';
			echo $this->form->getInput('message'); ?>
		
		<div class="clearfix ph-clearfix"></div>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PHOCAMENU_ADVANCED_SETTINGS')  ?></legend>
			
						
		<ul class="adminformlist">
			<?php
			$formArray 		= array ('display_second_price', 'header_price', 'header_price2');
		
			foreach ($formArray as $value) {
				echo '<li>'.$this->form->getLabel($value) . $this->form->getInput($value).'</li>' . "\n";
			} ?>
		</ul>
		
		<div class="clearfix ph-clearfix"></div>
		</fieldset>
	</div>

<div class="width-40 fltrt">
	<div style="text-align:right;margin:5px;"></div>
	<?php echo JHtml::_('sliders.start','phocamenux-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel',JText::_('COM_PHOCAMENU_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
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
	
	<?php echo JHtml::_('sliders.end'); ?>
</div>

<div class="clearfix ph-clearfix"></div>

<input type="hidden" name="type" value="<?php echo (int)$this->type['value'];?>" />
<input type="hidden" name="<?php echo $this->type['info']['catid'];?>" value="<?php echo (int)$this->type['valuecatid'];?>" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>

<?php /*
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
switch ($this->tmpl['type']){
	case 2:
		echo PhocaMenuRender::renderSubmitButtonJs(array(
			0 => array('title', 'Group name required', 'true', 1),
			1 => array('catid', 'Day must be selected', 'true', 0))
		);
	break;
	
	case 3:
	case 4:
	case 5:
		echo PhocaMenuRender::renderSubmitButtonJs(array(
			0 => array('title', 'Group name required', 'true', 1),
			1 => array('catid', 'List must be selected', 'true', 0))
		);
	break;
	
	default:
		echo PhocaMenuRender::renderSubmitButtonJs(array(
			0 => array('title', 'Group name required', 'true', 1))
		);
	break;
}
echo PhocaMenuRender::renderFormStyle();
?>

<div id="phocamenu-form"><form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
<div class="col50">
<fieldset class="adminform">
	<legend><?php echo JText::_('Group Detail'); ?></legend>
	<table class="admintable">
		<?php
		echo PhocaMenuRender::renderFormInput('title', 'Title', $this->item->title);
			switch ($this->tmpl['type']) {
				case 2:
				case 3:
				case 4:
				case 5:
				
					if ((int)$this->tmpl['type'] == 2) {
						echo PhocaMenuRender::renderFormItemSpecial('catid', 'Day', $this->lists['catid'] );
					} else {
						echo PhocaMenuRender::renderFormItemSpecial('catid', 'List', $this->lists['catid'] );
					}
					
				break;
			}
		echo PhocaMenuRender::renderFormItemSpecial('published', 'Published', $this->lists['published'] );
		echo PhocaMenuRender::renderFormItemSpecial('ordering', 'Ordering', $this->lists['ordering'] );
		?>
	</table>	
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('Group Message'); ?></legend>
<table class="admintable">
	<?php	
	if ($this->tmpl['enableeditor'] == 1) {
		echo PhocaMenuRender::renderFormItemSpecial('message', 'Message', $this->editor->display( 'message',  $this->item->message, '550', '300', '60', '20', array('pagebreak','phocadownload', 'readmore') ) );
	} else {
		echo PhocaMenuRender::renderFormTextArea('message', 'Message', $this->item->message, 60, 20, '');
	}
	
	?>
</table>
</fieldset>
</div>

<div class="clearfix ph-clearfix"></div>

<input type="hidden" name="controller" value="phocamenugroup" />
<input type="hidden" name="type" value="<?php echo (int)$this->tmpl['type'];?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="<?php echo $this->tmpl['typecatid'];?>" value="<?php echo (int)$this->tmpl['catid'];?>" />
</form>
</div> */ ?>