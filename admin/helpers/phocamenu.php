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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Plugin\PluginHelper;

class PhocaMenuHelper
{

	/*
	 * Used in controllers and modules
	 */
	public static function getUrlApend($typeview = 'group', $returnBack = 0 ) {

		$app = Factory::getApplication();
		//$typeValue	= JFactory::getApplication()->input->get('type', 0, '', 'int');
		$typeValue	= $app->input->get('type', 0, 'int');





		$typeviewBack	= $app->input->get('typeback', '', 'string');



		if ($typeview == 'config' || $typeview == 'email' || $typeview == 'multipleedit' || $typeview == 'rawedit') {
			$typeInfo	= PhocaMenuHelper::getTypeInfo($typeviewBack, $typeValue);
		} else {

			$typeInfo	= PhocaMenuHelper::getTypeInfo($typeview, $typeValue);
		}

		//$catid 		= JFactory::getApplication()->input->get( $typeInfo['catid'], 0, '', 'int' );
		$catid 	= $app->input->get($typeInfo['catid'], 0, 'int');
		// Catid changed in Edit mode by JForm
		//$jForm	= JFactory::getApplication()->input->get('jform', 0, '', 'array');

		$jForm	= $app->input->get('jform', array(0), 'array');


		if(isset($jForm['catid']) && (int)$jForm['catid'] > 0) {
			$catid = (int)$jForm['catid'];
		}



		if ($typeview == 'config' || $typeview == 'email' || $typeview == 'multipleedit'  || $typeview == 'rawedit') {


			if ($returnBack == 2) {
				// We are going back from RAW - there are new IDs for lists/days/groups/items so we need to go back to root
				$appendUrl	= '&view=phocamenu'.$typeInfo['root'];
			} else if ($returnBack == 1) {
				// We are going back from Config
				$appendUrl	= '&view=phocamenu'.$typeInfo['view'].'s'
						 .'&type='.(int)$typeValue.'&'.$typeInfo['catid'].'='. (int)$catid;
			} else {
				// We are going to be in Config, Email or Multiple

				/*$adminTool 	= JFactory::getApplication()->input->get( 'admintool', 0, '', 'int');
				$atid		= Factory::getApplication()->input->get( 'atid', 0, '', 'int' );
				$alang		= Factory::getApplication()->input->get( 'alang', '', '', 'string' );
				$adminLang	= JFactory::getApplication()->input->get( 'adminlang', 0, '', 'int' );*/

				$adminTool 	= $app->input->get( 'admintool', 0, 'int');
				$atid		= $app->input->get( 'atid', 0, 'int' );
				$alang		= $app->input->get( 'alang', '', 'string' );
				$adminLang	= $app->input->get( 'adminlang', 0, 'int' );
				$admin		= $app->input->get( 'admin', 0, 'int' );

				//$lang		= self::getLangAdmin(1);
				$suffix 	= '';
				if ((int)$adminTool > 0) 	{$suffix .= '&admintool='.(int)$adminTool;}
				if ((int)$atid > 0) 		{$suffix .= '&atid='.(int)$atid;}
				if ((string)$alang != '')	{$suffix .= '&lang='.(string)$alang;}
				if ((int)$adminLang > 0) 	{$suffix .= '&adminlang='.(int)$adminLang;}
				if ((int)$admin > 0) 		{$suffix .= '&admin='.(int)$admin;}

				$appendUrl	= '&type='.(int)$typeValue.'&'.$typeInfo['catid'].'='. (int)$catid
						 .'&typeback='.(string)$typeviewBack.$suffix;
			}
		} else {

			// NEW ITEM - when we are in All items and click new, we need to select which group will be the new item
			// This only applies to new items, so the group is always gid
			$new	= $app->input->get('new', array(0), 'array');


			if (isset($new['category_id']) && (int)$new['category_id'] > 0) {


				$typeFound = self::getTypeByCategory((int)$new['category_id'], 'Group', 0);

				if ($typeFound && (int)$typeFound > 0) {
					$typeBackUrl = $typeviewBack != '' ? '&typeback=' . (string)$typeviewBack : '';
					$appendUrl   = '&type=' . (int)$typeFound . '&gid=' . (int)$new['category_id'] . $typeBackUrl;
					return $appendUrl;
				} else {
					return false;
				}

			} else {

				if ((int)$typeValue < 1) {
					return false;
				}

				// Standard append except Config
				$typeBackUrl = $typeviewBack != '' ? '&typeback=' . (string)$typeviewBack : '';
				$appendUrl   = '&type=' . (int)$typeValue . '&' . $typeInfo['catid'] . '=' . (int)$catid . $typeBackUrl;
			}
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
		$langG		= JFactory::getApplication()->input->get('lang', 'all', 'GET', 'string');//link for specific items
		//$langP		= JFactory::getApplication()->input->get('language', '', 'POST', 'string');
		$langF		= JFactory::getApplication()->input->get('filter_language', '', 'POST', 'string');//hidden field in default.php

		if ($langF != '') {
			if ($reverse == 1) {

				if ($langF == '*') {
					return 'all';
				} else {
					jimport('joomla.language.helper');
					$code = LanguageHelper::getLanguages('lang_code');
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
				$sef = LanguageHelper::getLanguages('sef');
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
			$sef = LanguageHelper::getLanguages('sef');
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
			$code = LanguageHelper::getLanguages('lang_code');
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




		$app = Factory::getApplication();
		// Both are the same in J4
		//$post 		= $app->input->post->getArray();
		//$get		= $app->input->get->getArray();
		$post 		= $_POST;
		$get		= $_GET;
		$type	= array();

		//$type['value']		= JFactory::getApplication()->input->get('type', 0, '', 'int');
		$type['value']		= $app->input->get('type', 0, 'int');
		$type['info']		= PhocaMenuHelper::getTypeInfo($typeview, $type['value']);
		//$type['valuecatid'] = JFactory::getApplication()->input->get( $type['info']['catid'], 0, '', 'int' );
		$catName			= $type['info']['catid'];
		$type['valuecatid'] = $app->input->get( $catName, 0, 'int' );



		if (isset($post[$catName]) && $post[$catName] > 0) {
			$type['method'] = 'post';
		} else if (isset($get[$catName]) && $get[$catName] > 0) {
			$type['method'] = 'get';
		} else {
			$type['method'] = false;
		}

		// Catid changed in Edit mode by JForm
		//$jForm	= JFactory::getApplication()->input->get('jform', 0, '', 'array');
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

	public static function getTypeByCategory($catId = 0, $table = 'Group', $fallBack = 1) {
		if ((int)$catId > 0) {
			$db					= Factory::getDBO();


			switch ($table) {
				case 'Day':
					$tableS = 'day';
					$columnS = 'a.id';
				break;
				case 'List':
					$tableS = 'list';
					$columnS = 'a.id';
				break;
				case 'Item':
					$tableS = 'item';
					$columnS = 'a.catid';
				break;
				case 'Group':
				default:
					$tableS = 'group';
					$columnS = 'a.id';
				break;

			}


			$query = 'SELECT a.type'
				. ' FROM #__phocamenu_'.$tableS.' AS a'
				. ' WHERE '.$columnS.' = '.(int)$catId
				. ' LIMIT 1';

			$db->setQuery($query);
			$catType = $db->loadObject();


			if (isset($catType->type) && (int)$catType->type > 0) {
				return (int)$catType->type;
			} else {
				// If there is no type, we set the default one - 1
				// But if fllback is disabled, return false
				if ($fallBack == 0) {
					return false;
				}

			}
		}
		// Default type is set to 1
		return 1;
	}

	// Quick function for selects
	public static function getTitleByType($type, $format = 0) {

		$title = '';
		switch ($type) {
			case 1: $title =Text::_('COM_PHOCAMENU_DAILY_MENU');		break;
			case 2: $title =Text::_('COM_PHOCAMENU_WEEKLY_MENU');		break;
			case 3: $title =Text::_('COM_PHOCAMENU_BILL_OF_FARE');		break;
			case 4: $title =Text::_('COM_PHOCAMENU_BEVERAGE_LIST');	break;
			case 5: $title =Text::_('COM_PHOCAMENU_WINE_LIST');		break;
			case 6: $title =Text::_('COM_PHOCAMENU_BREAKFAST_MENU');	break;
			case 7: $title =Text::_('COM_PHOCAMENU_LUNCH_MENU');		break;
			case 8: $title =Text::_('COM_PHOCAMENU_DINNER_MENU');		break;
			default: $title = '';	break;
		}


		if ($format == 1) {
			$title = '<div class="badge prm-badge-'.$type.'"">'.$title.'</div>';
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

		$app				= Factory::getApplication();
		$typeInfo			= array();
		$typeInfo['type']	= $type;
		$typeInfo['view']	= $typeview;

		if ($typeview == '') {
			// Debug Info
			$view		= $app->input->get('view', '', 'string');
			$wTxt	= $view != '' ? Text::_('COM_PHOCAMENU_VIEW').': '. $view : '';
			$twTxt	= Text::_('COM_PHOCAMENU_TYPE').': '. $type;
			$errTxt	= ' ( '.$wTxt. ' ' .$twTxt.' ) ';
			$app->enqueueMessage(Text::_('COM_PHOCAMENU_ERROR_NO_MENU_TYPE_VIEW_FOUND') . $errTxt, 'error');
			$app->redirect(Route::_('index.php?option=com_phocamenu', false));
		}

		if ($typeview == 'gallery') {
			$typeInfo['backlink']		= '';
			$typeInfo['catid']			= -1;
			$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_CONTROL_PANEL';
			$typeInfo['text']			= Text::_('COM_PHOCAMENU_GALLERY');
			return $typeInfo;
		}


		switch ($type) {

			case 1:
				switch($typeview) {
					case 'group':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_CONTROL_PANEL';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU') . ' ' . Text::_('COM_PHOCAMENU_GROUP');
					break;

					case 'item':
						$typeInfo['backlink']		= '&view=phocamenugroups&type=1';
						$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_GROUPS';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . Text::_('COM_PHOCAMENU_ITEM');
					break;

					case 'config':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . Text::_('COM_PHOCAMENU_MENU_SETTINGS');
					break;

					case 'email':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . Text::_('COM_PHOCAMENU_SEND_EMAIL');
					break;
					case 'multipleedit':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . Text::_('COM_PHOCAMENU_MULTIPLE_EDIT');
					break;
					case 'rawedit':
						$typeInfo['backlink']		= '';
						$typeInfo['backlinktxt']	= '';
						$typeInfo['text']			= Text::_('COM_PHOCAMENU_DAILY_MENU'). ' ' . Text::_('COM_PHOCAMENU_RAW_EDIT');
					break;
				}
				$typeInfo['catid']			= 'gid';
				$typeInfo['pref']			= 'dm';
				$typeInfo['frontview']		= 'dailymenu';
				$typeInfo['render']			= 'renderDailyMenu';
				$typeInfo['title']			= Text::_('COM_PHOCAMENU_DAILY_MENU');
				$typeInfo['root']			= '&view=phocamenugroups&type=1';
			break;



			case -1:
				// All items
				$typeInfo['catid']			= 'gid';
				$typeInfo['backlink']		= '&view=phocamenuallitems';
				$typeInfo['backlinktxt']	= 'COM_PHOCAMENU_ALL_ITEMS';
				$typeInfo['text']			= Text::_('COM_PHOCAMENU_ALL_ITEMS'). ' ' . Text::_('COM_PHOCAMENU_ITEM');
				$typeInfo['pref']			= '';
				$typeInfo['frontview']		= '';
				$typeInfo['render']			= '';
				$typeInfo['title']			= Text::_('COM_PHOCAMENU_ALL_ITEMS');
				$typeInfo['root']			= '&view=phocamenuallitems';
			break;

			case 0:
			default:

				//$view		= JFactory::getApplication()->input->get('view');
				$view		= $app->input->get('view', '', 'string');
				$wTxt	= $view != '' ? Text::_('COM_PHOCAMENU_VIEW').': '. $view : '';
				$twTxt	= $typeview != '' ? Text::_('COM_PHOCAMENU_TYPE_VIEW').': '. $typeview : '';
				$errTxt	= ' ( '.$wTxt. ' ' .$twTxt.' ) ';
				$app->enqueueMessage(Text::_('COM_PHOCAMENU_ERROR_NO_MENU_TYPE_VIEW_FOUND') . $errTxt, 'error');
				$app->redirect(Route::_('index.php?option=com_phocamenu', false));
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

		$arrowImg	= '&nbsp; <b>&#8680;</b> &nbsp;';//HTMLHelper::_('image', 'media/com_phocamenu/images/administrator/icon-arrow.png', '-' );
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
					  .'<a href="index.php?option=com_phocamenu">'. Text::_('COM_PHOCAMENU_CONTROL_PANEL').'</a>'
					  . $backUp . $back
					  .'<span class="arrow"> '.$arrowImg.' </span>'
					  . $current
					  .'</div>';
		return $breadcrumbs;
	}

	public static function getPriceFormat($price, $params = array()) {

		// Administration (no frontend)
		if (empty($params)) {
			$params = ComponentHelper::getParams('com_phocamenu');
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

		$app		= Factory::getApplication();
		$post 		= $app->input->post->getArray();
		$get		= $app->input->get->getArray();

		if ($type == 'item') {
			$categoryType = 'gid';
		} else {
			if ($typeValue == 2) {
				$categoryType = 'did';// Weekly Menu - days
			} else {
				$categoryType = 'lid';// Food Menu, Beverage List, Wine List - lists
			}
		}



		$postCategory = 0;
		if (isset($post[$categoryType]) && (int)$post[$categoryType] > 0) {
			$postCategory = $post[$categoryType];
		}

		$getCategory = 0;
		if (isset($get[$categoryType]) && (int)$get[$categoryType] > 0) {
			$getCategory = $get[$categoryType];
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

	public static function getDate($date, $dateFormat, $dateClass = 0, $language = '') {

		if ($language == '*') {
			$language = '';// Don't reload base config language
		}

		if ((int)$dateClass == 1) {
			// We call this function from frontend and backend, so no JPATH_SITE can be used
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenuczechdate.php' );
			$date = PhocaMenuCzechDate::display(HTMLHelper::Date($date, Text::_($dateFormat)));
			//$date = PhocaMenuCzechDate::display(JHtml::_('date', $date, JText::_($dateFormat)));
		} else {

			if ($language != '') {

				$lang = Factory::getLanguage();
				$defaultLocale = $lang->getTag();
				if ($defaultLocale != $language) {
					$lang->load('joomla', JPATH_BASE, $language);
					$lang->load('com_phocamenu', JPATH_BASE, $language);
					$lang->load('com_phocamenu.sys', JPATH_BASE, $language);
				}
				//load($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true)
			}

			$date = HTMLHelper::Date($date,Text::_($dateFormat));
			//$date = JHtml::_('date', $date, JText::_( $dateFormat));
		}

		return $date;
	}

	public static function includePhocaGallery() {

		if (!class_exists('PhocaGalleryLoader')) {
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
		}
		require_once( JPATH_ADMINISTRATOR.'/components/com_phocamenu/helpers/phocamenugallery.php' );
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.file');
		phocagalleryimport('phocagallery.image.imagefront');
		phocagalleryimport('phocagallery.render.renderfront');
		phocagalleryimport('phocagallery.file.filethumbnail');
		phocagalleryimport('phocagallery.library.library');
	}


	public static function getPhocaVersion() {
		$component = 'com_phocamenu';
		$folder = JPATH_ADMINISTRATOR .'/components/'.$component;

		if (Folder::exists($folder)) {
			$xmlFilesInDir = Folder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE . '/components/'.$component;
			if (Folder::exists($folder)) {
				$xmlFilesInDir = Folder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (!empty($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JInstaller::parseXMLInstallFile($folder.'/'.$xmlfile)) {
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


		PluginHelper::importPlugin('phocatools');
		$results = Factory::getApplication()->triggerEvent('onPhocatoolsOnDisplayInfo', array('NjI5NTcyMjc2MjE1NzExNw=='));
		if (isset($results[0]) && $results[0] === true) {
			return '';
		}


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
		$db		= Factory::getDBO();

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


			$output .= '<div class="btn-group pull-right">';
			$output .= '<select name="filter_category_id" class="form-control" onchange="this.form.submit()">';
			//$output .= '<option value="">'.JText::_('JOPTION_SELECT_CATEGORY').'</option>';
			$output .= HTMLHelper::_('select.options', $categories, 'value', 'text', $state);
			$output .= '</select>';
			$output .= '</div>';

		}

		return $output;
	}

	public static function getBackCategoryUrl($typeViewUp, $typeValue, $catidUp) {
		$db					= Factory::getDBO();
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

	public static function cleanRawOutput($string) {

		$string = str_replace('&#160;', '', $string);
		$string = trim($string);
		$string = strip_tags($string);

		return $string;

	}

	public static function replaceCommaWithPoint($item) {
		$app				= Factory::getApplication();
		$paramsC 			= $app->isClient('administrator') ? ComponentHelper::getParams('com_phocamenu') : $app->getParams();
		$comma_point	= $paramsC->get( 'comma_point', 0 );
		if ($comma_point == 1) {
			return str_replace(',', '.', $item);
		} else {
			return $item;
		}
	}
}
?>
