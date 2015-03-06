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
class PhocaMenuCpModelPhocaMenuMultipleEdit extends JModelAdmin
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

		$type 		= PhocaMenuHelper::getUrlType('multipleedit');
		$adminTool 	= JRequest::getVar('admintool', 0, '', 'int');
		$atid		= JRequest::getVar( 'atid', 0, '', 'int' );
		$content	= array();
		
		$wheresLang = '';
		$content['itemlanguage'] = '';
		if ($this->getState('filter.language')) {
			//Possible old format data
			if ($this->getState('filter.language') == '*') {
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
	
		$type 		= PhocaMenuHelper::getUrlType('multipleedit');
		//$adminTool 	= JRequest::getVar('admintool', 0, '', 'int');
		//$atid		= JRequest::getVar( 'atid', 0, '', 'int' );
		
		// Language 
		$lang		= $post['language'];
	
		// - - - - - - - - - - - - - - - -
		// Config
		// - - - - - - - - - - - - - - - -
		$row1 				= &$this->getTable('phocamenuconfig');		
		$data				= array();

		$data['language'] = $lang;
		
		$dataNotEmpty = 0;
		// Type 1
		if (isset($post['date']) && !empty($post['date'])) {
			// Config has only one row - the foreach can be run before database storing
			foreach($post['date'] as $key => $value) {
				$data['id']		= $key;
				$data['date']	= $value;
				
				if ($data['date'] != '') {
					$dataNotEmpty = 1;
				}
			}
		}
		
		// Type 2
		if (isset($post['date_from']) && isset($post['date_to']) && !empty($post['date_from'])  && !empty($post['date_to'])) {
			// Config has only one row - the foreach can be run before database storing
			foreach($post['date_from'] as $key => $value) {
				$data['id']			= $key;
				$data['date_from']	= $value;
				
				if ($data['date_from'] != '') {
					$dataNotEmpty = 1;
				}
			}
			
			foreach($post['date_to'] as $key => $value) {
				$data['id']			= $key;//id is the same as in date_from, can be overwritten if exists
				$data['date_to']	= $value;
				
				if ($data['date_to'] != '') {
					$dataNotEmpty = 1;
				}
			}
		}
		
		if ($dataNotEmpty == 1) {
		
			// Bind the form fields table
			if (!$row1->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// Make sure table is valid
			if (!$row1->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Store table to the database
			if (!$row1->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// - - - - - - - - - - - - - - - -
		// Day
		// - - - - - - - - - - - - - - - -
		$rowD 	= &$this->getTable('phocamenuday');
		if (isset($post['datesub']) && !empty($post['datesub'])) {
			foreach($post['datesub'] as $key => $value) {
		
				$data 				= array();
				$data['id']			= $key;
				$data['title']		= $value;
				$data['language']	= $lang;
				
				
				
				// Bind the form fields to table
				if (!$rowD->bind($data)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Make sure the table is valid
				if (!$rowD->check()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Store the table to the database
				if (!$rowD->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		
		// - - - - - - - - - - - - - - - -
		// LIST
		// - - - - - - - - - - - - - - - -
		$rowL 	= &$this->getTable('phocamenulist');
		if (isset($post['list']) && !empty($post['list'])) {
			foreach($post['list'] as $key => $value) {
				
				$data 			= array();
				$data['id']		= $key;
				$data['title']	= $value;
				$data['language']	= $lang;
				
				// Bind the form fields to table
				if (!$rowL->bind($data)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Make sure the table is valid
				if (!$rowL->check()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Store the table to the database
				if (!$rowL->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// - - - - - - - - - - - - - - - -
		// Group and Message
		// - - - - - - - - - - - - - - - -
		$rowG 	= &$this->getTable('phocamenugroup');
		if (isset($post['group']) && !empty($post['group'])) {
			foreach($post['group'] as $key => $value) {
				
				$data 			= array();
				$data['id']		= $key;
				$data['title']	= $value;
				$data['language']	= $lang;
				
				
				if (isset($post['message'][$key])) {
					$data['message']	= $post['message'][$key];
				}
				
				if (isset($post['groupheaderprice'][$key])) {
					$data['header_price'] 	= $post['groupheaderprice'][$key];
				}
				
				if (isset($post['groupheaderprice2'][$key])) {
					$data['header_price2'] 	= $post['groupheaderprice2'][$key];
				}
			
				
				// Bind the form fields to table
				if (!$rowG->bind($data)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Make sure the table is valid
				if (!$rowG->check()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Store the table to the database
				if (!$rowG->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

			}
		}

		// - - - - - - - - - - - - - - - -
		// Items
		// - - - - - - - - - - - - - - - -
		$rowI 		= &$this->getTable('phocamenuitem');
		
		if (isset($post['itemtitle']) && !empty($post['itemtitle'])) {
			foreach($post['itemtitle'] as $key => $value) {
				
				$data 			= array();
				$data['id']		= $key;
				$data['title']	= $value;
				$data['language']	= $lang;
				
				
				if (isset($post['itemquantity'][$key])) {
					$data['quantity']	= $post['itemquantity'][$key];
				}
				
				if (isset($post['itemprice'][$key])) {
					$data['price'] 	= $post['itemprice'][$key];
				}
				if (isset($post['itemprice2'][$key])) {
					$data['price2'] 	= $post['itemprice2'][$key];
				}
				
				if (isset($post['itemdesc'][$key])) {
					$data['description'] 	= $post['itemdesc'][$key];
				}
				
				if (isset($post['itempublish'][$key])) {
					$data['published'] 	= $post['itempublish'][$key];
				}
				
				
				
				// Bind the form fields to table
				if (!$rowI->bind($data)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Make sure the table is valid
				if (!$rowI->check()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				// Store table to the database
				if (!$rowI->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		
		// Items ADD
		if (isset($post['newitemtitle']) && !empty($post['newitemtitle'])) {
		
			
			foreach($post['newitemtitle'] as $keyCatid => $valueCatid) {

				if (isset($valueCatid) && !empty($valueCatid)) {
				
					// Security check, this situation should not appear as there is a js check (addrow.js)
					if (count($valueCatid) > 50) {
						$errorMsg = JText::_("COM_PHOCAMENU_ERROR_NOT_ADDED_NEW_ITEMS");
						return false;
					}
				
					foreach($valueCatid as $keyId => $valueId) {
						
						// User can create new input via JS but he can delete created row after creation (click on delete)
						if (isset($post['newitemdelete'][$keyCatid][$keyId])) {
							// Do Nothing
						} else {
						
							$data			= array();
							$data['id']		= NULL;
							$data['title']	= $valueId;
							$data['catid']	= $keyCatid;
							$data['type']	= $type['value'];
							$data['language']	= $lang;
							
							if (isset($post['newitemquantity'][$keyCatid][$keyId])) {
								$data['quantity']	= $post['newitemquantity'][$keyCatid][$keyId];
							}
							
							if (isset($post['newitemprice'][$keyCatid][$keyId])) {
								$data['price'] 	= $post['newitemprice'][$keyCatid][$keyId];
							}
							
							if (isset($post['newitemdesc'][$keyCatid][$keyId])) {
								$data['description'] 	= $post['newitemdesc'][$keyCatid][$keyId];
							}
							
							if (isset($post['newitempublish'][$keyCatid][$keyId])) {
								$data['published'] 	= $post['newitempublish'][$keyCatid][$keyId];
							}
							
							$rowI 		= &$this->getTable('phocamenuitem');
							// Bind the form fields to table
							if (!$rowI->bind($data)) {
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
							
							// if new item, order last in appropriate group
							if (!$rowI->id) {
								$where = 'catid = ' . (int) $rowI->catid ;
								$rowI->ordering = $rowI->getNextOrder( $where );
							}

							// Make sure the table is valid
							if (!$rowI->check()) {
								$this->setError($this->_db->getErrorMsg());
								return false;
							}

							// Store table to the database
							if (!$rowI->store()) {
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
						}
					}
				}
			}
		}

		// Items REMOVE
		$rowI 			= &$this->getTable('phocamenuitem');
		$itemToDelete 	= array();
		if (isset($post['itemdelete']) && !empty($post['itemdelete'])) {
			foreach($post['itemdelete'] as $key => $value) {
				$itemToDelete[]	= $key;
			}
		}

		$db =& JFactory::getDBO();
		if (count( $itemToDelete )) {
			$cids = implode( ',', $itemToDelete );
			$query = 'DELETE FROM #__phocamenu_item'
			. ' WHERE id IN ( '.$cids.' )';
			$db->setQuery( $query );
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
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
		$type		= JRequest::getVar('type', 0, '', 'int');
		
		$lang = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', $language);

	}
}
?>