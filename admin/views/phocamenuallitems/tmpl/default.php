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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

use Phoca\Text\Text as PhocaText;

/* When editing an item in all lists we need to asign this item with type of menu and gid (the group id) */
$js ='
Joomla.submitbutton = function(task) {

	var inputName = \'#adminForm input[name="cid[]"]:checked\';
	var checkedValue = jQuery(inputName).val();

	if (checkedValue > 0) {
		var id = "#prmItem" + checkedValue;
		if (task == "phocamenuitem.edit") {
		
			jQuery(\'#adminForm input[name="type"]\').val(jQuery(id).data("type"));
			jQuery(\'#adminForm input[name="gid"]\').val(jQuery(id).data("gid"));
	
		} 
	}
	
	Joomla.submitform(task, document.getElementById("adminForm"));
}
';
Factory::getDocument()->addScriptDeclaration($js);
$r 			= $this->r;
$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
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


//echo $r->startFilterBar();

echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

//echo $r->endFilterBar();

echo $r->startTable('categoryList');

echo $r->startTblHeader();

//echo $r->thOrderingXML('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
//echo $r->thCheck('JGLOBAL_CHECK_ALL');

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);

echo '<th class="ph-quantity">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_QUANTITY', 'a.quantity', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-price">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_PRICE', 'a.price', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-price">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_PRICE2', 'a.price2', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";

echo '<th class="ph-action ph-center">'.Text::_($this->t['l'].'_ACTION').'</th>'."\n";
echo '<th class="ph-category">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_TYPE', 'a.type', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-category">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_DAY', 'day.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-category">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_LIST', 'list.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-category">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_GROUP', 'c.title', $listDirn, $listOrder ).'</th>'."\n";
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
$canDelete		= $user->authorise('core.delete', 'com_phocamenu');
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;


/*
	$backCatidSpec = '';
		if (isset($this->type['actualcatid']) && (int)$this->type['actualcatid'] > 0) {
			$backCatidSpec 	=  '&'.$this->type['info']['catid'].'='.(int)$this->type['actualcatid'];
		}
		$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));
		$this->t['linkpreview'] = Uri::root().'index.php?option=com_phocamenu&view='.$this->type['info']['frontview'].'&tmpl=component&admin=1'.$langSuffix;
		$this->t['linkemail'] 	= 'index.php?option=com_phocamenu&task=phocamenuemail.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		$this->t['linkmultiple']= 'index.php?option=com_phocamenu&task=phocamenumultipleedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		$this->t['linkraw']= 'index.php?option=com_phocamenu&task=phocamenurawedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		//$this->t['linkpdf']	= PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 1);
		$this->t['linkconfig']	= 'index.php?option=com_phocamenu&task=phocamenuconfig.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		//ID must be added
		$this->t['linkedit']	= 'index.php?option=com_phocamenu&task=phocamenuitem.edit&type='.$this->type['value']. $backCatidSpec;*/

$linkEdit = '';
if (isset($item->type) && $item->type > 0 && $item->type < 9) {
	$typeInfo = PhocaMenuHelper::getTypeInfo('item', $item->type);
	$catidInfo = '';
	if (isset($typeInfo['catid'])) {
		$catidInfo = $typeInfo['catid'];
	}
	$linkEdit 		= 'index.php?option=com_phocamenu&task=phocamenuitem.edit&type='.$item->type . '&'.$catidInfo.'='.(int)$item->catid.'&id='.(int)$item->id . '&typeback=phocamenuallitems';

	$linkEdit 		= Route::_( $linkEdit);
}

$linkView		= Route::_( 'index.php?option='.$this->t['o'].'&view='.$this->t['c'].'items&type='.(int)$this->type['value'].'&gid='.$item->id );
$linkRemove 	= 'javascript:void(0);';
$onClickRemove 	= 'javascript:if (confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEMS').'\')){'
				 .' return Joomla.listItemTask(\'cb'. $i .'\',\''.$this->t['tasks'].'.delete\');'
				 .'}';


//$iD = $i % 2;
//echo "\n\n";
//echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" item-id="'.$item->id.'" parents="'.$item->category_id.'" level="0">'. "\n";
//echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" >'. "\n";

//echo $r->tdOrder($canChange, $saveOrder, $orderkey, $item->ordering);
//echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);

