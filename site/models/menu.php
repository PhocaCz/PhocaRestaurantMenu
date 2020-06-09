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
jimport('joomla.application.component.model');

class PhocaMenuModelMenu extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct() {
		parent::__construct();
		$app	= JFactory::getApplication();
		$id 	= $app->input->get('id', 0, '', 'int');
		$this->setState('filter.language',$app->getLanguageFilter());
		$this->setId((int)$id);
	}

	function setId($id) {
		$this->_id		= $id;
		$this->_data	= null;
	}

	function &getData($type = 1) {
		$this->_loadData($type);
		return $this->_data;
	}

	function _loadData($type = 1) {

		if (empty($this->_data)) {

			$app	= JFactory::getApplication();
			$params				= $app->getParams();

			/* Specific data from FRONTEND
			 * you can display all data from menu type or you can display only one list(day) from menu type
			 */
			$menuListIdsArray	= $params->get( 'menulist', array() );
			$menuDayIdsArray	= $params->get( 'menuday', array() );
			$displayHeader		= $params->get( 'displayheader', 1 );
			$displayFooter		= $params->get( 'displayfooter', 1 );
			$displayHeaderDate	= $params->get( 'displayheaderdate', 1 );
			$displayCurrentDay	= $params->get( 'displaycurrentday');

			// Filter by language
			$wheresLang = '';

			if ($this->getState('filter.language')) {
				$wheresLang =  'a.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			} else {
				// Because of possible duplicity, we can get the last item but we need to differentiate between languages
				// all language is "" or "*"
				$wheresLang =  'a.language IN (\'\','.$this->_db->Quote('*').')';
			}

			/* Specific data from ADMINISTRATOR
			 * In administration you can display data from menu type or you can display one list(day) from menu type
			 * This is used for: PRINT PDF, PREVIEW, MULTIPLE EDIT, EMAIL (administration tools in lists or days)
			 */

			$adminTool 	= $app->input->get( 'admintool', 0, 'get', 'int' );//Items tool
			$atid 		= $app->input->get( 'atid', 0, 'get', 'int' );//Items tool id
			$adminLang	= $app->input->get( 'adminlang', 0, 'get', 'int' );//We call the site from admin
			$alang 		= $app->input->get( 'alang', '', 'get', 'string' );//Alang because we call frontend where the link can be changed

			if ((int)$adminLang == 1) {
				if ($alang != '') {

					$wheresLang =  'a.language ='.$this->_db->Quote(PhocaMenuHelper::getLangCode($alang));
				} else {
					$wheresLang = '';
				}
			}


			//CONFIG
			$wheres		= array();
			$wheres[]	= 'a.type = '.(int)$type;
			if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}

			$header = "'' AS header";
			if ($displayHeader > 0) {
				$header = 'a.header';
			}
			$footer = "'' AS footer";
			if ($displayFooter > 0) {
				$footer = 'a.footer';
			}
			$headerDate = "'' AS date_from, '' AS date_to";
			if ($displayHeader > 0) {
				$headerDate = 'a.date_from, a.date_to';
			}

			// TO DO CHANGE - select latest config in case there are more obsolete.
			if ($wheresLang == '') {
				$wheres[] = 'a.date_to = (SELECT MAX(a2.date_to) FROM #__phocamenu_config AS a2 WHERE a2.type = '.(int)$type.')';
			} else {
              $wheres[] = 'a.date_to = (SELECT MAX(a2.date_to) FROM #__phocamenu_config AS a2 WHERE a2.type = '.(int)$type.' AND '.str_replace('a.', 'a2.', $wheresLang).')';

			}



			$query 		= 'SELECT a.*,'
						. ' '.$header.','
						. ' '.$footer.','
						. ' '.$headerDate
						. ' FROM #__phocamenu_config AS a'
						. ' WHERE ' . implode(' AND ', $wheres);


			$this->_db->setQuery($query);
			$this->_data['config'] = $this->_db->loadObject();

			if ((int)$adminLang == 1) {

			} else {
				// We need to set header and footer in specific langauge, if does not exist then in "all" language
				$dConfig = $this->_db->loadObjectList();
				if (isset($dConfig[0]) && !isset($dConfig[1])) {
					$this->_data['config'] = $dConfig[0];
				}

				if (isset($dConfig[0]) && isset($dConfig[1]) && isset($dConfig[0]->language) && $dConfig[0]->language == '*') {
					$this->_data['config'] = $dConfig[1];
					if ((isset($dConfig[1]->header) && $dConfig[1]->header == '') || !isset($dConfig[1]->header)) {
						$this->_data['config']->header = $dConfig[0]->header;
					}
					if ((isset($dConfig[1]->footer) && $dConfig[1]->footer == '') || !isset($dConfig[1]->footer)) {
						$this->_data['config']->footer = $dConfig[0]->footer;
					}
					if ((isset($dConfig[1]->metakey) && $dConfig[1]->metakey == '') || !isset($dConfig[1]->metakey)) {
						$this->_data['config']->metakey = $dConfig[0]->metakey;
					}
					if ((isset($dConfig[1]->metadesc) && $dConfig[1]->metadesc == '') || !isset($dConfig[1]->metadesc)) {
						$this->_data['config']->metadesc = $dConfig[0]->metadesc;
					}
					if ((isset($dConfig[1]->date) && $dConfig[1]->date == '') || !isset($dConfig[1]->date)) {
						$this->_data['config']->date = $dConfig[0]->date;
					}
					if ((isset($dConfig[1]->date_from) && $dConfig[1]->date_from == '') || !isset($dConfig[1]->date_from)) {
						$this->_data['config']->date_from = $dConfig[0]->date_from;
					}
					if ((isset($dConfig[1]->date_to) && $dConfig[1]->date_to == '') || !isset($dConfig[1]->date_to)) {
						$this->_data['config']->date_to = $dConfig[0]->date_to;
					}

				}
			}


			if ($type == 3 || $type == 4 || $type == 5) {
				//LIST
				$wheres 	= array();
				$wheres[] 	= 'a.published = 1';
				$wheres[]	= 'a.type = '.(int)$type;
				if ($wheresLang != '') {
					$wheres[]	= $wheresLang;
				}

				// Front
				if (count( $menuListIdsArray )) {
					if (!is_array($menuListIdsArray) && (int)$menuListIdsArray > 0) {
						$wheres[]	= ' a.id = '.(int)$menuListIdsArray;
					} else {
						\Joomla\Utilities\ArrayHelper::toInteger($menuListIdsArray);
						$menuListIds = implode( ',', $menuListIdsArray );
						$wheres[]	= ' a.id IN ( '.$menuListIds.' )';
					}
				}
				// Administration
				else if ($adminTool == 1 && (int)$atid > 0) {
					$wheres[]	= ' a.id = '.(int)$atid;
				}

				$query 		= ' SELECT a.*'
							. ' FROM #__phocamenu_list AS a'
							. ' WHERE ' . implode(' AND ', $wheres)
							. ' ORDER BY ordering ASC';
				$this->_db->setQuery($query);
				$this->_data['list'] = $this->_db->loadObjectList();

			}

			if ($type == 2) {
				//DAY
				$wheres 	= array();
				$wheres[] 	= 'a.published = 1';
				$wheres[]	= 'a.type = '.(int)$type;
				if ($wheresLang != '') {
					$wheres[]	= $wheresLang;
				}

				// Front
				if ($displayCurrentDay == 1) {
					jimport('joomla.utilities.date');
					$date		= JFactory::getDate();
					$dateFrom	= $date->format('Y-m-d 00:00:00');
					$dateTo		= $date->format('Y-m-d 23:59:59');
					$wheres[]	= ' a.title BETWEEN \''.$dateFrom.'\' AND \''.$dateTo.'\'';
				}
				else if (count( $menuDayIdsArray )) {
					if (!is_array($menuDayIdsArray) && (int)$menuDayIdsArray > 0) {
						$wheres[]	= ' a.id = '.(int)$menuDayIdsArray;
					} else {
						\Joomla\Utilities\ArrayHelper::toInteger($menuDayIdsArray);
						$menuDayIds = implode( ',', $menuDayIdsArray );
						$wheres[]	= ' a.id IN ( '.$menuDayIds.' )';
					}
				}
				// Administration
				else if ($adminTool == 1 && (int)$atid > 0) {
					$wheres[]	= ' a.id = '.(int)$atid;
				}

				$query 		= ' SELECT a.*'
							. ' FROM #__phocamenu_day AS a'
							. ' WHERE ' . implode(' AND ', $wheres)
							. ' ORDER BY ordering ASC';
				$this->_db->setQuery($query);
				$this->_data['day'] = $this->_db->loadObjectList();

			}

			//GROUP
			$wheres 	= array();
			$wheres[] 	= 'a.published = 1';
			$wheres[]	= 'a.type = '.(int)$type;
			if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}

			$query 		= 'SELECT a.*'
						. ' FROM #__phocamenu_group AS a'
						. ' WHERE ' . implode(' AND ', $wheres)
						. ' ORDER BY ordering ASC';
			$this->_db->setQuery($query);
			$this->_data['group'] = $this->_db->loadObjectList();

			//ITEM
			$wheres 	= array();
			$wheres[] 	= 'a.published = 1';
			$wheres[]	= 'a.type = '.(int)$type;
			if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}

			//Check Phoca Gallery
			$phocaGallery = PhocaMenuExtensionHelper::getExtensionInfo('com_phocagallery', 'component');
			if ($phocaGallery != 1) {

				$query 	= 'SELECT a.*, 0 as imageid'
					. ' FROM #__phocamenu_item AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';

			} else {

				$query 	= 'SELECT a.*, i.id as imageid, i.alias as imagealias, i.filename as imagefilename,'
					. ' ic.id as imagecatid, ic.alias as imagecatalias,'
					. ' i.extid as imageextid, i.exts as imageexts, i.extm as imageextm, i.extl as imageextl,'
					. ' i.extw as imageextw, i.exth as imageexth,'
					. ' CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(\':\', i.id, i.alias) ELSE i.id END as imageslug,'
					. ' CASE WHEN CHAR_LENGTH(ic.alias) THEN CONCAT_WS(\':\', ic.id, ic.alias) ELSE ic.id END as imagecatslug'
					. ' FROM #__phocamenu_item AS a'
					. ' LEFT JOIN #__phocagallery AS i ON i.id = a.imageid'
					. ' LEFT JOIN #__phocagallery_categories AS ic ON ic.id = i.catid'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';

			}


			$this->_db->setQuery($query);
			$this->_data['item'] = $this->_db->loadObjectList();

			//IMAGE
			$wheres 	= array();
			$wheres[] 	= 'a.published = 1';
			$wheres[]	= 'a.type = '.(int)$type;
			$query 		= 'SELECT SUM(a.imageid) AS sum'
						. ' FROM #__phocamenu_item AS a'
						. ' WHERE ' . implode(' AND ', $wheres)
						. ' ORDER BY ordering ASC';
			$this->_db->setQuery($query);
			$this->_data['imagesum'] = $this->_db->loadObject();

			$wheres = array();// clear $wheres
			return (boolean) $this->_data;
		}
		return true;
	}
}
?>
