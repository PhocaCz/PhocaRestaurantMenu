<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 

class PhocaMenuHelper
{
	
	/*
	 * Used in controllers and modules
	 */
	public static function getUrlApend($typeview = 'group', $returnBack = 0 ) {
		
		$app = JFactory::getApplication();
		//$typeValue	= JRequest::getVar('type', 0, '', 'int');
		$typeValue	= $app->input->get('type', 0, 'int');
		
		//$typeviewBack	= JRequest::getVar('typeback', null, '', 'STRING', JREQUEST_NOTRIM);
		$typeviewBack	= $app->input->get('typeback', '', 'string');
		
		
		if ($typeview == 'config' || $typeview == 'email' || $typeview == 'multipleedit') {
			$typeInfo	= PhocaMenuHelper::getTypeInfo($typeviewBack, $typeValue);
		} else {
			$typeInfo	= PhocaMenuHelper::getTypeInfo($typeview, $typeValue);
		}	

		//$catid 		= JRequest::getVar( $typeInfo['catid'], 0, '', 'int' );
		$catid 	= $app->input->get($typeInfo['catid'], 0, 'int');
		// Catid changed in Edit mode by JForm
		//$jForm	= JRequest::getVar('jform', 0, '', 'array');
		
		$jForm	= $app->input->get('jform', array(0), 'array');

		
		if(isset($jForm['catid']) && (int)$jForm['catid'] > 0) {
			$catid = (int)$jForm['catid'];
		}

		
		
		if ($typeview == 'config' || $typeview == 'email' || $typeview == 'multipleedit') {
			
			
			if ($returnBack == 1) {
				// We are going back from Config
				$appendUrl	= '&view=phocamenu'.$typeInfo['view'].'s'
						 .'&type='.(int)$typeValue.'&'.$typeInfo['catid'].'='. (int)$catid;
			} else {
				// We are going to be in Config, Email or Multiple
				
				/*$adminTool 	= JRequest::getVar( 'admintool', 0, '', 'int');
				$atid		= JRequest::getVar( 'atid', 0, '', 'int' );
				$alang		= JRequest::getVar( 'alang', '', '', 'string' );
				$adminLang	= JRequest::getVar( 'adminlang', 0, '', 'int' );*/
				
				$adminTool 	= $app->input->get( 'admintool', 0, 'int');
				$atid		= $app->input->get( 'atid', 0, 'int' );
				$alang		= $app->input->get( 'alang', '', 'string' );
				$adminLang	= $app->input->get( 'adminlang', 0, 'int' );
				
				//$lang		= self::getLangAdmin(1);
				$suffix 	= '';
				if ((int)$adminTool > 0) 	{$suffix .= '&admintool='.(int)$adminTool;}
				if ((int)$atid > 0) 		{$suffix .= '&atid='.(int)$atid;}
				if ((string)$alang != '')	{$suffix .= '&lang='.(string)$alang;}
				if ((int)$adminLang > 0) 	{$suffix .= '&adminlang='.(int)$adminLang;}
				
				$appendUrl	= '&type='.(int)$typeValue.'&'.$typeInfo['catid'].'='. (int)$catid
						 .'&typeback='.(string)$typeviewBack.$suffix;
			}
		} else {
			// Standard append except Config
			$appendUrl	= '&type='.(int)$typeValue.'&'.$typeInfo['catid'].'='. (int)$catid;
		}
		
		return $appendUrl;
	
	}
	
	/*
	 * Set language for administration
	 * Some views (multiple edit, email, preview - for item) - for them default language view must be set:
	 * sometimes by GET
	 * sometimes by Filter (but by redirects we allways get GET)
	 */
	 /*
	function getLangAdmin ($reverse = 0) {
		$langG		= JRequest::getVar('lang', 'all', 'GET', 'string');//link for specific items
		//$langP		= JRequest::getVar('language', '', 'POST', 'string');
		$langF		= JRequest::getVar('filter_language', '', 'POST', 'string');//hidden field in default.php

		if ($langF != '') {
			if ($reverse == 1) {
			
				if ($langF == '*') {
					return 'all';
				} else {
					jimport('joomla.language.helper');
					$code = JLanguageHelper::getLanguages('lang_code');
					if (isset($code[$langF]->sef)) {
							$langCode = $code[$langF]->sef;
					}
					return $langCode;
				}
			}
			return $langF;
		} else if ($langG != '') {
			
			if ($reverse == 1) {
				return $langG;
			}
		
			$langSef = '';
			if ($langG == 'all') {
				return '*';
			} else {
				jimport('joomla.language.helper');
				$sef = JLanguageHelper::getLanguages('sef');
				if (isset($sef[$langG]->lang_code)) {
					$langSef = $sef[$langG]->lang_code;
				}
				return $langSef;
			}
		}
	
		return '';
	}
	 */
	 
