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
jimport('joomla.application.component.modeladmin');
use Joomla\String\StringHelper;

class PhocaMenuCpModelPhocaMenuAllItem extends AdminModel
{
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';

	protected function canDelete($record)
	{
		$user = Factory::getUser();

		if ($record->catid) {
			return $user->authorise('core.delete', 'com_phocamenu.phocamenuitem.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = Factory::getUser();

		if ($record->catid) {
			return $user->authorise('core.edit.state', 'com_phocamenu.phocamenuitem.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'phocamenuitem', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocamenu.phocamenuitem', 'phocamenuitem', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamenu.edit.phocamenuitem.data', array());

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
		$table->alias		= ApplicationHelper::stringURLSafe((string)$table->alias);

		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe((string)$table->title);
		}

		$table->price = PhocaMenuHelper::replaceCommaWithPoint($table->price);
		$table->price2 = PhocaMenuHelper::replaceCommaWithPoint($table->price2);



		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_item WHERE catid = '. (int) $table->catid . ' AND type = '. (int) $table->type);
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







	public function increaseOrdering($categoryId, $catType) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_item WHERE catid='.(int)$categoryId . ' AND type = '.(int)$catType);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}
}


?>
