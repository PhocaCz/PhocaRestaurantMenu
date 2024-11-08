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
defined('_JEXEC') or die( 'Restricted access' );
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Document\Feed\FeedItem;
jimport( 'joomla.application.component.view');

class PhocaMenuViewDailyMenu extends HtmlView
{
    public $t;
    public $p;

	function display($tpl = null)
	{
		$app = Factory::getApplication();

		$doc     	= Factory::getDocument();
		$this->params 	= $app->getParams();
		$view		= 'dailymenu';
		$model 		= $this->getModel('Menu');
		$data		= $model->getData(1);

		// Specific items to correct links
		$uri = Uri::getInstance();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		// Params
		$this->p['dateclass']			= $this->params->get( 'date_class', 0 );
		//$this->p['customclockcode']	= $this->params->get( 'custom_clock_code', '' );
		$this->p['daydateformat']		= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->p['priceprefix']		= $this->params->get( 'price_prefix', '...' );
		$this->p['displayrss']			= $this->params->get( 'display_rss', 0 );

        $this->p['feed_date_format']                     = $this->params->get( 'feed_date_format', 'DATE_FORMAT_LC' );
        $this->p['feed_root']                     = $this->params->get( 'feed_root', '' );
        $this->p['feed_date']                     = $this->params->get( 'feed_date', '' );
        $this->p['feed_group']                     = $this->params->get( 'feed_group', '' );
        $this->p['feed_item']                     = $this->params->get( 'feed_item', '' );
        $this->p['feed_item_type']                = $this->params->get( 'feed_item_type', '' );
        $this->p['feed_item_title']			    = $this->params->get( 'feed_item_title', '' );
        $this->p['feed_item_price']			    = $this->params->get( 'feed_item_price', '' );
        $this->p['feed_item_additional_info']     = $this->params->get( 'feed_item_additional_info', '' );

        $this->p['feed_note']     = $this->params->get( 'feed_note', '' );
        $this->p['feed_note_type']     = $this->params->get( 'feed_note_type', '' );


		// Phoca Gallery
		$this->p['phocagallery']		= 0;
		$this->p['customclockcode'] 	= '';
		$this->paramsG					= array();
		$button						= '';

		$title = $this->escape( $data['config']->header );
		$title = html_entity_decode( $title );

		$this->t['output'] 	=  PhocaMenuRenderViews::renderDailyMenu($data, $this->p, $this->params,$this->paramsG, 7);


		parent::display('feed');
	}
}