	public static function getLangCode($langSef) {
		$langCode = '';
		if ($langSef != '') {
			jimport('joomla.language.helper');
			$sef = JLanguageHelper::getLanguages('sef');
			if (isset($sef[$langSef]->lang_code)) {
				$langCode = $sef[$langSef]->lang_code;
			}
		}
		return $langCode;
	}
	
	public static function getLangSef($langCode) {
		$langSef = '';
		if ($langCode != '') {
			jimport('joomla.language.helper');
			$code = JLanguageHelper::getLanguages('lang_code');
			if (isset($code[$langCode]->sef)) {
				$langSef = $code[$langCode]->sef;
			}
		}
		return $langSef;
	}
	
	public static function getLangSuffix($filterLang = '', $admin = 1) {
		
		if ($filterLang != '') {
			$langSef = self::getLangSef($filterLang);
			if ($langSef != '') {
				if ($admin == 1) {
					return '&adminlang=1&alang='.$langSef;
				} else {
					return '&lang='.$langSef;
				}
			}
		}
		if ($admin == 1) {
			return '&adminlang=1';
		} else {
			return '';
		}
	}
	
	
	/*
	 * Used in views
	 */
	public static function getUrlType($typeview = 'group') {
		
		$app = JFactory::getApplication();
		$type	= array();
	
		//$type['value']		= JRequest::getVar('type', 0, '', 'int');
		$type['value']		= $app->input->get('type', 0, 'int');
		$type['info']		= PhocaMenuHelper::getTypeInfo($typeview, $type['value']);
		//$type['valuecatid'] = JRequest::getVar( $type['info']['catid'], 0, '', 'int' );
		$catName			= $type['info']['catid'];
		$type['valuecatid'] = $app->input->get( $catName, 0, 'int' );

		if (isset($_POST[$catName]) && $_POST[$catName] > 0) {
			$type['method'] = 'post';
		} else if (isset($_GET[$catName]) && $_GET[$catName] > 0) {
			$type['method'] = 'get';
		} else {
			$type['method'] = false;
		}
		
		// Catid changed in Edit mode by JForm
		//$jForm	= JRequest::getVar('jform', 0, '', 'array');
		//if(isset($jForm['catid']) && (int)$jForm['catid'] > 0) {
		//	$type['valuecatid'] = (int)$jForm['catid'];
		//}
		
		return $type;
	
	}
	
	public static function getTypeByView($typeview = 'dailymenu') {
		
		$type = 1;
		switch ($typeview) {
			case 'dailymenu': 		$type = 1;	break;
			case 'weeklymenu':		$type = 2;	break;
			case 'foodmenu':		$type = 3;	break;
			case 'beveragelist':	$type = 4; 	break;
			case 'winelist':		$type = 5;	break;
			case 'breakfastmenu':	$type = 6;	break;
			case 'lunchmenu':		$type = 7;	break;
			case 'dinnermenu':		$type = 8;	break;
			default:				$type = 1;	break;
		}
		return $type;
	}
	
	public static function getTypeByCategory($catId = 0, $table = 'Group') {
		if ((int)$catId > 0) {
			$db					= JFactory::getDBO();
			
			$tableS = 'group';
			switch ($table) {
				case 'Day':
					$tableS = 'day';
				break;
				case 'List':
					$tableS = 'list';
				break;
				case 'Group':
				default:
					$tableS = 'group';
				break;
			
			}
			$query = 'SELECT a.type'
				. ' FROM #__phocamenu_'.$tableS.' AS a'
				. ' WHERE a.id = '.(int)$catId
				. ' LIMIT 1';

			$db->setQuery($query);
			$catType = $db->loadObject();
				
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
			if (isset($catType->type) && (int)$catType->type > 0) {
				return (int)$catType->type;
			}
		}
		// Default type is set to 1
		return 1;
	}
	
