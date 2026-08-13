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
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

use Phoca\Text\Text as PhocaText;

HTMLHelper::_('jquery.framework');

$r 			= $this->r;
$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
/*
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option='.$this->t['o'].'&task='.$this->t['tasks'].'.saveOrderAjax&type='.(int)$this->type['value'].'&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}*/
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo '<div id="prm-box">'. "\n";
echo $r->jsJorderTable($listOrder);



echo $r->startFormType($this->t['o'], (int)$this->type['value'], $this->t['tasks'], 'adminForm');
//echo $r->startFilter();
//echo $r->endFilter();

echo $r->startMainContainer();

echo $this->t['breadcrumb'];
/*
echo $r->startFilterBar();
echo $r->inputFilterSearch($this->t['l'].'_FILTER_SEARCH_LABEL', $this->t['l'].'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
//echo PhocaMenuHelper::getCategoryList('group', $this->type['value'], $this->state->get('filter.category_id'));
echo $r->endFilterBar();

echo $r->endFilterBar();*/

//echo $r->startFilterBar();
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
//echo $r->endFilterBar();

echo $r->startTable('categoryList');

echo $r->startTblHeader();

//echo $r->thOrderingXML('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
//echo $r->thCheck('JGLOBAL_CHECK_ALL');

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);

echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";

echo '<th class="ph-groups ph-center">'.Text::_($this->t['l'].'_GROUPS').'</th>'."\n";
echo '<th class="ph-delete ph-center">'.Text::_($this->t['l'].'_ACTION').'</th>'."\n";

if ($this->t['displayitemtools'] == 1) {
	echo '<th width="4" colspan="4" nowrap="nowrap" class="ph-tools ph-center">'. Text::_('COM_PHOCAMENU_TOOLS' ).'</th>';
}

echo '<th class="ph-language">'.HTMLHelper::_('searchtools.sort',  	'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.HTMLHelper::_('searchtools.sort',  		$this->t['l'].'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

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
$linkEdit 		= Route::_( $this->t['linkedit'].'&id='.(int) $item->id);
$linkView		= Route::_( 'index.php?option='.$this->t['o'].'&view='.$this->t['c'].'groups&type='.(int)$this->type['value'].'&lid='.$item->id );
$linkRemove 	= 'javascript:void(0);';
$onClickRemove 	= 'javascript:if (confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEMS', true).'\')){'
				 .' return Joomla.listItemTask(\'cb'. $i .'\',\''.$this->t['tasks'].'.delete\');'
				 .'}';

/*
$iD = $i % 2;
echo "\n\n";
//echo '<tr class="row'.$iD.'" sortable-group-id="0" item-id="'.$item->id.'" parents="0" level="0">'. "\n";
echo '<tr class="row'.$iD.'" sortable-group-id="0" >'. "\n";
echo $r->tdOrder($canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");*/

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);

/*
$checkO = '';
if ($item->checked_out) {
	$checkO .= HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'].'.', $canCheckin);
}

if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. Route::_($linkEdit).'">'. $this->escape($item->title).'</a>';
} else {
	$checkO .= $this->escape($item->title);
}
//$checkO .= '<br /><span class="smallsub">(<span>'.JText::_($this->t['l'].'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small ");
*/
$o = array();
if ($item->checked_out) {
	$o[] = HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'] . '.', $canCheckin);
}
if ($canCreate || $canEdit) {
	$o[] = '<span class="ph-editinplace-text ph-eip-text ph-eip-title" id="list' . ':' .'title'.':'.(int)$item->id . '">' . PhocaText::filterValue($item->title, 'text') . '</span>';
} else {
	$o[] = $this->escape($item->title);
}

echo $r->td(implode("\n", $o), 'small');




echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small  ph-center");


$vO = '<a class="ph-inline-task" href="'. $linkView.'" title="'. Text::_('COM_PHOCAMENU_VIEW_LIST_GROUPS').'">'
	//. JHtml::_('image', $this->t['i'].'icon-16-item.png', JText::_('COM_PHOCAMENU_VIEW_LIST_GROUPS') )
		. '<div class="ph-cp-item ph-icon-task"><i class="duotone icon-save"></i></div>'
	.'</a>';
echo $r->td($vO, "small  ph-center");

/*
$vD = '';
if ($canDelete) {
$vD = '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. Text::_('COM_PHOCAMENU_DELETE').'"'
	.' onclick="return confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_LIST').'\');">'
	//. JHtml::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
		. '<div class="ph-icon-task"><i class="duotone icon-purge"></i></div>'
	.'</a>';
}
echo $r->td($vD, "small  ph-center");
*/

$action = '<div class="ph-action-inline-icon-box">';
if ($canCreate || $canEdit) {
	$action .= '<a class="ph-inline-task" href="' . Route::_($linkEdit) . '" title="'. Text::_('COM_PHOCAMENU_EDIT').'"><span class="ph-cp-item ph-icon-task"><i class="duotone icon-pencil"></i></span></a>';
}

if ($canDelete) {
$action .= '<a class="ph-inline-task" href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. Text::_('COM_PHOCAMENU_DELETE').'"'
	.' onclick="return confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_LIST').'\');">'
	//. JHtml::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
		. '<span class="ph-cp-item ph-icon-task"><i class="duotone icon-purge"></i></span>'
	.'</a>';
}
$action .= '</div>';

echo $r->td($action, "small  ph-center");

