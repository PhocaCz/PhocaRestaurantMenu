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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Phoca\PhocaMenu\MVC\Model\AdminModelTrait;
jimport('joomla.application.component.modeladmin');

class PhocaMenuCpModelphocamenuday extends AdminModel
{
	use AdminModelTrait;
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';

	protected function canDelete($record)
	{
		//$user = JFactory::getUser();
		return parent::canDelete($record);
	}

	protected function canEditState($record)
	{
		//$user = JFactory::getUser();
		return parent::canEditState($record);
	}

	public function getTable($type = 'PhocaMenuDay', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocamenu.phocamenuday', 'phocamenuday', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamenu.edit.phocamenuday.data', array());

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

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_day WHERE type = '. (int) $table->type);
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

	function delete(&$cid = array()) {

		$db = Factory::getDBO();
		$result = false;

		// - - - - - - - - - - - - - - -
		// FIRST - Are there some groups in the list?
		if (count( $cid )) {

			ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			// Select id's from tables. If the group has some items, don't delete it
			$query = 'SELECT d.id, d.title, COUNT( g.catid ) AS numcat'
			. ' FROM #__phocamenu_day AS d'
			. ' LEFT JOIN #__phocamenu_group AS g ON g.catid = d.id'
			. ' WHERE d.id IN ( '.$cids.' )'
			. ' GROUP BY d.id';

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
				$query = 'DELETE FROM #__phocamenu_day'
				. ' WHERE id IN ( '.$cids.' )';
				$db->setQuery( $query );
				if (!$db->execute()) {
					throw new Exception($db->getError());
					return false;
				}
			}
		}

		// There are some groups in day - don't delete it
		$msg = '';
		if (count( $errItem )) {
			$cidsItem = implode( ", ", $errItem );
			$msg 	 .= Text::sprintf( 'COM_PHOCAMENU_WARNING_DAY_CONTAIN_GROUPS', $cidsItem );

			$this->setError($msg);
			return false;
		}
		return true;
	}
}

?>