	// Quick function for selects
	public static function getTitleByType($type) {
		
		$title = '';
		switch ($type) {
			case 1: $title =JText::_('COM_PHOCAMENU_DAILY_MENU');		break;
			case 2: $title =JText::_('COM_PHOCAMENU_WEEKLY_MENU');		break;
			case 3: $title =JText::_('COM_PHOCAMENU_BILL_OF_FARE');		break;
			case 4: $title =JText::_('COM_PHOCAMENU_BEVERAGE_LIST');	break;
			case 5: $title =JText::_('COM_PHOCAMENU_WINE_LIST');		break;
			case 6: $title =JText::_('COM_PHOCAMENU_BREAKFAST_MENU');	break;
			case 7: $title =JText::_('COM_PHOCAMENU_LUNCH_MENU');		break;
			case 8: $title =JText::_('COM_PHOCAMENU_DINNER_MENU');		break;
			default:				'';	break;
		}
		return $title;
	}
	
	
	public static function getTypeTable($type) {
		switch((int)$type) {			
			case 2:
				$table = '#__phocamenu_day';
			break;
			case 3:
			case 4:
			case 5:
				$table = '#__phocamenu_list';
			break;
			default:
				$table = '';
			break;
		}
		return $table;
	}
	
	
	public static function getTypeInfo($typeview, $type = 0) {
		
		$app				= JFactory::getApplication();
		$typeInfo			= array();
		$typeInfo['type']	= $type;
		$typeInfo['view']	= $typeview;
		
		if ($typeview == '') {
			// Debug Info
			$view		= $app->input->get('view', '', 'string');
			$wTxt	= $view != '' ? JText::_('COM_PHOCAMENU_VIEW').': '. $view : '';
			$twTxt	= JText::_('COM_PHOCAMENU_TYPE').': '. $type;
			$errTxt	= ' ( '.$wTxt. ' ' .$twTxt.' ) ';
			$app->redirect(JRoute::_('index.php?option=com_phocamenu', false), JText::_('COM_PHOCAMENU_ERROR_NO_MENU_TYPE_VIEW_FOUND') . $errTxt, 'error');
		}
		
		
		switch ($type) {
			
			case 1:
				switch($typeview) {
					case 'group':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_CONTROL_PANEL';
						$typeInfo['text']			= JText::_('COM_PHOCAMENU_DAILY_MENU') . ' ' . JText::_('COM_PHOCAMENU_GROUP');
					break;
					
					case 'item':
						$typeInfo['backlink']		= '&view=phocamenugroups&type=1';
						$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_GROUPS';
						$typeInfo['text']			= JText::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . JText::_('COM_PHOCAMENU_ITEM');
					break;
					
					case 'config':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= JText::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . JText::_('COM_PHOCAMENU_SETTINGS');
					break;
					
					case 'email':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= JText::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . JText::_('COM_PHOCAMENU_SEND_EMAIL');
					break;
					case 'multipleedit':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= JText::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . JText::_('COM_PHOCAMENU_MULTIPLE_EDIT');
					break;
				}
				$typeInfo['catid']			= 'gid';
				$typeInfo['pref']			= 'dm';
				$typeInfo['frontview']		= 'dailymenu';
				$typeInfo['render']			= 'renderDailyMenu';
				$typeInfo['title']			= JText::_('COM_PHOCAMENU_DAILY_MENU');
			break;
			
		}
		return $typeInfo;
	}
	
	public static function displayToolbarTools($displayToolbars, $type) {
	
		switch ($displayToolbars) {
			case 1:
				return true;
			break;
			case 2:
				if ((int)$type < 2 || (int)$type > 5) {
					return true;
				} else {
					return false;
				}
			break;
			case 0:
			default:
				return false;
			break;
		
		}
		return false;
	}
	
	
	public static function getBreadcrumbs($current, $backLink = '', $backLinkText = '', $backLinkUp = '', $backLinkTextUp = '') {
		
		$arrowImg	= JHTML::_('image', 'media/com_phocamenu/images/administrator/icon-arrow.png', '-' );
		$back 		= '';
		$backUp		= '';
		
		if ($backLinkUp != '') {
			$backUp	= '<span class="arrow"> '.$arrowImg.' </span>'
					 .'<a href="index.php?option=com_phocamenu'.$backLinkUp.'">'. $backLinkTextUp.'</a>';
		}
		
		if ($backLink != '') {
			$back	= '<span class="arrow"> '.$arrowImg.' </span>'
					 .'<a href="index.php?option=com_phocamenu'.$backLink.'">'. $backLinkText.'</a>';
		}
		
		$breadcrumbs = '<div class="breadcrumb" id="phocamenubreadcrumb">'
					  .'<a href="index.php?option=com_phocamenu">'. JText::_('COM_PHOCAMENU_CONTROL_PANEL').'</a>'
					  . $backUp . $back
					  .'<span class="arrow"> '.$arrowImg.' </span>'
					  . $current
					  .'</div>';
		return $breadcrumbs;
	}
	
