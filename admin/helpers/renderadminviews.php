<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Session\Session;
use Phoca\Render\Adminviews;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


class PhocaMenuRenderAdminViews extends AdminViews
{
	public $view        = '';
    public $viewtype    = 1;
    public $option      = '';
    public $optionLang  = '';
    public $tmpl        = '';
    public $compatible  = false;
    public $sidebar     = true;
    protected $document	= false;

	public function __construct(){

		parent::__construct();

		$this->loadMedia();

	}

	public function loadMedia() {
		$urlEip = JURI::base(true).'/index.php?option='.$this->option.'&task='.str_replace('com_', '', $this->option).'editinplace.editinplacetext&format=json&'. Session::getFormToken().'=1';

		Joomla\CMS\HTML\HTMLHelper::_('jquery.framework', false);
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.autogrow.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.autogrowtextarea.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.phocajeditable.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.jeditable.masked.min.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/'.$this->option.'/js/jeditable/jquery.maskedinput.min.js', array('version' => 'auto'));
		HTMLHelper::_('stylesheet', 'media/'.$this->option.'/js/jeditable/phocajeditable.css', array('version' => 'auto'));


		$this->document->addScriptOptions('phLang', array(
			'PHOCA_CLICK_TO_EDIT' => JText::_('COM_PHOCAMENU_CLICK_TO_EDIT'),
			'PHOCA_CANCEL' => JText::_('COM_PHOCAMENU_CANCEL'),
			'PHOCA_SUBMIT' => JText::_('COM_PHOCAMENU_SUBMIT'),
			'PHOCA_PLEASE_RELOAD_PAGE_TO_SEE_UPDATED_INFORMATION' => JText::_('COM_PHOCAMENU_PLEASE_RELOAD_PAGE_TO_SEE_UPDATED_INFORMATION')
		));

		$params = JComponentHelper::getParams($this->option);
		// PHOCAMENU Specific
		//$dateFormat	= 'DATE_FORMAT_LC';
		$dateFormat	= $params->get( 'day_date_format', 'DATE_FORMAT_LC' );


		$this->document->addScriptOptions('phVars', array('token' => Session::getFormToken(), 'urleditinplace' => $urlEip, 'dateformat' => $dateFormat));

	}


	/* TODO:
	* CHANGE PATHS
	* SET NEW PARAM IN PG: '/media/com_phocagallery/images/administrator/'
	*/
	public function tdImage($item, $button, $txtE, $class = '', $avatarAbs = '', $avatarRel = '') {
		$o = '<td class="'.$class.'">'. "\n";
		$o .= '<div class="phocagallery-box-file">'. "\n"
			.' <center>'. "\n"
			.'  <div class="phocagallery-box-file-first">'. "\n"
			.'   <div class="phocagallery-box-file-second">'. "\n"
			.'    <div class="phocagallery-box-file-third">'. "\n"
			.'     <center>'. "\n";

		if ($avatarAbs != '' && $avatarRel != '') {
			// AVATAR
			if (JFile::exists($avatarAbs.$item->avatar)){
				$o .= '<a class="'. $button->modalname.'"'
				.' title="'. $button->text.'"'
				.' href="'.JURI::root().$avatarRel.$item->avatar.'" '
				.' rel="'. $button->options.'" >'
				.'<img src="'.JURI::root().$avatarRel.$item->avatar.'?imagesid='.md5(uniqid(time())).'" alt="'.Text::_($txtE).'" />'
				.'</a>';
			} else {
				$o .= JHTML::_( 'image', '/media/com_phocagallery/images/administrator/phoca_thumb_s_no_image.gif', '');
			}
		} else {
			// PICASA
			if (isset($item->extid) && $item->extid !='') {

				$resW				= explode(',', $item->extw);
				$resH				= explode(',', $item->exth);
				$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($resW[2], $resH[2], 50, 50);
				$imgLink			= $item->extl;

				$o .= '<a class="'. $button->modalname.'" title="'.$button->text.'" href="'. $imgLink .'" rel="'. $button->options.'" >'
				. '<img src="'.$item->exts.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="'.Text::_($txtE).'" />'
				.'</a>'. "\n";
			} else if (isset ($item->fileoriginalexist) && $item->fileoriginalexist == 1) {

				$imageRes			= PhocaGalleryImage::getRealImageSize($item->filename, 'small');
				$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 50, 50);
				$imgLink			= PhocaGalleryFileThumbnail::getThumbnailName($item->filename, 'large');

				$o .= '<a class="'. $button->modalname.'" title="'. $button->text.'" href="'. JURI::root(). $imgLink->rel.'" rel="'. $button->options.'" >'
				. '<img src="'.JURI::root().$item->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="'.Text::_($txtE).'" />'
				.'</a>'. "\n";
			} else {
				$o .= JHTML::_( 'image', 'media/com_phocagallery/images/administrator/phoca_thumb_s_no_image.gif', '');
			}
		}
		$o .= '     </center>'. "\n"
			.'    </div>'. "\n"
			.'   </div>'. "\n"
			.'  </div>'. "\n"
			.' </center>'. "\n"
			.'</div>'. "\n";
		$o .=  '</td>'. "\n";
		return $o;
	}

	public function startFormType($option, $type, $view, $id = 'adminForm', $name = 'adminForm') {
		return '<div id="'.$view.'"><form action="'.JRoute::_('index.php?option='.$option.'&view='.$view.'&type='.(int)$type).'" method="post" name="'.$name.'" id="'.$id.'">'."\n";
	}
}
?>
