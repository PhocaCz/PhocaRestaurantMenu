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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
jimport('joomla.application.component.modeladmin');
use Joomla\String\StringHelper;

class PhocaMenuCpModelPhocaMenuGroup extends AdminModel
{
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';

	protected function canDelete($record)
	{
		$user = Factory::getUser();

		if ($record->catid) {
			return $user->authorise('core.delete', 'com_phocamenu.phocamenugroup.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = Factory::getUser();

		if ($record->catid) {
			return $user->authorise('core.edit.state', 'com_phocamenu.phocamenugroup.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'PhocaMenuGroup', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocamenu.phocamenugroup', 'phocamenugroup', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamenu.edit.phocamenugroup.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

		public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new Registry;
			//$registry->loadString($item->metadata);
			//$item->metadata = $registry->toArray();
		}

		return $item;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_group WHERE catid = '. (int) $table->catid . ' AND type = '. (int) $table->type);
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
		$condition[] = 'catid = '. (int) $table->catid;
		$condition[] = 'type = '. (int) $table->type;
		//$condition[] = 'state >= 0';
		return $condition;
	}

	function delete(&$cid = array()) {

		$db 	= Factory::getDBO();
		$result = false;

		// - - - - - - - - - - - - - - -
		// FIRST - Are there some items in the group?
		if (count( $cid )) {
			ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );


			// Select id's from tables. If the group has some items, don't delete it
			$query = 'SELECT g.id, g.title, COUNT( i.catid ) AS numcat'
			. ' FROM #__phocamenu_group AS g'
			. ' LEFT JOIN #__phocamenu_item AS i ON i.catid = g.id'
			. ' WHERE g.id IN ( '.$cids.' )'
			. ' GROUP BY g.id';

			$db->setQuery( $query );

			if (!($rows = $db->loadObjectList())) {
				throw new Exception(Text::_('COM_PHOCAMENU_ERROR_DB_LOAD_DATA'), 500);
				return false;
			}

			$errItem = array();
			$cid 	 = array();
			foreach ($rows as $row) {
				if ($row->numcat == 0) {
					$cid[] = (int) $row->id;
				} else {
					$errItem[] = $row->title;
				}
			}

			if (count( $cid )) {
				$cids = implode( ',', $cid );
				$query = 'DELETE FROM #__phocamenu_group'
				. ' WHERE id IN ( '.$cids.' )';
				$db->setQuery( $query );
				if (!$db->execute()) {
					throw new Exception($db->getError());
					return false;
				}
			}
		}

		// There are some items in the category - don't delete it
		$msg = '';
		if (count( $errItem )) {
			$cidsItem = implode( ", ", $errItem );
			$msg 	 .= Text::sprintf( 'COM_PHOCAMENU_WARNING_GROUP_CONTAIN_ITEMS', $cidsItem );

			$this->setError($msg);
			return false;
		}
		return true;
	}

	protected function batchCopy($value, $pks, $contexts)
	{
		$tableN 	= '';
		$typeN		= '';
		$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_MENU_TYPE_NOT_FOUND');
		if (isset($value) && $value != '') {
			$vA = explode(',', $value);
			if (isset($vA[0])) {
				if ($vA[0] == 'd') {
					$tableN = 'Day';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_DAY_NOT_FOUND');
				} else if ($vA[0] == 'l') {
					$tableN 	= 'List';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_LIST_NOT_FOUND');

				} else {
					// Groups are directly in type - they don't have any day or list: e.g. Daily Menu
					// Wine List 	==> l,3 means it is wine list (lists) and the list id = 3
					// Weekly Menu 	==> d,3 means it is weekly menu (days) and the day id = 3
					// Daily Menu	==> 1,0 means it is daily menu (type = 1) and not parent id
					// Lunch Menu	==> 7,0 means it is lunch menu (type = 7) and not parent id
					if ((int)$vA[0] > 0) {
						$typeN	= (int)$vA[0];
					}
					$tableN 	= '';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_MENU_TYPE_NOT_FOUND');
				}
			}
			if (isset($vA[1])) {
				if ((int)$vA[1] > 0) {
					$value = (int)$vA[1];
				} else {
					$value = 0;
				}
			}


		}

		$categoryId	= (int) $value;

		$table	= $this->getTable();
		$db		= $this->getDbo();
		//NEW
		$i		= 0;
		//ENDNEW


		if ($tableN != '') {
			// ALL TYPES WITH DAYS OR LISTS
			// Check that the category exists
			if ($categoryId) {
				$categoryTable = Table::getInstance('PhocaMenu'.(string)$tableN, 'Table');
				if (!$categoryTable->load($categoryId)) {
					if ($error = $categoryTable->getError()) {
						// Fatal error
						$this->setError($error);
						return false;
					}
					else {

						$this->setError($errMove);
						return false;
					}
				}
			}

			//if (empty($categoryId)) {
			if (!isset($categoryId)) {
				$this->setError($errMove);
				return false;
			}

			// PHOCAEDIT - get new type
			$catType = PhocaMenuHelper::getTypebyCategory((int)$categoryId, $tableN);
		} else {
			// TYPES WITHOUT DAYS AND LISTS
			$catType 	= (int)$typeN;
			$categoryId = 0;
		}




		// Check that the user has create permission for the component
		$extension	= Factory::getApplication()->input->get('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title   = $data['0'];
			$table->alias   = $data['1'];

			// Reset the ID because we are making a copy
			$table->id		= 0;

			// New category ID
			$table->catid	= (int)$categoryId;
			$table->type	= (int)$catType;

			// Ordering
			$table->ordering = $this->increaseOrdering($categoryId, $catType);

			//$table->hits = 0;

			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			//NEW
			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$pk]	= $newId;
			$i++;
			//ENDNEW
		}

		// Clean the cache
		$this->cleanCache();

		//NEW
		return $newIds;
		//END NEW
	}

	/**
	 * Batch move articles to a new category
	 *
	 * @param   integer  $value  The new category ID.
	 * @param   array    $pks    An array of row IDs.
	 *
	 * @return  booelan  True if successful, false otherwise and internal error is set.
	 *
	 * @since	11.1
	 */
	protected function batchMove($value, $pks, $contexts)
	{


		$tableN 	= '';
		$typeN		= '';

		$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_MENU_TYPE_NOT_FOUND');
		if (isset($value) && $value != '') {
			$vA = explode(',', $value);
			if (isset($vA[0])) {
				if ($vA[0] == 'd') {
					$tableN = 'Day';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_DAY_NOT_FOUND');
				} else if ($vA[0] == 'l') {
					$tableN 	= 'List';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_LIST_NOT_FOUND');

				} else {
					// Groups are directly in type - they don't have any day or list: e.g. Daily Menu
					// Wine List 	==> l,3 means it is wine list (lists) and the list id = 3
					// Weekly Menu 	==> d,3 means it is weekly menu (days) and the day id = 3
					// Daily Menu	==> 1,0 means it is daily menu (type = 1) and not parent id
					// Lunch Menu	==> 7,0 means it is lunch menu (type = 7) and not parent id
					if ((int)$vA[0] > 0) {
						$typeN	= (int)$vA[0];
					}
					$tableN 	= '';
					$errMove	= Text::_('COM_PHOCAMENU_ERROR_BATCH_MOVE_MENU_TYPE_NOT_FOUND');
				}
			}
			if (isset($vA[1])) {
				if ((int)$vA[1] > 0) {
					$value = (int)$vA[1];
				} else {
					$value = 0;
				}
			}


		}

		$categoryId	= (int) $value;

		$table	= $this->getTable();
		//$db		= $this->getDbo();

		if ($tableN != '') {
			// ALL TYPES WITH DAYS OR LISTS

			// Check that the category exists


			if ($categoryId) {
				$categoryTable = Table::getInstance('PhocaMenu'.(string)$tableN, 'Table');
				if (!$categoryTable->load($categoryId)) {
					if ($error = $categoryTable->getError()) {
						// Fatal error
						$this->setError($error);
						return false;
					}
					else {

						$this->setError($errMove);
						return false;
					}
				}
			}

			if (empty($categoryId)) {
				$this->setError($errMove);
				return false;
			}

			// PHOCAEDIT - get new type
			$catType = PhocaMenuHelper::getTypebyCategory((int)$categoryId, $tableN);

		} else {
			// TYPES WITHOUT DAYS AND LISTS
			$catType 	= (int)$typeN;
			$categoryId = 0;
		}

		// Check that user has create and edit permission for the component
		$extension	= Factory::getApplication()->input->get('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		if (!$user->authorise('core.edit', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// New category ID
			$table->catid	= (int)$categoryId;
			$table->type	= (int)$catType;

			// Ordering
			$table->ordering = $this->increaseOrdering($categoryId, $catType);


			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			//Everything stored - change type for all items
			if (!$this->setTypeItems($table->id, $table->type)) {
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	public function batch($commands, $pks, $contexts)
	{

		// Sanitize user ids.
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			$this->setError(Text::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['assetgroup_id'])) {
			if (!$this->batchAccess($commands['assetgroup_id'], $pks)) {
				return false;
			}

			$done = true;
		}


		//PHOCAEDIT - because parent it is 0
		//if (!empty($commands['category_id'])) {
		if (isset($commands['category_id']))
		{
			$cmd = ArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['category_id'], $pks, $contexts);
				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['category_id'], $pks, $contexts))
			{
				return false;
			}
			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}



	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias - we use title in Phoca Menu
		$table = $this->getTable();
		//while ($table->load(array('alias'=>$title, 'catid'=> $category_id))) {
		while ($table->load(array('title'=>$title, 'catid'=> $category_id))) {

			$m = null;
			if (preg_match('#-(\d+)$#', $alias, $m)) {
				$alias = preg_replace('#-(\d+)$#', '-'.($m[1] + 1).'', $alias);
			} else {
				$alias .= '-2';
			}
			if (preg_match('#\((\d+)\)$#', $title, $m)) {
				$title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
			} else {
				$title .= ' (2)';
			}

		}

		return array($title, $alias);
	}


	public function increaseOrdering($categoryId, $catType) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_group WHERE catid='.(int)$categoryId . ' AND type = '.(int)$catType);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}

	private function setTypeItems($id, $type) {

		$query = 'UPDATE #__phocamenu_item'
			.' SET type = ' . $this->_db->Quote($type)
			.' WHERE catid = '.(int) $id;
		$this->_db->setQuery($query);
		$this->_db->execute();
		/*if (!$this->_db->execute()) {
			$this->setError('Database Error Changing Item Types');
			return false;
		}*/

		return true;
	}
}

?>
