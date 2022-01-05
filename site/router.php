<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_phocamaps
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Factory;


/**
 * Routing class of com_phocamaps
 *
 * @since  3.3
 */


class PhocamenuRouter extends RouterView
{
	protected $noIDs = false;

	/**
	 * Content Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{

        $views = array('beveragelist', 'breakfastmenu', 'dailymenu', 'dinnermenu', 'foodmenu', 'lunchmenu', 'weeklymenu', 'winelist');
        foreach ($views as $k => $v) {
           $m = new RouterViewConfiguration($v);
           $this->registerView($m);
		}


		parent::__construct($app, $menu);

        //$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));



	}
}


function PhocaMenuBuildRoute(&$query)
{

	$app = Factory::getApplication();
	$router = new PhocamenuRouter($app, $app->getMenu());

	return $router->build($query);
}


function PhocaMenuParseRoute($segments)
{


	$app = Factory::getApplication();
	$router = new PhocamenuRouter($app, $app->getMenu());

	return $router->parse($segments);
}

