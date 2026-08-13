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

class PhocaMenuViewWeeklyMenu extends HtmlView
{
	function display()
	{
		//global $app;
		$app = Factory::getApplication();

		$doc     	= Factory::getDocument();
		$params 	= $app->getParams();
		$view		= 'weeklymenu';
		$model 		= $this->getModel('Menu');
		$data		= $model->getData(2);

		// Specific items to correct links
		$uri = Uri::getInstance();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		// Params
		$tmpl['dateclass']			= $params->get( 'date_class', 0 );
		//$tmpl['customclockcode']	= $params->get( 'custom_clock_code', '' );
		$tmpl['daydateformat']		= $params->get( 'day_date_format', 'l, d. F Y' );
		$tmpl['weekdateformat']		= $params->get( 'week_date_format', 'l, d. F Y' );
		$tmpl['priceprefix']		= $params->get( 'price_prefix', '...' );
		$tmpl['displayrss']			= $params->get( 'display_rss', 0 );

		// Phoca Gallery
		$tmpl['phocagallery']		= 0;
		$tmpl['customclockcode'] 	= '';
		$paramsG					= array();
		$button						= '';

		$title = $this->escape( $data['config']->header );
		$title = html_entity_decode( $title );

		$output 	=  PhocaMenuRenderViews::renderWeeklyMenu($data, $tmpl, $params,$paramsG, 0);
		$doc->link 	= Route::_('index.php?option=com_phocamenu&view='.$view.'&Itemid='.Factory::getApplication()->getInput()->get('Itemid', 0, '', 'int'));

		$doc->image->url	= Uri::base() . 'components/com_phocamenu/assets/images/icon-128-dm.png';
		$doc->image->title = $title;
		$doc->image->link	= $url . $doc->link;
		$doc->image->width	= 128;
		$doc->image->height = 128;
		$doc->image->description = '';

		$title = $this->escape( $data['config']->header );
		$title = html_entity_decode( $title );

		$description	= $output;
		if ($tmpl['displayrss']	== 2) {
			$description	= str_replace('<div class="pm-item">', ': &nbsp;<div class="pm-item">', $description);
			$description	= str_replace('<div class="pm-group">', '&nbsp; | &nbsp;<div class="pm-item">', $description);
			$description	= str_replace('<div class="pm-date">', '&nbsp; | &nbsp;<div class="pm-date">', $description);
			$description	= str_replace('<div class="pm-footer">', '&nbsp; | &nbsp;<div class="pm-footer">', $description);
			$description	= str_replace('<div class="pm-date-sub">', '&nbsp; *** &nbsp;<div class="pm-footer">', $description);
			$description	= str_replace('</tr><tr>', "</tr>,<tr> &nbsp;", $description);
			$description	= strip_tags($description);
		}

		$description 	= '<div><img src="'.$doc->image->url.'" alt="" title="" /></div>' . $description;

		// load individual item creator class
		$item = new FeedItem();
		$item->title 		= $title;
		$item->link 		= $doc->link;
		$item->description 	= $description;
		$item->date			= $data['config']->date_from;

		$item->date 		= str_replace('00:00:00', '01:01:01', $item->date);
		$item->guid 		= $url . Route::_('index.php?option=com_phocamenu&view='.$view.'&date='.strtotime($item->date).'&Itemid='.Factory::getApplication()->getInput()->get('Itemid', 0, '', 'int'));
		//$item->category   	= '';
		//$item->author		= '';
		//$item->authorEmail 	= '';

		$doc->addItem( $item );
	}
}
