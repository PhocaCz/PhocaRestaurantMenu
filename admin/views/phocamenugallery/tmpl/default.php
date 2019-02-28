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

$task		= 'phocagalleryimg';

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$r 			=  new PhocaMenuRenderAdminViews();
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
if ($saveOrder) {
	//$saveOrderingUrl = 'index.php?option='.$option.'&task='.$tasks.'.saveOrderAjax&tmpl=component';
	//JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();


echo $r->jsJorderTable($listOrder);


echo $r->startForm($option, 'phocamenugallery', 'adminForm');
//echo $r->startFilter();
//echo $r->endFilter();

echo $r->startMainContainer(12);
echo $r->startFilterBar();
echo $r->inputFilterSearch($OPT.'_FILTER_SEARCH_LABEL', $OPT.'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo ''. $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder) .'';

echo $r->startFilterBar(2);
//echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo $r->selectFilterCategory(PhocaGalleryCategory::options($option), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
echo $r->endFilterBar();

echo $r->endFilterBar();		

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
echo $r->thCheck('JGLOBAL_CHECK_ALL');
echo '<th class="ph-image">'.JText::_( $OPT. '_IMAGE' ).'</th>'."\n";
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$OPT.'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-filename">'.JHTML::_('grid.sort',  	$OPT.'_FILENAME', 'a.filename', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-functions">'.JText::_( $OPT. '_FUNCTIONS' ).'</th>'."\n";
echo '<th class="ph-id">'.JHTML::_('grid.sort',  		$OPT.'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
			
echo '<tbody>'. "\n";

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


$iD = $i % 2;
echo "\n\n";
echo '<tr class="row'.$iD.'" sortable-group-id="'.$item->category_id.'" item-id="'.$item->id.'" parents="'.$item->category_id.'" level="0">'. "\n";

echo $r->tdOrder(0, 0, 0);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");

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

echo '</tr>'. "\n";
						
		//}
	}
}
echo '</tbody>'. "\n";

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();

echo '<input type="hidden" name="tmpl" value="component" />'. "\n";
echo '<input type="hidden" name="field" value="'. $this->field .'" />'. "\n";
echo $r->formInputs($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>


<?php
/*
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<form action="<?php echo JRoute::_('index.php?option=com_phocamenu&view=phocamenugallery'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_PHOCAMENU_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
		

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', PhocaGalleryCategory::options('com_phocagallery'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
			
			<?php /*
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
					
			 ?>
					<th class="image" width="70" align="center"><?php echo JText::_( 'COM_PHOCAMENU_IMAGE' ); ?></th>
					<th class="title" width="80%"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="20%" nowrap="nowrap"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_FILENAME', 'a.filename',$listDirn, $listOrder ); ?>
					</th>
					

					<th width="10%" nowrap="nowrap"><?php echo JText::_('COM_PHOCAMENU_FUNCTIONS'); ?>
					</th>
					
					<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort',  'COM_PHOCAMENU_ID', 'a.id',$listDirn, $listOrder ); ?>
					</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
					
				
echo '<tr class="row'. $i % 2 .'">';
					
//echo '<td class="center">'. JHtml::_('grid.id', $i, $item->id) . '</td>';

// - - - - - - - - - -
// Image
$file_original = PhocaGalleryFile::getFileOriginal($item->filename);
if (!JFile::exists($file_original)) {
	$item->fileoriginalexist = 0;
} else { 
	$item->fileoriginalexist = 1;
	$fileThumb 					= PhocaGalleryFileThumbnail::getOrCreateThumbnail($item->filename, '', 0,0,0);
	$item->linkthumbnailpath 	= $fileThumb['thumb_name_s_no_rel'];		
}


echo '<td>';
echo '<div class="phocagallery-box-file">'
    .' <center>'
	.'  <div class="phocagallery-box-file-first">'
	.'   <div class="phocagallery-box-file-second">'
	.'    <div class="phocagallery-box-file-third">'
	.'     <center>';
// PICASA
if (isset($item->extid) && $item->extid !='') {									
	
	$resW				= explode(',', $item->extw);
	$resH				= explode(',', $item->exth);
	$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($resW[2], $resH[2], 50, 50);
	$imgLink			= $item->extl;
	
	echo '<a href="#" onclick="if (window.parent) window.parent.'.$this->fce.'('.$item->id.');" >'

	. '<img src="'.JURI::root().$item->exts.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="'.$item->title.'" />'
	.'</a>';
} else if (isset ($item->fileoriginalexist) && $item->fileoriginalexist == 1) {
	
	$imageRes			= PhocaGalleryImage::getRealImageSize($item->filename, 'small');
	$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 50, 50);
	$imgLink			= PhocaGalleryFileThumbnail::getThumbnailName($item->filename, 'large');
	

	echo '<a href="#" onclick="if (window.parent) window.parent.'.$this->fce.'('.$item->id.');" >'
	. '<img src="'.JURI::root().$item->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="'.$item->title.'" />'
	.'</a>';
} else {
	echo JHTML::_( 'image', 'administrator/components/com_phocagallery/assets/images/phoca_thumb_s_no_image.gif', '');
}
echo '     </center>'
    .'    </div>'
	.'   </div>'
	.'  </div>'
	.' </center>'
	.'</div>';
echo '</td>';
// - - - - - - - - - -


echo '<td>'; 
echo  $this->escape($item->title);
echo '</td>';
				
if (isset($item->extid) && $item->extid !='') {
	echo '<td align="center">'.JText::_('COM_PHOCAMENU_PICASA_STORED_FILE').'</td>';
} else {
	echo '<td>'. $item->filename .'</td>';
}

echo '<td align="center">'
	.'<a href="#" onclick="if (window.parent) window.parent.'.$this->fce.'('.$item->id.');">'
	. JHTML::_( 'image', 'administrator/components/com_phocamenu/assets/images/icon-16-insert.png', JText::_('COM_PHOCAMENU_INSERT_ID'))
	.'</a></td>';

echo '<td align="center">'. $item->id .'</td>';

echo '</tr>';

		}
	}
echo '</tbody>';		
?>
			</tbody>
			
			<tfoot>
				<tr>
					<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>

<input type="hidden" name="field" value="<?php echo $this->field; ?>" />
<input type="hidden" name="t" value="component" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHtml::_('form.token'); ?>
</form> */
?>