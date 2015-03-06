<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$class		= $this->t['n'] . 'RenderAdminViews';
$r 			=  new $class();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option='.$this->t['o'].'&task='.$this->t['tasks'].'.saveOrderAjax&type='.(int)$this->type['value'].'&tmpl=component';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();

echo '<div id="prm-box">'. "\n";
echo $r->jsJorderTable($listOrder);

echo $this->t['breadcrumb'];


echo $r->startFormType($this->t['o'], (int)$this->type['value'], $this->t['tasks'], 'adminForm');
echo $r->startFilter($this->t['l'].'_FILTER');
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo PhocaMenuHelper::getCategoryList('item', $this->type['value'], $this->state->get('filter.category_id'));
echo $r->endFilter();

echo $r->startMainContainer();
echo $r->startFilterBar();
echo $r->inputFilterSearch($this->t['l'].'_FILTER_SEARCH_LABEL', $this->t['l'].'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);
echo $r->endFilterBar();		

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
echo $r->thCheck('JGLOBAL_CHECK_ALL');

echo '<th class="ph-quantity">'.JHTML::_('grid.sort',  	$this->t['l'].'_QUANTITY', 'a.quantity', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$this->t['l'].'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-price">'.JHTML::_('grid.sort',  	$this->t['l'].'_PRICE', 'a.price', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-price">'.JHTML::_('grid.sort',  	$this->t['l'].'_PRICE2', 'a.price2', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.JHTML::_('grid.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";	

echo '<th class="ph-delete ph-center">'.JTEXT::_($this->t['l'].'_DELETE').'</th>'."\n";
echo '<th class="ph-category">'.JHTML::_('grid.sort',  $this->t['l'].'_GROUP', 'a.catid', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-language">'.JHTML::_('grid.sort',  	'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.JHTML::_('grid.sort',  		$this->t['l'].'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
		
echo '<tbody>'. "\n";

$originalOrders = array();	
$parentsStr 	= "";		
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
			$j++;


$urlTask		= 'index.php?option='.$this->t['o'].'&task='.$this->t['task'];
$orderkey   	= array_search($item->id, $this->ordering[$item->catid]);		
$ordering		= ($listOrder == 'a.ordering');			
$canCreate		= $user->authorise('core.create', $this->t['o']);
$canEdit		= $user->authorise('core.edit', $this->t['o']);
$canDelete		= $user->authorise('core.delete', 'com_phocamenu');
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;
$linkEdit 		= JRoute::_( $this->t['linkedit'].'&id='.(int) $item->id);
$linkView		= JRoute::_( 'index.php?option='.$this->t['o'].'&view='.$this->t['c'].'items&type='.(int)$this->type['value'].'&gid='.$item->id );
$linkRemove 	= 'javascript:void(0);';
$onClickRemove 	= 'javascript:if (confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_ITEMS').'\')){'
				 .' return listItemTask(\'cb'. $i .'\',\''.$this->t['tasks'].'.delete\');'
				 .'}';


$iD = $i % 2;
echo "\n\n";
echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" item-id="'.$item->id.'" parents="'.$item->category_id.'" level="0">'. "\n";

echo $r->tdOrder($canChange, $saveOrder, $orderkey);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small hidden-phone");

$qtO = '';
if ($canCreate || $canEdit) {
	$qtO .= '<a href="'. JRoute::_($linkEdit).'">'. $this->escape($item->quantity).'</a>';
} else {
	$qtO .=  $this->escape($item->quantity);
}
echo $r->td($qtO, "small hidden-phone");
					
$checkO = '';
if ($item->checked_out) {
	$checkO .= JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'].'.', $canCheckin);
}
if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. JRoute::_($linkEdit).'">'. $this->escape($item->title).'</a>';
} else {
	$checkO .= $this->escape($item->title);
}
//$checkO .= '<br /><span class="smallsub">(<span>'.JText::_($this->t['l'].'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small hidden-phone");


$priceO = $price2O = '';
if ($canCreate || $canEdit) {
	$priceO .= '<a href="'. JRoute::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($this->escape($item->price )).'</a>';
	$price2O .= '<a href="'. JRoute::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($this->escape($item->price2 )).'</a>';
} else {
	$priceO .=  PhocaMenuHelper::getPriceFormat ($this->escape($item->price ));
	$price2O .=  PhocaMenuHelper::getPriceFormat ($this->escape($item->price2 ));
}
echo $r->td($priceO, "small hidden-phone");
echo $r->td($price2O, "small hidden-phone");



echo $r->td(JHtml::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small hidden-phone ph-center");

/*
$vO = '<a href="'. $linkView.'" title="'. JText::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS').'">'
	. JHTML::_('image', $this->t['i'].'icon-16-item.png', JText::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS') )
	.'</a>';
echo $r->td($vO, "small hidden-phone ph-center");
	*/

$vD = '';
if ($canDelete) {	
$vD = '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. JText::_('COM_PHOCAMENU_DELETE').'"' 
	.' onclick="return confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\');">'
	. JHTML::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
	.'</a>';
}

echo $r->td($vD, "small hidden-phone ph-center");	
echo $r->td($this->escape($item->category_title), "small hidden-phone ph-center");	
echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));
echo $r->td($item->id, "small hidden-phone");

echo '</tr>'. "\n";
						
	}
}
echo '</tbody>'. "\n";

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();

