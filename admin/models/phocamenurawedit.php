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
defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');
class PhocaMenuCpModelPhocaMenuRawEdit extends JModelAdmin
{
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';


	public function getForm($data = array(), $loadData = true) {

		return false;
	}

	public function getFormData() {

		/* Specific data from ADMINISTRATOR
		 * In administration you can display data from menu type or you can display one list(day) from menu type
		 * This is used for: PRINT PDF, PREVIEW, MULTIPLE EDIT, EMAIL (administration tools in lists or days)
		 */

		$type 		= PhocaMenuHelper::getUrlType('rawedit');
		$adminTool 	= JFactory::getApplication()->input->get('admintool', 0, '', 'int');
		$atid		= JFactory::getApplication()->input->get( 'atid', 0, '', 'int' );
		$content	= array();

		$wheresLang = '';
		$content['itemlanguage'] = '';
		if (!$this->getState('filter.language')) {
			// If there is no language set we need to set empty or * to not load all language versions
			$wheresLang = ' (a.language ='.$this->_db->Quote('*').' OR a.language ='.$this->_db->Quote('').')';
		} else if ($this->getState('filter.language')) {
			//Possible old format data
			if ($this->getState('filter.language') == '*' || $this->getState('filter.language') == '') {
				$wheresLang = ' (a.language ='.$this->_db->Quote('*').' OR a.language ='.$this->_db->Quote('').')';
			} else {
				$wheresLang = ' a.language ='.$this->_db->Quote($this->getState('filter.language'));
			}
		}


		//DAY
		$wheres 	= array();
		$wheres[] 	= 'a.published = 1';
		$wheres[]	= 'a.type = '.(int)$type['value'];
		if ($adminTool == 1 && (int)$atid > 0) {
			$wheres[]	= ' a.id = '.(int)$atid;
			$wheresLang = '';
		} else {
			// Ignore language if we ask specific ID
			if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}
		}

		$query 		= ' SELECT a.*'
					. ' FROM #__phocamenu_day AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';
		$this->_db->setQuery($query);
		$content['day'] = $this->_db->loadObjectList();




		// Specific ID = Specific language
		if ($adminTool == 1 && (int)$atid > 0) {
			if (isset($content['day'][0]->language)) {
				$wheresLang = 'a.language ='.$this->_db->Quote($content['day'][0]->language);
				$content['itemlanguage'] = $content['day'][0]->language;
			}
		}