	public static function getPriceFormat($price, $params = array()) {
		
		// Administration (no frontend)
		if (empty($params)) {
			$params = JComponentHelper::getParams('com_phocamenu');
		}
		$priceFormat		= $params->get( 'price_format', 0 );
		$priceCurSymbol		= $params->get( 'price_currency_symbol', '€' );
		$priceDecSymbol		= $params->get( 'price_dec_symbol', ',' );
		$priceThousandsSep	= $params->get( 'price_thousands_sep', '.' );
		$priceDecimals		= $params->get( 'price_decimals', 2 );
		$priceSuffix		= $params->get( 'price_suffix', '' );
		
		// Not possible to save space in parameters, not possible to use &nbsp; in number_format, not possible to use ''
		switch ($priceThousandsSep) {
			case '-':
				$priceThousandsSep = ' ';
			break;
			
			case '_':
				$priceThousandsSep = '';
			break;
		}
		
		$price = number_format((double)$price, $priceDecimals, $priceDecSymbol, $priceThousandsSep) . $priceSuffix;
		switch($priceFormat) {
			case 1:
				$price = $price . $priceCurSymbol;
			break;
			
			case 2:
				$price = $priceCurSymbol . $price;
			break;
			
			case 3:
				$price = $priceCurSymbol . ' ' . $price;
			break;
			
			case 0:
			default:
				$price = $price . ' ' . $priceCurSymbol;
			break;
		}
		
		return $price;
	
	}
	
	// Category = Group, List, Day
	public static function getActualCategory ($type, $typeValue, $filterCatid) {
	
		if ($type == 'item') {
			$categoryType = 'gid';
		} else {
			if ($typeValue == 2) {
				$categoryType = 'did';// Weekly Menu - days
			} else {
				$categoryType = 'lid';// Food Menu, Beverage List, Wine List - lists
			}
		}
	
		//$postCategory	= JRequest::getVar( $categoryType, 0, 'POST', 'int' );
		//$getCategory	= JRequest::getVar( $categoryType, 0, 'GET', 'int' );
		
		$postCategory = 0;
		if (isset($_POST[$categoryType]) && (int)$_POST[$categoryType] > 0) {
			$postCategory = $_POST[$categoryType];
		}
		
		$getCategory = 0;
		if (isset($_GET[$categoryType]) && (int)$_GET[$categoryType] > 0) {
			$getCategory = $_GET[$categoryType];
		}
		
		
		if ($postCategory > 0) {
			//$categoryId['catid']		= $postCategory;
			$categoryId	= $filterCatid;
		} else {
			//$categoryId['catid']		= $getCategory;
			$categoryId	= $getCategory;
		}

		return $categoryId;
	}
	
	public static function getDate($date, $dateFormat, $dateClass = 0) {	

		if ((int)$dateClass == 1) {
			// We call this function from frontend and backend, so no JPATH_SITE can be used
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenuczechdate.php' );
			$date = PhocaMenuCzechDate::display(JHTML::Date($date, JText::_($dateFormat)));
			//$date = PhocaMenuCzechDate::display(JHTML::_('date', $date, JText::_($dateFormat)));
		} else {
			$date = JHTML::Date($date,JText::_($dateFormat));
			//$date = JHTML::_('date', $date, JText::_( $dateFormat));
		}
		
		return $date;
	}
	
	public static function includePhocaGallery() {
		
		if (!class_exists('PhocaGalleryLoader')) {
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocagallery'.DS.'libraries'.DS.'loader.php');
		}
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocamenu'.DS.'helpers'.DS.'phocamenugallery.php' );
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.file');
		phocagalleryimport('phocagallery.image.imagefront');
		phocagalleryimport('phocagallery.render.renderfront');
		phocagalleryimport('phocagallery.file.filethumbnail'); 
		phocagalleryimport('phocagallery.library.library'); 
	}
	