echo $this->loadTemplate('batch');

echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'.$this->type['info']['catid'].'" value="'.(int)$this->type['actualcatid'].'" />'. "\n";
echo $r->formInputs($listOrder, $originalOrders);
echo $r->endMainContainer();


echo $r->endForm();
echo '</div>'. "\n";
?>


<?php 
/*
 
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_phocamenu');
$saveOrder	= 'a.ordering';
echo $this->tmpl['breadcrumb'];
?>


<form action="<?php echo JRoute::_('index.php?option=com_phocamenu&view=phocamenuitems&type='.(int)$this->type['value']); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_PHOCAMENU_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			
			<?php echo PhocaMenuHelper::getCategoryList('item', $this->type['value'], $this->state->get('filter.category_id')); ?> 
			
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
			
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

		</div>
	</fieldset>
	<div class="clearfix ph-clearfix"> </div>

	<div id="editcell">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>
				<th class="title" width="10%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_QUANTITY', 'a.quantity', $listDirn, $listOrder); ?></th>
				<th class="title" width="56%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
				<th class="title" width="7%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_PRICE', 'a.price', $listDirn, $listOrder); ?></th>
				<th class="title" width="7%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_PRICE2', 'a.price2', $listDirn, $listOrder); ?></th>

				<th width="5%" nowrap="nowrap"><?php echo JText::_('COM_PHOCAMENU_DELETE' ) ?></th>
				<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_PHOCAMENU_PUBLISHED', 'a.published',$listDirn, $listOrder ); ?></th>

				<th width="10%">
				<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder);
				if ($canOrder && $saveOrder) {
					echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'phocamenuitems.saveorder');
				} ?>
				</th>
				
				<th class="title" width="15%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_GROUP', 'a.catid', $listDirn, $listOrder); ?></th>
				<th width="5%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?></th> 
				<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_ID', 'a.id',$listDirn, $listOrder ); ?></th>
			</tr>
			</thead>
			<tbody><?php
				
if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
					
$ordering	= ($listOrder == 'a.ordering');			
$canCreate	= $user->authorise('core.create', 'com_phocamenu');
$canEdit	= $user->authorise('core.edit', 'com_phocamenu');
$canDelete	= $user->authorise('core.delete', 'com_phocamenu');
$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange	= $user->authorise('core.edit.state', 'com_phocamenu') && $canCheckin;
$linkEdit	= JRoute::_( $this->tmpl['linkedit'].'&id='.(int) $item->id );
$linkRemove 	= 'javascript:void(0);';
$onClickRemove 	= 'javascript:if (confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_ITEMS').'\')){'
				 .' return listItemTask(\'cb'. $i .'\',\'phocamenuitems.delete\');'
				 .'}';
				
echo '<tr class="row'. $i % 2 .'">';
echo '<td class="center">'. JHtml::_('grid.id', $i, $item->id) . '</td>';

echo '<td>'; 
if ($canCreate || $canEdit) {
	echo '<a href="'. JRoute::_($linkEdit).'">'. $this->escape($item->quantity).'</a>';
} else {
	echo $this->escape($item->quantity);
}
echo '</td>';

echo '<td>'; 
if ($item->checked_out) {
	echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'phocamenuitems.', $canCheckin);
}

if ($canCreate || $canEdit) {
	echo '<a href="'. JRoute::_($linkEdit).'">'. $this->escape($item->title).'</a>';
} else {
	echo $this->escape($item->title);
}
echo '</td>';

echo '<td align="right">'; 
if ($canCreate || $canEdit) {
	echo '<a href="'. JRoute::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($item->price ).'</a>';
} else {
	echo PhocaMenuHelper::getPriceFormat ($item->price );
}
echo '</td>';

echo '<td align="right">'; 
if ($canCreate || $canEdit) {
	echo '<a href="'. JRoute::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($item->price2 ).'</a>';
} else {
	echo PhocaMenuHelper::getPriceFormat ($item->price2 );
}
echo '</td>';

	
echo '<td align="center">';
if ($canDelete) {	
echo '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. JText::_('COM_PHOCAMENU_DELETE').'"' 
	.' onclick="return confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_GROUP').'\');">'
	. JHTML::_('image', 'administrator/components/com_phocamenu/assets/images/icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
	.'</a>';
}
echo '</td>';	


echo '<td class="center">'. JHtml::_('jgrid.published', $item->published, $i, 'phocamenuitems.', $canChange) . '</td>';


$cntx = 'phocamenuitems';
echo '<td class="order">';
if ($canChange) {
	if ($saveOrder) {
		if ($listDirn == 'asc') {
			echo '<span>'. $this->pagination->orderUpIcon($i, ($item->category_id == @$this->items[$i-1]->category_id), $cntx.'.orderup', 'JLIB_HTML_MOVE_UP', $ordering).'</span>';
			echo '<span>'.$this->pagination->orderDownIcon($i, $this->pagination->total, ($item->category_id == @$this->items[$i+1]->category_id), $cntx.'.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering).'</span>';
		} else if ($listDirn == 'desc') {
			echo '<span>'. $this->pagination->orderUpIcon($i, ($item->category_id == @$this->items[$i-1]->category_id), $cntx.'.orderdown', 'JLIB_HTML_MOVE_UP', $ordering).'</span>';
			echo '<span>'.$this->pagination->orderDownIcon($i, $this->pagination->total, ($item->category_id == @$this->items[$i+1]->category_id), $cntx.'.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering).'</span>';
		}
	}
	$disabled = $saveOrder ?  '' : 'disabled="disabled"';
	echo '<input type="text" name="order[]" size="5" value="'.$item->ordering.'" '.$disabled.' class="text-area-order" />';
} else {
	echo $item->ordering;
}
echo '</td>';

echo '<td class="center">'. $this->escape($item->category_title) . '</td>';

echo '<td class="center">';
if ($item->language=='*') {
	echo JText::_('JALL');
} else {
	echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
}
echo '</td>';

echo '<td align="center">'. $item->id .'</td>';

echo '</tr>';

		}
	}
echo '</tbody>'."\n";		
echo '<tfoot><tr><td colspan="11">'. $this->pagination->getListFooter().'</td></tr></tfoot>'."\n";
echo '</table>' . "\n";

echo $this->loadTemplate('batch');

echo  '</div>'."\n";
	
//<input type="hidden" name="controller" value="phocamenuitem" /> ?>
<input type="hidden" name="type" value="<?php echo (int)$this->type['value'];?>" />
<input type="hidden" name="<?php echo $this->type['info']['catid'];?>" value="<?php echo (int)$this->type['actualcatid'];?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
*/ ?>