/*
$qtO = '';
if ($canCreate || $canEdit) {
	$qtO .= '<a href="'. Route::_($linkEdit).'">'. $this->escape($item->quantity).'</a>';
} else {
	$qtO .=  $this->escape($item->quantity);
}
echo $r->td($qtO, "small ");*/

echo $r->tdEip('item:quantity:'.(int)$item->id, $this->escape($item->quantity ), array('classeip' => 'ph-editinplace-text ph-eip-text ph-eip-quantity'));

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
	$o[] = '<span class="ph-editinplace-text ph-eip-text ph-eip-title" id="item' . ':' .'title'.':'.(int)$item->id . '">' . PhocaText::filterValue($item->title, 'text') . '</span>';
} else {
	$o[] = $this->escape($item->title);
}

echo $r->td(implode("\n", $o), 'small');


/*
$priceO = $price2O = '';
if ($canCreate || $canEdit) {
	$priceO .= '<a href="'. Route::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($this->escape($item->price )).'</a>';
	$price2O .= '<a href="'. Route::_($linkEdit).'">'. PhocaMenuHelper::getPriceFormat ($this->escape($item->price2 )).'</a>';
} else {
	$priceO .=  PhocaMenuHelper::getPriceFormat ($this->escape($item->price ));
	$price2O .=  PhocaMenuHelper::getPriceFormat ($this->escape($item->price2 ));
}
echo $r->td($priceO, "small ");
echo $r->td($price2O, "small ");*/

echo $r->tdEip('item:price:'.(int)$item->id, $this->escape($item->price ));
echo $r->tdEip('item:price:'.(int)$item->id, $this->escape($item->price2 ));



echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small  ph-center");

/*
$vO = '<a href="'. $linkView.'" title="'. Text::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS').'">'
	. HTMLHelper::_('image', $this->t['i'].'icon-16-item.png', Text::_('COM_PHOCAMENU_VIEW_GROUP_ITEMS') )
	.'</a>';
echo $r->td($vO, "small  ph-center");
	*/
/*
$vD = '';
if ($canDelete) {
$vD = '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. Text::_('COM_PHOCAMENU_DELETE').'"'
	.' onclick="return confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\');">'
	//. JHtml::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
		. '<div class="ph-icon-task"><i class="duotone icon-purge"></i></div>'
	.'</a>';
}

echo $r->td($vD, "small  ph-center");*/

$action = '<div class="ph-action-inline-icon-box">';
if ($canCreate || $canEdit) {
	$action .= '<a href="' . Route::_($linkEdit) . '" title="'. Text::_('COM_PHOCAMENU_EDIT').'"><span class="ph-icon-task"><i class="duotone icon-pencil"></i></span></a>';
}

if ($canDelete) {
$action .= '<a class="ph-action-inline-icon-box ph-inline-task" href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. Text::_('COM_PHOCAMENU_DELETE').'"'
	.' onclick="return confirm(\''.Text::_('COM_PHOCAMENU_WARNING_DELETE_ITEM').'\');">'
	//. JHtml::_('image', $this->t['i'].'icon-16-trash.png', JText::_('COM_PHOCAMENU_DELETE') )
		. '<span class="ph-cp-item ph-icon-task"><i class="duotone icon-purge"></i></span>'
	.'</a>';
}
$action .= '</div>';
echo $r->td($action, "small  ph-center");


echo $r->td(PhocaMenuHelper::getTitleByType((int)$item->type, 1), "small  ph-center");
echo $r->td($this->escape($item->day_title), "small  ph-center");
echo $r->td($this->escape($item->list_title), "small  ph-center");

echo $r->td($this->escape($item->category_title), "small  ph-center");
echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));


// Hidden form for each item (no inputs so the post does not post them)
$hiddenForm = '<div style="display:hidden" id="prmItem'.(int)$item->id.'" data-type="'.$item->type.'" data-gid="'.$item->catid.'"></div>';
echo $r->td($item->id . $hiddenForm, "small ");

echo $r->endTr();

	}
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 18);
echo $r->endTable();

echo $this->loadTemplate('batch');
echo $this->loadTemplate('new');

echo '<input type="hidden" name="type" value="-1" />'. "\n";
echo '<input type="hidden" name="gid" value="" />'. "\n";
echo '<input type="hidden" name="typeback" value="phocamenuallitems" />'. "\n";

//echo '<input type="hidden" name="'.$this->type['info']['catid'].'" value="'.(int)$this->type['actualcatid'].'" />'. "\n";
echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();


echo $r->endForm();
echo '</div>'. "\n";
?>
