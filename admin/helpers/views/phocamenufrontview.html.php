<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
jimport('joomla.application.component.view');
class PhocaMenuFrontViewHtml extends HtmlView
{
	public $button;
	protected $params;
	protected $paramsg;
	protected $t;
	protected $data;

	function display($tpl = null) {

		$app						= Factory::getApplication();
		$this->params				= $app->getParams();
		$this->t['printview'] 		= $app->input->get('print', 0, 'int');
		$model 						= $this->getModel('Menu');

		$type						= PhocaMenuHelper::getTypeByView($this->_name);
		$this->data					= $model->getData($type);

		$css						= 'media/com_phocamenu/css/phocamenu.css';
		$cssP						= 'media/com_phocamenu/css/phocamenu-print.css';


		// Params
		$this->t['dateclass']		= $this->params->get( 'date_class', 0 );
		$this->t['customclockcode']	= $this->params->get( 'custom_clock_code', '' );
		$this->t['daydateformat']	= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->t['daydateformat']	= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->t['weekdateformat']	= $this->params->get( 'week_date_format', 'l, d. F Y' );
		$this->t['priceprefix']		= $this->params->get( 'price_prefix', '...' );
		$this->t['displayrss']		= $this->params->get( 'display_rss', 0 );

		// Phoca Gallery Test
		// Check if Phoca Gallery is installed and enabled
		// If it is installed and enabled, check if it is allready used on the site (some of row contains imageid value)
		$phocaGallery = PhocaMenuExtensionHelper::getExtensionInfo('com_phocagallery', 'component');
		if ($phocaGallery != 1) {
			$this->t['phocagallery']	= 0;
		} else if (isset($this->data['imagesum']->sum) && (int)$this->data['imagesum']->sum > 0) {
			$this->t['phocagallery']	= 1;
		} else {
			$this->t['phocagallery']	= 0;
		}

		if ((int)$this->t['printview'] == 1 ) {

			$this->t['phocagallery']		= 0;
			HTMLHelper::stylesheet( $css );
			HTMLHelper::stylesheet( $cssP );

			$this->t['customclockcode'] 	= '';
			$this->paramsg					= array();
			$this->t['button']					= '';

		} else if ($this->t['phocagallery'] == 0) {

			HTMLHelper::stylesheet( $css );
			if ($type != 1) {
				$this->t['customclockcode'] 	= '';
			}
			$this->paramsg						= array();
			$this->t['button']						= '';

		} else {

			$this->t['phocagallery']			= 1;
			HTMLHelper::stylesheet( $css );
			PhocaMenuHelper::includePhocaGallery();

			$this->paramsg['imagedetailwindow']				= $this->params->get( 'image_detail_window', 0 );
			$this->paramsg['detailwindowbackgroundcolor']	= $this->params->get( 'detail_window_background_color', '#ffffff' );
			$this->paramsg['modalboxoverlaycolor']			= $this->params->get( 'modal_box_overlay_color', '#000000' );
			$this->paramsg['modalboxoverlayopacity']		= $this->params->get( 'modal_box_overlay_opacity', 0.3 );
			$this->paramsg['modalboxbordercolor']			= $this->params->get( 'modal_box_border_color', '#6b6b6b' );
			$this->paramsg['modalboxborderwidth']			= $this->params->get( 'modal_box_border_width', 2 );
			$this->paramsg['frontmodalboxwidth']			= $this->params->get( 'front_modal_box_width', 680 );
			$this->paramsg['frontmodalboxheight']			= $this->params->get( 'front_modal_box_height', 560 );

			$this->t['imagesize']							= $this->params->get( 'image_size', 'small' );

			$this->t['button'] = PhocaMenuGallery::getPhocaGalleryBehaviour($this->paramsg);
		}

		//$this->assign('params', $this->params);
		//$this->assign('t', $this->t);

		$this->_prepareDocument();
		parent::display($tpl);

	}

	protected function _prepareDocument() {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= &$app->getParams();
		$title 		= null;




		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('COM_PHOCAMENU_PHOCAMENU'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->get('sitename'));
		} else if ($app->get('sitename_pagetitles', 0)) {
			$title = Text::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);
		}
		$this->document->setTitle($title);

		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

	}
}
?>
