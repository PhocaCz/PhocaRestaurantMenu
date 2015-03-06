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
defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');

class PhocaMenuCpModelPhocaMenuEmail extends JModelAdmin
{
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';
	
	
	protected function canEditState($record)
	{
		//$user = JFactory::getUser();
		return parent::canEditState($record);
	}
	
	public function getTable($type = 'PhocaMenuEmail', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) {
		
		$app	= JFactory::getApplication();
		
		$form 	= $this->loadForm('com_phocamenu.phocamenuemail', 'phocamenuemail', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_phocamenu.edit.phocamenuemail.data', array());
		
		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getItem($pk = null)
	{
		
		$type = JRequest::getVar('type', 0, '', 'int');
		$query = ' SELECT a.id '
			    .' FROM #__phocamenu_email AS a'
			    .' WHERE a.type ='.(int)$type
				.' LIMIT 1';
					
		// We need only one row, if we don't have one, we must add one -> $row->load($cid[0]) = $row->load(0)
		//						 if we have one, we must edit this one -> $row->load(1)	
		$this->_db->setQuery($query);//Try to find the first row 
		$itemEmail = $this->_db->loadObject();
		if (isset($itemEmail->id)) {
			$pk	= $itemEmail->id;
		} /*else {
			$this->_loadData();// not init data, because there are other items to load
		}*/
		
		
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			//$registry->loadString($item->metadata);
			//$item->metadata = $registry->toArray();
		}

		return $item;
	}
	
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_email');
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
			//$table->modified	= $date->toSql();
			//$table->modified_by	= $user->get('id');
		}
	}
	

	
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		//$condition[] = 'catid = '. (int) $table->catid;
		$condition[] = 'type = '. (int) $table->type;
		//$condition[] = 'state >= 0';		
		return $condition;
	}
	
	public function getBodyText() {
		
		/* Specific data from ADMINISTRATOR
		 * In administration you can display data from menu type or you can display one list(day) from menu type
		 * This is used for: PRINT PDF, PREVIEW, MULTIPLE EDIT, EMAIL (administration tools in lists or days)
		 */

		$type 		= PhocaMenuHelper::getUrlType('email');
		$adminTool 	= JRequest::getVar('admintool', 0, '', 'int');
		$atid		= JRequest::getVar( 'atid', 0, '', 'int' );
		$content	= array();
		
		$wheresLang = '';
		$content['itemlanguage'] = '';
		if ($this->getState('filter.language')) {
			//Possible old data
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
		$wheres[] 	= 'a.published = 1';
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
	
	protected function populateState($ordering = NULL, $direction = NULL) {

		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 		= JFactory::getApplication('administrator');
		$language 	= $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		
		
		$this->setState('filter.language', $language);
		
		parent::populateState();
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