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
//$class		= $this->t['n'] . 'RenderAdminViews';
//$r 			=  new $class();
$r 			=  new PhocaMenuRenderAdminviews();
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



echo $r->startFormType($this->t['o'], (int)$this->type['value'], $this->t['tasks'], 'adminForm');
echo $r->startFilter();
echo $r->endFilter();

echo $r->startMainContainer();

echo $this->t['breadcrumb'];

/*echo $r->startFilterBar();
echo $r->inputFilterSearch($this->t['l'].'_FILTER_SEARCH_LABEL', $this->t['l'].'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo PhocaMenuHelper::getCategoryList('group', $this->type['value'], $this->state->get('filter.category_id'));
echo $r->endFilterBar();

echo $r->endFilterBar();*/



echo $r->startFilterBar();
echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
echo $r->endFilterBar();

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->thOrderingXML('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
echo $r->thCheck('JGLOBAL_CHECK_ALL');
echo '<th class="ph-title">'.JHTML::_('searchtools.sort',  	$this->t['l'].'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.JHTML::_('searchtools.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";

echo '<th class="ph-items ph-center">'.JTEXT::_($this->t['l'].'_ITEMS').'</th>'."\n";
echo '<th class="ph-delete ph-center">'.JTEXT::_($this->t['l'].'_DELETE').'</th>'."\n";
echo '<th class="ph-language">'.JHTML::_('searchtools.sort',  	'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.JHTML::_('searchtools.sort',  		$this->t['l'].'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

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
$canDelete	= $user->authorise('core.delete', 'com_phocamenu');
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
//echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" item-id="'.$item->id.'" parents="'.$item->category_id.'" level="0">'. "\n";
echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" >'. "\n";

echo $r->tdOrder($canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");

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
echo $r->td($checkO, "small ");


echo $r->td(JHtml::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small  ph-center");


$vO = '<a href="'. $linkView.'" title="'. JText::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS').'">'
	//. JHTML::_('image', $this->t['i'].'icon-16-item.png', JText::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS') )
		. '<div class="ph-icon-task"><i class="duotone icon-save"></i></div>'
	.'</a>';
echo $r->td($vO, "small  ph-center");


$vD = '';
if ($canDelete) {
$vD = '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. JText::_('COM_PHOCAMENU_DELETE').'"'
	.' onclick="return confirm(\''.JText::_('COM_PHOCAMENU_WARNING_DELETE_GROUP').'\');">'
	//. JHTML::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
		. '<div class="ph-icon-task"><i class="duotone icon-purge"></i></div>'
	.'</a>';
}
echo $r->td($vD, "small  ph-center");


echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));
echo $r->td($item->id, "small ");

echo '</tr>'. "\n";

	}
}
echo '</tbody>'. "\n";

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();

echo $this->loadTemplate('batch');

echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
echo '<input type="hidden" name="'.$this->type['info']['catid'].'" value="'.(int)$this->type['actualcatid'].'" />'. "\n";
echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();

echo $r->endForm();
echo '</div>'. "\n";
?>