	public static function getPhocaVersion() {
		$component = 'com_phocamenu';
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.$component;
		
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.$component;
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
	public static function renderCode($id, $method){
		$v	= PhocaMenuHelper::getPhocaVersion();
		$i	= str_replace('.', '',substr($v, 0, 3));
		$n	= '<p>&nbsp;</p>';
		$l	= 'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'m'.'e'.'n'.'u';
		$t	= 'P'.'o'.'w'.'e'.'r'.'e'.'d'.' '.'b'.'y';
		$p	= 'P'.'h'.'o'.'c'.'a'.' '.'R'.'e'.'s'.'t'.'a'.'u'.'r'.'a'.'n'.'t'.' '.'M'.'e'.'n'.'u';
		$s	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'d'.'e'.'c'.'o'.'r'.'a'.'t'.'i'.'o'.'n'.':'.'n'.'o'.'n'.'e'.'"';
		$s2	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'a'.'l'.'i'.'g'.'n'.':'.'c'.'e'.'n'.'t'.'e'.'r'.';'.'c'.'o'.'l'.'o'.'r'.':'.'#'.'d'.'3'.'d'.'3'.'d'.'3'.'"';
		$b	= 't'.'a'.'r'.'g'.'e'.'t'.'='.'"'.'_'.'b'.'l'.'a'.'n'.'k'.'"';
		$i	= (int)$i + $i;
		
		$output	= '';
		if ($id != $i) {
			$output		.= $n;
			$output		.= '<div '.$s2.'>';
		}
		
		if ($id == $i) {
			$output	.= '<!-- <a href="'.$l.'">site: www.phoca.cz | version: '.$v.'</a> -->';
		} else {
			$output	.= $t . ' <a href="'.$l.'" '.$s.' '.$b.' title="'.$p.'">'. $p. '</a>';
		}
		if ($id != $i) {
			$output		.= '</div>' . $n;
		}
		if ($method == 2 || $method == 1) {
			$output = '';
		}
		return $output;
	}
	
	/*
	Script Name: Simple 'if' PHP Browser detection
	Author: Harald Hope, Website: http://TechPatterns.com/
	Script Source URI: http://TechPatterns.com/downloads/php_browser_detection.php
	Version 2.0.2
	Copyright (C) 29 June 2007
	 
	Modified 22 April 2008 by Jon Czerwinski
	Added IE 7 version detection
	 
	This program is free software; you can redistribute it and/or modify it under 
	the terms of the GNU General Public License as published by the Free Software
	Foundation; either version 3 of the License, or (at your option) any later version.
	 
	This program is distributed in the hope that it will be useful, but WITHOUT 
	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
	FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 
	Get the full text of the GPL here: http://www.gnu.org/licenses/gpl.txt
	 
	Coding conventions:
	http://cvs.sourceforge.net/viewcvs.py/phpbb/phpBB2/docs/codingstandards.htm?rev=1.3
	*/
	public static function PhocaMenuBrowserDetection( $which_test ) {
	 
		// initialize the variables
		$browser 		= '';
		$dom_browser	= '';
 
		// set to lower case to avoid errors, check to see if http_user_agent is set
		$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
 
		// run through the main browser possibilities, assign them to the main $browser variable
		if (stristr($navigator_user_agent, "opera"))  {
			$browser 		= 'opera';
			$dom_browser 	= true;
		}
 
		/*
		Test for IE 7 added
		April 22, 2008
		Jon Czerwinski
		*/
		elseif (stristr($navigator_user_agent, "msie 7")) {
			$browser = 'msie7'; 
			$dom_browser = false;
		}
		
		elseif (stristr($navigator_user_agent, "msie 8")) {
			$browser = 'msie8'; 
			$dom_browser = false;
		}
 
		elseif (stristr($navigator_user_agent, "msie 4"))  {
			$browser = 'msie4'; 
			$dom_browser = false;
		}
 
		elseif (stristr($navigator_user_agent, "msie")) {
			$browser = 'msie'; 
			$dom_browser = true;
		}
 
		elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari"))) {
			$browser = 'safari'; 
			$dom_browser = true;
		}
 
		elseif (stristr($navigator_user_agent, "gecko")) {
			$browser = 'mozilla';
			$dom_browser = true;
		}
 
		elseif (stristr($navigator_user_agent, "mozilla/4")) {
			$browser = 'ns4';
			$dom_browser = false;
		}
 
		else {
			$dom_browser = false;
			$browser = false;
		}
 
		// return the test result you want
		if ( $which_test == 'browser' ) {
				return $browser;
		} elseif ( $which_test == 'dom' ) {
			return $dom_browser;
			//  note: $dom_browser is a boolean value, true/false, so you can just test if
			// it's true or not.
		}
	}
	
