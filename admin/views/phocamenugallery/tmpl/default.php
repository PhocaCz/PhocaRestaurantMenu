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

use Joomla\CMS\HTML\HTMLHelper;
use Phoca\Text\Text as PhocaText;

$task		= 'phocagalleryimg';


$r 			= $this->r;
$app		= JFactory::getApplication();
$option 	= $app->input->get('option');
$tasks		= $task . 's';
$OPT		= strtoupper($option);
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $option);
$saveOrder	= $listOrder == 'a.ordering';
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo $r->jsJorderTable($listOrder);

echo $r->startFormType($this->t['o'], (int)$this->type['value'], 'phocamenugallery', 'adminForm');
//echo $r->startForm($option, 'phocamenugallery', 'adminForm');
//echo $r->startFilter();
//echo $r->endFilter();

echo $r->startMainContainerNoSubmenu();
/*
//echo $r->startFilterBar();
echo $r->inputFilterSearch($OPT.'_FILTER_SEARCH_LABEL', $OPT.'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo ''. $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder) .'';

//echo $r->startFilterBar(2);
//echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo $r->selectFilterCategory(PhocaGalleryCategory::options($option), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
//echo $r->endFilterBar();

//echo $r->endFilterBar();
*/


echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo $r->startTable('categoryList');

echo $r->startTblHeader();

//echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
//echo $r->thCheck('JGLOBAL_CHECK_ALL');

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);

echo '<th class="ph-image">'.JText::_( $OPT. '_IMAGE' ).'</th>'."\n";
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$OPT.'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-filename">'.JHTML::_('grid.sort',  	$OPT.'_FILENAME', 'a.filename', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-functions">'.JText::_( $OPT. '_FUNCTIONS' ).'</th>'."\n";
echo '<th class="ph-id">'.JHTML::_('grid.sort',  		$OPT.'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();

echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

$originalOrders = array();
$parentsStr 	= "";
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;

//$urlEdit		= 'index.php?option='.$option.'&task='.$task.'.edit&id=';
//$urlTask		= 'index.php?option='.$option.'&task='.$task;
$orderkey   	= array_search($item->id, $this->ordering[$item->catid]);
$ordering		= ($listOrder == 'a.ordering');
/*$canCreate		= $user->authorise('core.create', $option);
$canEdit		= $user->authorise('core.edit', $option);*/
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $option) && $canCheckin;
//$linkEdit 		= JRoute::_( $urlEdit. $item->id );

/*
//$linkCat	= JRoute::_( 'index.php?option=com_phocagallery&task=phocagalleryc.edit&id='.(int) $item->category_id );
$canEditCat	= $user->authorise('core.edit', $option);*/

/*
$iD = $i % 2;
echo "\n\n";
echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" item-id="'.$item->id.'" parents="'.$item->category_id.'" level="0">'. "\n";

echo $r->tdOrder(0, 0, 0);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");*/

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);

$fileOriginal = PhocaGalleryFile::getFileOriginal($item->filename);
if (!JFile::exists($fileOriginal)) {
	$item->fileoriginalexist = 0;
} else {
	$fileThumb 		= PhocaGalleryFileThumbnail::getOrCreateThumbnail($item->filename, false, 0, 0, 0);
	$item->linkthumbnailpath = $fileThumb['thumb_name_s_no_rel'];
	$item->fileoriginalexist = 1;
}

echo $r->tdImage($item, $this->button, 'COM_PHOCAMENU_ENLARGE_IMAGE');
$checkO = '';
if ($item->checked_out) {
	$checkO .= JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $tasks.'.', $canCheckin);
}

$checkO .= $this->escape($item->title);

$checkO .= ' <span class="smallsub">(<span>'.JText::_($OPT.'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small ");

if (isset($item->extid) && $item->extid !='') {
	if (isset($item->exttype) && $item->exttype == 1) {
		echo $r->td(JText::_('COM_PHOCAGALLERY_FACEBOOK_STORED_FILE'));
	} else {
		echo $r->td(JText::_('COM_PHOCAGALLERY_PICASA_STORED_FILE'));
	}
} else {
	echo $r->td($item->filename);
}

echo '<td align="center">'
	.'<a href="#" onclick="if (window.parent) window.parent.'.$this->fce.'('.$item->id.');">'
	. JHTML::_( 'image', 'media/com_phocamenu/images/administrator/icon-16-insert.png', JText::_('COM_PHOCAMENU_INSERT_ID'))
	.'</a></td>';



echo $r->td($item->id, "small ");

echo $r->endTr();

		//}
	}
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();

echo '<input type="hidden" name="tmpl" value="component" />'. "\n";
echo '<input type="hidden" name="field" value="'. $this->field .'" />'. "\n";
echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>