		//LIST
		$wheres 	= array();
		$wheres[] 	= 'a.published = 1';
		$wheres[]	= 'a.type = '.(int)$type['value'];
		if ($adminTool == 1 && (int)$atid > 0) {
			$wheres[]	= ' a.id = '.(int)$atid;
			$wheresLang = '';
		} else {
			// Ignore language if we ask specific ID
			if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}
		}

		$query 		= ' SELECT a.*'
					. ' FROM #__phocamenu_list AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';
		$this->_db->setQuery($query);
		$content['list'] = $this->_db->loadObjectList();

		// Specific ID = Specific language
		if ($adminTool == 1 && (int)$atid > 0) {
			if (isset($content['list'][0]->language)) {
				$wheresLang = 'a.language ='.$this->_db->Quote($content['list'][0]->language);
				$content['itemlanguage'] = $content['list'][0]->language;

			}
		}

		// Corect the language - if it was not set by day or list, nor by previously settings, change it to nothing
		if ($wheresLang == "a.language=''" || 	$wheresLang == "a.language =''") {
			$wheresLang = '';
		}


		//CONFIG
		$wheres		= array();
		$wheres[]	= 'a.type = '.(int)$type['value'];


		if ($wheresLang != '') {
			$wheres[]	= $wheresLang;
		}

		$query 		= 'SELECT a.*'
					. ' FROM #__phocamenu_config AS a'
					. ' WHERE ' . implode(' AND ', $wheres);


		$this->_db->setQuery($query);
		$content['config'] = $this->_db->loadObject();


		//GROUP
		$wheres 	= array();
		$wheres[] 	= 'a.published = 1';
		$wheres[] 	= 'a.type = '.(int)$type['value'];
		if ($wheresLang != '') {
				$wheres[]	= $wheresLang;
			}



		$query 		= 'SELECT a.*'
					. ' FROM #__phocamenu_group AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';
		$this->_db->setQuery($query);
		$content['group'] = $this->_db->loadObjectList();

		//ITEM
		$wheres 	= array();
		//$wheres[] 	= 'a.published = 1';// In Multiple Edit - unpublished items are displayed
		$wheres[] 	= 'a.type = '.(int)$type['value'];
		if ($wheresLang != '') {
			$wheres[]	= $wheresLang;
		}

		$query 		= 'SELECT a.*'
					. ' FROM #__phocamenu_item AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';
		$this->_db->setQuery($query);
		$content['item'] = $this->_db->loadObjectList();

		//EMAIL
		/*$wheres 	= array();
		$wheres[] 	= 'a.published = 1';
		$wheres[] 	= 'a.type = '.(int)$type['value'];
		$wheres[]	= 'a.id = '.(int) $this->_id;
		$query 		= 'SELECT a.*'
					. ' FROM #__phocamenu_email AS a'
					. ' WHERE ' . implode(' AND ', $wheres)
					. ' ORDER BY ordering ASC';
		$this->_db->setQuery($query);
		$content['email'] = $this->_db->loadObject();*/

		$wheres = array();
		return $content;
	}

	public function save($post, &$errorMsg = '') {

		$app			= JFactory::getApplication();
		$type 			= PhocaMenuHelper::getUrlType('rawedit');
		$paramsC 		= $app->isClient('administrator') ? JComponentHelper::getParams('com_phocamenu') : $app->getParams();
		$item_delimiter	= $paramsC->get( 'item_delimiter', 1 );
		if ($item_delimiter == 1) {
			$item_delimiter = ";";
		} else {
			$item_delimiter = "\t";
		}
		//$adminTool 	= JFactory::getApplication()->input->get('admintool', 0, '', 'int');
		//$atid		= JFactory::getApplication()->input->get( 'atid', 0, '', 'int' );

		// Language
		$lang = '';
		if (isset($post['language']) && $post['language'] != '') {
			$lang		= $post['language'];
		}


		$d 	= strip_tags($post['menudata']);
		$dN	= array();
		if (!empty($d)) {
			$dA = explode("\n", $d);

			if (!empty($dA)) {
				$i = 0;// Group
				$l = 0;// Day or List
				$j = 0;// Item



				foreach ($dA as $k => $v) {

					if (substr($v, 0, 3) === '###') {
						$i++;
						$dN['group'][$l][$i] = ltrim($v, '#');

					} else if (substr($v, 0, 2) === '##') {
						$l++;
						$dN['daylist'][$l] = ltrim($v, '#');
					} else if (substr($v, 0, 1) === '#') {
						$dN['date'] = ltrim($v, '#');



						$dSpec = explode(' - ', $dN['date']);
						$dN['date_from'] = '';
						$dN['date_from'] = '';
						if (isset($dSpec[0]) && $dSpec[0] != '') {
							$dN['date_from'] = $dSpec[0];
						}
						if (isset($dSpec[1]) && $dSpec[1] != '') {
							$dN['date_to'] = $dSpec[1];
						}

					} else if (substr($v, 0, 1) === '>') {
						$dN['message'][$l][$i] = ltrim($v, '>');
					} else {
						$dIA = explode($item_delimiter, $v);
						$j++;

						if (isset($dIA[0])) {$dN['item'][$l][$i][$j]['quantity'] = $dIA[0];} else {$dN['item'][$l][$i][$j]['quantity'] = '';}
						if (isset($dIA[1])) {$dN['item'][$l][$i][$j]['title'] = $dIA[1];} else {$dN['item'][$l][$i][$j]['title'] = '';}
						if (isset($dIA[2])) {$dN['item'][$l][$i][$j]['price'] = $dIA[2];} else {$dN['item'][$l][$i][$j]['price'] = '';}
						if (isset($dIA[3])) {$dN['item'][$l][$i][$j]['price2'] = $dIA[3];} else {$dN['item'][$l][$i][$j]['price2'] = '';}
						if (isset($dIA[4])) {$dN['item'][$l][$i][$j]['description'] = $dIA[4];} else {$dN['item'][$l][$i][$j]['description'] = '';}
						if (isset($dIA[5])) {$dN['item'][$l][$i][$j]['imageid'] = $dIA[5];} else {$dN['item'][$l][$i][$j]['imageid'] = '';}

					}
				}

			}

		}



		// Data
		$db 				= JFactory::getDBO();
		$data				= array();
		$data['language'] 	= $lang;
		$data['type'] 		= $type['value'];
		$data['date']		= isset($dN['date']) ? trim($dN['date']) : '';
		$data['date_from']	= isset($dN['date_from']) ? trim($dN['date_from']) : '';
		$data['date_to']	= isset($dN['date_to']) ? trim($dN['date_to']) : '';




		if ($data['date'] != '') {
			$dateA = explode(' ', trim($dN['date']));
			if (isset($dateA[0]) && (!isset($dateA[1]) || (isset($dateA[1]) && trim($dateA[1]) == ''))) {
				$data['date'] = trim($dateA[0]) . ' 00:00:00';
			}
		}

		if ($data['date_from'] != '') {
			$dateFA = explode(' ', trim($dN['date_from']));
			if (isset($dateFA[0]) && (!isset($dateFA[1]) || (isset($dateFA[1]) && trim($dateFA[1]) == ''))) {
				$data['date_from'] = trim($dateFA[0]) . ' 00:00:00';
			}
		}

		if ($data['date_to'] != '') {
			$dateTA = explode(' ', trim($dN['date_to']));

			if (isset($dateTA[0]) && (!isset($dateTA[1]) || (isset($dateTA[1]) && trim($dateTA[1]) == ''))) {
				$data['date_to'] = trim($dateTA[0]) . ' 00:00:00';
			}
		}


		if ($data['language'] == '*' || $data['language'] == '') {
			$data['langwhere'] = ' (language = '.$db->quote($data['language']).' OR language = \'\')';
		} else {
			$data['langwhere'] = ' language = '.$db->quote($data['language']);
		}


		// =======================
		// Daily Menu, Breakfast Menu, Lunch Menu, Dinner Menu  - 1,6,7,8
		// =======================
		if ($data['type'] == 1 || $data['type'] == 6 || $data['type'] == 7 || $data['type'] == 8) {

			// DAILY MENU
			if ($data['type'] == 1) {
				if (!isset($dN['date']) || (isset($dN['date']) && $dN['date'] == '')) {
					$msg = JText::_('COM_PHOCAMENU_ERROR_NO_DATE_FOUND');
					$app->enqueueMessage($msg, 'error');
					return false;
				}
			}

			if (empty($dN['group'][0])) {
				$msg = JText::_('COM_PHOCAMENU_ERROR_NO_GROUP_FOUND');
				$app->enqueueMessage($msg, 'error');
				return false;
			}

			// CONFIG
			if ($data['type'] == 1) {
				$q = ' SELECT a.id FROM #__phocamenu_config AS a'
					.' WHERE type = '.(int)$data['type']
					.' AND '.$data['langwhere']
					.' ORDER BY a.id';

				$db->setQuery($q);
				$config = $db->loadRow();


				if (isset($config[0]) && (int)$config[0] > 0) {
					$q = 'UPDATE #__phocamenu_config SET date = '.$db->quote($data['date']).' WHERE id = '.(int)$config[0];
					$db->setQuery($q);

					$db->execute();
				} else {
					$q = ' INSERT INTO #__phocamenu_config ( `type`, `date`, `language`) VALUES'
						.' ('.(int)$data['type'].', '.$db->quote($data['date']).', '.$db->quote($data['language']).')';
					$db->setQuery($q);
					$db->execute();
				}
			}

			// CLEAN TABLES
			$q = 'DELETE FROM #__phocamenu_item WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			$db->setQuery($q);
			$db->execute();
			$q = 'DELETE FROM #__phocamenu_group WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			$db->setQuery($q);
			$db->execute();

			$i = 1;// ordering group
			foreach($dN['group'][0] as $k2 => $v2) {

				$data['message'] 	= '';
				if (isset($dN['message'][0][$k2]) && $dN['message'][0][$k2] != '') {
					$data['message'] = $dN['message'][0][$k2];
				}

				// type, title, message
				$q = ' INSERT INTO #__phocamenu_group ( `type`, `title`, `message`, `language`, `published`, `ordering`) VALUES'
					.' ('.(int)$data['type'].', '.$db->quote(strip_tags($v2)).', '.$db->quote($data['message']).', '.$db->quote($data['language']).', 1, '.$i.')';
				$db->setQuery($q);
				$db->execute();

				$insertIdGroup = $db->insertid();
				$j = 1;// ordering item
				if (!empty($dN['item'][0][$k2])) {
					foreach($dN['item'][0][$k2] as $k3 => $v3) {

						$price = PhocaMenuHelper::replaceCommaWithPoint(strip_tags($v3['price']));
						$price2 = PhocaMenuHelper::replaceCommaWithPoint(strip_tags($v3['price2']));

						$q = ' INSERT INTO #__phocamenu_item ( `catid`, `imageid`, `type`, `quantity`, `title`, `price`, `price2`,  `description`, `language`, `published`, `ordering` ) VALUES'
						.' ( '.(int)$insertIdGroup.', '.(int)$v3['imageid'].', '.(int)$data['type'].', '.$db->quote(strip_tags($v3['quantity'])).', '.$db->quote(strip_tags($v3['title'])).', '.$db->quote($price).', '.$db->quote($price2).', '.$db->quote(strip_tags($v3['description'])).', '.$db->quote($data['language']).', 1, '.(int)$j.')';
						$db->setQuery($q);
						$db->execute();
						$j++;
					}
				}
				$i++;
			}
		}



		// =======================
		// Weekly Menu, Bill of Fare, Beverage List, Wine List - 2, 3, 4, 5
		// =======================
		if ($data['type'] == 2 || $data['type'] == 3 || $data['type'] == 4 || $data['type'] == 5) {

			if ($data['type'] == 2) {
				if (!isset($dN['date_from']) || (isset($dN['date_from']) && $dN['date_from'] == '')) {
					$msg = JText::_('COM_PHOCAMENU_ERROR_NO_DATE_FROM_FOUND');
					$app->enqueueMessage($msg, 'error');
					return false;
				}
				if (!isset($dN['date_to']) || (isset($dN['date_to']) && $dN['date_to'] == '')) {
					$msg = JText::_('COM_PHOCAMENU_ERROR_NO_DATE_TO_FOUND');
					$app->enqueueMessage($msg, 'error');
					return false;
				}
			}

			if (empty($dN['daylist'][1])) {
				$msg = JText::_('COM_PHOCAMENU_ERROR_NO_DAY_FOUND');
				$app->enqueueMessage($msg, 'error');
				return false;
			}

			if (empty($dN['group'][1])) {
				$msg = JText::_('COM_PHOCAMENU_ERROR_NO_GROUP_FOUND');
				$app->enqueueMessage($msg, 'error');
				return false;
			}

			// CONFIG
			if ($data['type'] == 2) {
				$q = ' SELECT a.id FROM #__phocamenu_config AS a'
					.' WHERE type = '.(int)$data['type']
					.' AND language = '.$db->quote($data['language'])
					.' ORDER BY a.id';

				$db->setQuery($q);
				$config = $db->loadRow();

				if (isset($config[0]) && (int)$config[0] > 0) {
					$q = 'UPDATE #__phocamenu_config SET date_from = '.$db->quote(trim($data['date_from'])).' WHERE id = '.(int)$config[0];
					$db->setQuery($q);
					$db->execute();
					$q = 'UPDATE #__phocamenu_config SET date_to = '.$db->quote(trim($data['date_to'])).' WHERE id = '.(int)$config[0];
					$db->setQuery($q);
					$db->execute();
				} else {


					$q = ' INSERT INTO #__phocamenu_config ( `type`, `date_from`, `date_to`, `language`) VALUES'
						.' ('.(int)$data['type'].', '.$db->quote(trim($data['date_from'])).', '.$db->quote(trim($data['date_to'])).', '.$db->quote($data['language']).')';
					$db->setQuery($q);

					$db->execute();
					/*$q = ' INSERT INTO #__phocamenu_config ( `type`, `date_to`, `language`) VALUES'
						.' ('.(int)$data['type'].', '.$db->quote(trim($data['date_to'])).', '.$db->quote($data['language']).')';
					$db->setQuery($q);
					$db->execute();*/

				}
			}

			// CLEAN TABLES
			$q = 'DELETE FROM #__phocamenu_item WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			$db->setQuery($q);
			$db->execute();
			$q = 'DELETE FROM #__phocamenu_group WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			$db->setQuery($q);
			$db->execute();

			if ($data['type'] == 2) {
				$q = 'DELETE FROM #__phocamenu_day WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			} else {
				$q = 'DELETE FROM #__phocamenu_list WHERE type = '.(int)$data['type'].' AND '.$data['langwhere'];
			}
			$db->setQuery($q);
			$db->execute();


			$l = 0;// ordering day list
			$i = 0;// ordering group
			$j = 0;// ordering item

			foreach($dN['daylist'] as $k => $v) {

				$l++;

				if ($data['type'] == 2) {
					$q = ' INSERT INTO #__phocamenu_day ( `type`, `title`, `language`, `published`, `ordering`) VALUES'
						.' ('.(int)$data['type'].', '.$db->quote(strip_tags($v)).', '.$db->quote($data['language']).', 1, '.(int)$l.')';
				} else {
					$q = ' INSERT INTO #__phocamenu_list ( `type`, `title`, `language`, `published`, `ordering`) VALUES'
						.' ('.(int)$data['type'].', '.$db->quote(strip_tags($v)).', '.$db->quote($data['language']).', 1, '.(int)$l.')';
				}
				$db->setQuery($q);
				$db->execute();

				$insertIdDay = $db->insertid();


				if (!empty($dN['group'][$k])) {
					foreach($dN['group'][$k] as $k2 => $v2) {
						$i++;
						$data['message'] 	= '';
						if (isset($dN['message'][$k][$k2]) && $dN['message'][$k][$k2] != '') {
							$data['message'] = $dN['message'][$k][$k2];
						}

						// type, title, message
						$q = ' INSERT INTO #__phocamenu_group ( `catid`, `type`, `title`, `message`, `language`, `published`, `ordering`) VALUES'
							.' ('.(int)$insertIdDay.', '.(int)$data['type'].', '.$db->quote(strip_tags($v2)).', '.$db->quote($data['message']).', '.$db->quote($data['language']).', 1, '.(int)$i.')';


						$db->setQuery($q);
						$db->execute();

						$insertIdGroup = $db->insertid();


						if (!empty($dN['item'][$k][$k2])) {
							foreach($dN['item'][$k][$k2] as $k3 => $v3) {
								$j++;

								$price = PhocaMenuHelper::replaceCommaWithPoint(strip_tags($v3['price']));
								$price2 = PhocaMenuHelper::replaceCommaWithPoint(strip_tags($v3['price2']));

								$q = ' INSERT INTO #__phocamenu_item ( `catid`, `imageid`, `type`, `quantity`, `title`, `price`, `price2`,  `description`, `language`, `published`, `ordering` ) VALUES'
								.' ( '.(int)$insertIdGroup.', '.(int)$v3['imageid'].', '.(int)$data['type'].', '.$db->quote(strip_tags($v3['quantity'])).', '.$db->quote(strip_tags($v3['title'])).', '.$db->quote($price).', '.$db->quote($price2).', '.$db->quote(strip_tags($v3['description'])).', '.$db->quote($data['language']).', 1, '.(int)$j.')';

								$db->setQuery($q);
								$db->execute();

							}
						}
					}

				}
			}

		}


		return true;
	}

	protected function populateState($ordering = NULL, $direction = NULL) {

		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 		= JFactory::getApplication('administrator');
		$language 	= $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');


		$this->setState('filter.language', $language);
		/*if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}

		$app 		= JFactory::getApplication('administrator');
		$language	= PhocaMenuHelper::getLangAdmin();

		if ($language == '') {
			$language 	= $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		}
		$this->setState('filter.language', $language);*/
		//parent::populateState();
	}

	/* Load new content with selected language
	 * <input type="hidden" name="task" value="phocamenuconfig.edit" />
	*/
	public function setLangAndLoadContent($language) {
		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 		= JFactory::getApplication('administrator');
		$type		= JFactory::getApplication()->input->get('type', 0, '', 'int');

		$lang = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', $language);

	}
}
?>