	/*
	 * @based based on Seb's BB-Code-Parser script by seb
	 * @url http://www.traum-projekt.com/forum/54-traum-scripts/25292-sebs-bb-code-parser.html 
	 */
	public static function bbCodeReplace($string, $currentString = '') {
	 
	    while($currentString != $string) {
			$currentString 	= $string;
			$string 		= preg_replace_callback('{\[(\w+)((=)(.+)|())\]((.|\n)*)\[/\1\]}U', array('PhocaMenuHelper', 'bbCodeCallback'), $string);
	    }
	    return $string;
	}

	/*
	 * @based based on Seb's BB-Code-Parser script by seb
	 * @url http://www.traum-projekt.com/forum/54-traum-scripts/25292-sebs-bb-code-parser.html 
	 */
	function bbCodeCallback($matches) {
		$tag 			= trim($matches[1]);
		$bodyString 	= $matches[6];
		$argument 		= $matches[4];
	    
	    switch($tag) {
			case 'b':
			case 'i':
			case 'u':
				$replacement = '<'.$tag.'>'.$bodyString.'</'.$tag.'>';
	            break;

	        default:    // unknown tag => reconstruct and return original expression
	            $replacement = '[' . $tag . ']' . $bodyString . '[/' . $tag .']';
	            break;
	    }
		return $replacement;
	}
	
	public static function replaceTag($string, $method) {
		
		switch($method) {
			case 2:
				$string = str_replace ( '[np]', '<tcpdf method="AddPage" />', $string );
			break;
			default:
				$string = str_replace ( '[np]', '', $string );
			break;
		}
		return $string;
	}
	
	public static function getCategoryList($type, $typeValue, $state) {
		
		
		
		$query	= '';
		$output	= '';
		$db		= JFactory::getDBO();	
		
		if ($type == 'item') {
			$query 		= 'SELECT a.title AS text, a.id AS value, a.catid as category_id'
					. ' FROM #__phocamenu_group AS a'
					. ' WHERE a.type = '.(int)$typeValue
					. ' AND a.catid = (SELECT ag.catid'
									. ' FROM #__phocamenu_group AS ag'
									. ' WHERE ag.id = '.(int)$state.')'
				//	. ' WHERE a.published = 1'
					. ' ORDER BY a.ordering';
		
		} else {
			switch ($typeValue) {
				case 2:
				case 3:
				case 4:
				case 5:		
					$tableName	= PhocaMenuHelper::getTypeTable($typeValue);
					$query 		= 'SELECT a.title AS text, a.id AS value, a.catid as category_id'
					. ' FROM '.$tableName.' AS a'
					. ' WHERE a.type = '.(int)$typeValue
				//	. ' WHERE a.published = 1'
					. ' ORDER BY a.ordering';
				break;
			}
		}
	
		if ($query != '') {
			$db->setQuery( $query );
			$categories = $db->loadObjectList();

			
			$output .= '<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">';
			//$output .= '<option value="">'.JText::_('JOPTION_SELECT_CATEGORY').'</option>';
			$output .= JHtml::_('select.options', $categories, 'value', 'text', $state);
			$output .='</select>';
			
		}
		
		return $output;
	}
	
	public static function getBackCategoryUrl($typeViewUp, $typeValue, $catidUp) {
		$db					= JFactory::getDBO();
		$typeUp				= PhocaMenuHelper::getTypeInfo($typeViewUp, $typeValue);
		$typeUp['urlup'] 	= '';
	
		switch ($typeValue){
			case 2:
			case 3:
			case 4:
			case 5:
				$query = 'SELECT a.catid AS catid, a.type AS type'
				. ' FROM #__phocamenu_group AS a'
				. ' WHERE a.id = '.(int)$catidUp
				. ' LIMIT 1';
				$db->setQuery( $query );
				$catid = $db->loadObject();
			
			if (isset($catid->catid) && $catid->catid > 0) {
				$typeUp['urlup'] 	=  '&'.$typeUp['catid'].'=' .(int)$catid->catid;
			}
			break;
		}
		
		return $typeUp;
	}
	
	public static function isMenuEnabled() {
		return false;
	}
}
?>