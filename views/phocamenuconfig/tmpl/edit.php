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
		<?php echo $this->form->getField('header')->save(); ?>
		<?php echo $this->form->getField('footer')->save(); ?>
		Joomla.submitform(task, document.getElementById('adminForm'));
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
'general' 		=> JText::_($this->t['l'].'_SETTINGS')
);
echo $r->navigation($tabs);

echo '<div class="tab-content">'. "\n";

echo '<div class="tab-pane active" id="general">'."\n"; 

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

echo '</div>'. "\n";

				
echo '</div>';//end tab content
echo '</div>';//end span10

// Second Column
echo '<div class="span2">';


$warning = '<span style="float:right;margin-right:5px;margin-top:-5px;" class="hasTip" title="'.JText::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'">'.JHtml::_('image', 'media/com_phocamenu/images/administrator/icon-16-warning.png', '' ).'</span>'. "\n"; 

echo JText::_('COM_PHOCAMENU_SELECT_LANGUAGE'). ''.$warning.' :'. "\n"; 
echo '<select name="filter_language" class="inputbox" onchange="this.form.submit()">'. "\n";
echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'.(int)$this->item->type)). "\n";
echo '</select>'. "\n";
 

echo '</div>';//end span2

/*
echo $r->formInputs();
echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'. $this->type['info']['catid'].'" value="'.(int)$this->type['valuecatid'].'" />'. "\n";
*/

$filterLang = $this->state->get('filter.language'.(int)$this->item->type);
if ($filterLang == '') { $filterLang = '*';}
echo '<input type="hidden" name="jform[language]" value="'.$filterLang.'" />'. "\n"; 
echo '<input type="hidden" name="task" value="phocamenuconfig.edit" />'. "\n"; 
echo JHtml::_('form.token');

echo $r->endForm();
echo '</div>'. "\n";


/*
<form action="<?php JRoute::_('index.php?option=com_phocamenuconfig'); ?>" method="post" name="adminForm" id="phocamenuconfig-form" class="form-validate">

	<div class="filter-select fltrt">
			
			<div style="position:relative;float:right;width:auto;margin-righ:10px;padding:5px;">
			<?php echo JText::_('COM_PHOCAMENU_SELECT_LANGUAGE'); ?>: 
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'.(int)$this->item->type));?>
			</select>

			<?php 
			echo '<span class="hasTip" title="'.JText::_('COM_PHOCAMENU_WARNING_SELECT_LANG').'">'.JHtml::_('image', 'administrator/components/com_phocamenu/assets/images/icon-16-warning.png', '' )
			.'</span>'
			.'</div>';
			echo '<div style="clear:both"></div>';
			?>

		</div>

	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php
			echo empty($this->item->id) ? JText::_('COM_PHOCAMENU_SETTINGS') : JText::sprintf('COM_PHOCAMENU_SETTINGS', $this->item->id); ?></legend>
		
		<ul class="adminformlist">
			<?php
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

			foreach ($formArray as $value) {
				echo '<li>'.$this->form->getLabel($value) . $this->form->getInput($value).'</li>' . "\n";
			} 
			echo $hiddenArray;
			echo '<input type="hidden" name="jform[type]" id="jform_type" value="'.$this->type['value'].'" />';
			?>
		</ul>
			<?php echo $this->form->getLabel('header'); ?>
			<div class="clearfix ph-clearfix"></div>
			<?php echo $this->form->getInput('header'); ?>
			<div class="clearfix ph-clearfix"></div>
			<?php echo $this->form->getLabel('footer'); ?>
			<div class="clearfix ph-clearfix"></div>
			<?php echo $this->form->getInput('footer'); ?>
			<div class="clearfix ph-clearfix"></div>
	</fieldset>
</div>

<?php ?>

<div class="clearfix ph-clearfix"></div>



<?php // phocamenuconfig.edit - because of filtering language 
$filterLang = $this->state->get('filter.language'.(int)$this->item->type);
if ($filterLang == '') {
	$filterLang = '*';
}

?>
<input type="hidden" name="jform[language]" value="<?php echo $filterLang ?>" />
<input type="hidden" name="task" value="phocamenuconfig.edit" />
<?php echo JHtml::_('form.token'); ?>
</form>
*/ ?>

	
