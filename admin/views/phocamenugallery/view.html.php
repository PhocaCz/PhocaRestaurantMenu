<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Object\CMSObject;
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.file' );

$phocaGallery = PhocaMenuExtensionHelper::getExtensionInfo('com_phocagallery', 'component');

if ($phocaGallery != 1) {
	throw new Exception(Text::_('COM_PHOCAMENU_ERROR') .': '. Text::_('COM_PHOCAMENU_PHOCAGALLERY_NOT_INSTALLED'), 500);
}
if (!class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.file.filethumbnail');
phocagalleryimport('phocagallery.html.category');
class PhocaMenuCpViewPhocaMenuGallery extends HtmlView
{
	protected $items;
	protected $items_thumbnail;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	protected $type;
	protected $field;
	protected $fce;
	public $filterForm;
	public $activeFilters;

	function display($tpl = null) {

	    $this->t			= PhocaMenuUtils::setVars('gallery');
	    $this->r		    = new PhocaMenuRenderAdminViews('gallery');
	    $this->type			= PhocaMenuHelper::getUrlType('gallery');

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->field		= Factory::getApplication()->input->get('field');
		$this->fce 			= 'phocaSelectImage_'.$this->field;


		foreach ($this->items as &$item) {
			$this->ordering[$item->catid][] = $item->id;
		}

		HTMLHelper::stylesheet('media/com_phocagallery/css/administrator/phocagallery.css' );
		$params 	= ComponentHelper::getParams('com_phocagallery');

		// Button
		/*HTMLHelper::_('behavior.modal', 'a.modal_phocagalleryimgs');
		$this->button = new CMSObject();
		$this->button->set('modal', true);
		$this->button->set('methodname', 'modal-button');
		//$this->button->set('link', $link);
		$this->button->set('text', Text::_('COM_PHOCAMENU_DISPLAY_IMAGE_DETAIL'));
		//$this->button->set('name', 'image');
		$this->button->set('modalname', 'modal_phocagalleryimgs');
		$this->button->set('options', "{handler: 'image', size: {x: 200, y: 150}}");*/

		parent::display($tpl);

	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_('COM_PHOCAMENU_TITLE'),
			'a.filename'	=> Text::_('COM_PHOCAMENU_FILENAME'),
			//'a.published' 	=> JText::_('COM_PHOCAMENU_PUBLISHED'),
			//'category_id' 	=> JText::_('COM_PHOCAMENU_CATEGORY'),
			//'language' 		=> JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