if ($this->t['displayitemtools'] == 1) {

	// Multiple Edit
	echo '<td align="center" width="1" class="small ph-center ph-tools-td">'
	.'<a class="ph-inline-task" href="'.Route::_($this->t['linkmultiple'].'&admintool=1&atid='.(int)$item->id).'" title="'. Text::_('COM_PHOCAMENU_MULTIPLE_EDIT').'">'
	. ''
	//.JHtml::_('image', $this->t['i'].'icon-16-multiple.png', JText::_('COM_PHOCAMENU_MULTIPLE_EDIT'), 'style="max-width: none"' )
			. '<div class="ph-cp-item ph-icon-task"><i class="duotone icon-apply"></i></div>'
	.'</a></td>';

	// Email
	echo '<td align="center" width="1" class="small ph-center ph-tools-td">'
	.'<a class="ph-inline-task" href="'.Route::_($this->t['linkemail'].'&admintool=1&atid='.(int)$item->id).'" title="'. Text::_('COM_PHOCAMENU_EMAIL').'">'
	//.JHtml::_('image', $this->t['i'].'icon-16-email.png', JText::_('COM_PHOCAMENU_EMAIL'), 'style="max-width: none"' )
			. '<div class="ph-cp-item ph-icon-task"><i class="duotone icon-envelope"></i></div>'
	.'</a></td>';

	// Preview
	echo '<td align="center" width="1" class="small ph-center ph-tools-td">';


	//.'<a class="modal" href="'. JRoute::_($this->t['linkpreview'].'&task=preview&admintool=1&atid='.(int)$item->id).'"'
	//.' rel="{handler: \'iframe\', size: {x: 640, y: 480}}" title="'. JText::_('COM_PHOCAMENU_PREVIEW').'">'
	//.JHtml::_('image', $this->t['i'].'icon-16-preview.png', JText::_('COM_PHOCAMENU_PREVIEW'), 'style="max-width: none"' ).'</a></td>';



	$html 		= array();
	$idA		= 'phMenuPreview'.(int)$item->id;
	$linkPr		= Route::_($this->t['linkpreview'].'&task=preview&admintool=1&atid='.(int)$item->id);
	// Screenshot
	$buttonScreenshot = '';
	if ($this->p['enable_screenshot'] == 1) {
		$buttonScreenshot = ' <button type="button" class="btn btn-primary phPrintButton" data-id="'.$idA.'">' . Text::_('COM_PHOCAMENU_TAKE_SCREENSHOT') . '</button>';
		PhocamenuRender::renderScreenshotScript($idA, $this->p);
	}


	$html[] = '<a class="ph-inline-task" href="'.$linkPr.'" role="button" class="ph-inline-task" data-bs-toggle="modal" data-bs-target="#'.$idA.'" title="' . Text::_('COM_PHOCAMENU_PREVIEW') . '">'
		//.JHtml::_('image', $this->t['i'].'icon-16-preview.png', JText::_('COM_PHOCAMENU_PREVIEW'), 'style="max-width: none"' )
			. '<div class="ph-cp-item ph-icon-task"><i class="duotone icon-eye-open"></i></div>'
		. '</a>';


	$this->t['modal_bottom_array'][] = HTMLHelper::_(
		'bootstrap.renderModal',
		$idA,
		array(

			'url'    => $linkPr,
			'title'  => Text::_('COM_PHOCAMENU_PREVIEW'),
			'width'  => '',
			'height' => '',
			'modalWidth' => '80',
			'bodyHeight' => '80',
			'footer' => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
				. Text::_('COM_PHOCAMENU_CLOSE') . '</button>'. $buttonScreenshot
		)
	);

	$dhtml = implode("\n", $html);
	echo $dhtml;
	echo '</td>';
	// END PREVIEW

	// Print PDF
	echo '<td align="center" width="1" class="small ph-center ph-tools-td">';
	if ($this->t['linkpdf']['url'] == '') {
		echo '<a class="ph-inline-task" href="#" class="hasTip" title="'.Text::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'">'
		//. JHtml::_('image', $this->t['i'].'icon-16-pdf-dis.png', JText::_('COM_PHOCAMENU_PRINT_PDF'), 'style="max-width: none"' )
				. '<div class="ph-cp-item ph-icon-task disabled"><i class="duotone icon-articles"></i></div>'
		.'</a></td>';
	} else {
		echo '<a class="ph-inline-task" href="'.Route::_($this->t['linkpdf']['url'].'&atid='.(int)$item->id).'" title="'.Text::_('COM_PHOCAMENU_PRINT_PDF').'" '.$this->t['linkpdf']['attribs'].'>'
		//. JHtml::_('image', $this->t['i'].'icon-16-pdf.png', JText::_('COM_PHOCAMENU_PRINT_PDF'), 'style="max-width: none"' )
				. '<div class="ph-cp-item ph-icon-task"><i class="duotone icon-articles"></i></div>'
		.'</a></td>';
	}
}


echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));
echo $r->td($item->id, "small ");

echo $r->endTr();

	}
}
echo $r->endTblBody();

$columns = $this->t['displayitemtools'] == 1 ? '12' : '8';
echo $r->tblFoot($this->pagination->getListFooter(), $columns);
echo $r->endTable();

//echo $this->loadTemplate('batch');

echo '<input type="hidden" name="type" value="'.(int)$this->type['value'].'" />'. "\n";
//echo '<input type="hidden" name="'.$this->type['info']['catid'].'" value="'.(int)$this->type['actualcatid'].'" />'. "\n";
echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();

echo $r->endForm();
echo '</div>'. "\n";
echo $this->t['modal_bottom'];
if (!empty($this->t['modal_bottom_array'])) {
	foreach($this->t['modal_bottom_array'] as $v){
		echo $v;
	}
}
?>
