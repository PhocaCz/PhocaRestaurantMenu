<?php
/**
* @version		$Id: router.php 6676 2007-02-19 05:52:03Z Jinx $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
function PhocaMenuBuildRoute(&$query)
{
	$segments = array();
	
	/*if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	};*/
	
/*	if(isset($query['type']))
	{
		$segments[] = $query['type'];
		unset($query['type']);
	};
	*/
	
	/*if(isset($query['tmpl']))
	{
		$segments[] = $query['tmpl'];
		unset($query['tmpl']);
	};*/
	

	
	/*if(isset($query['print']))
	{
		$segments[] = $query['print'];
		unset($query['print']);
	};
	if(isset($query['format']))
	{
		$segments[] = $query['format'];
		unset($query['format']);
	};*/
	

	unset($query['view']);
	return $segments;
}

function PhocaMenuParseRoute($segments)
{		
	$vars = array();
	
	//Get the active menu item

	$app 	= Factory::getApplication('site');
	$menu  = $app->getMenu();
	$item	= &$menu->getActive();

	// Count route segments
	$count = count($segments);

	
	//Handle View and Identifier
	switch($item->query['view'])
	{	
		case 'beveragelist'   :
		{
			$vars['view']	= 'beveragelist';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		
		case 'dailymenu'   :
		{
			$vars['view']	= 'dailymenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		
		case 'foodmenu'   :
		{
			$vars['view']	= 'foodmenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		
		case 'weeklymenu'   :
		{
			$vars['view']	= 'weeklymenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		
		case 'winelist'   :
		{
			$vars['view']	= 'winelist';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		
		case 'breakfastmenu'   :
		{
			$vars['view']	= 'breakfastmenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		case 'lunchmenu'   :
		{
			$vars['view']	= 'lunchmenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
		case 'dinnermenu'   :
		{
			$vars['view']	= 'dinnermenu';
		//	$vars['type']	= $segments[$count-2];
			$vars['tmpl']	= $segments[$count-1];
		} break;
	}

	return $vars;
}
?>