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
jimport('joomla.application.component.modeladmin');

class PhocaMenuCpModelPhocaMenuConfig extends AdminModel
{
	protected	$option 		= 'com_phocamenu';
	protected 	$text_prefix	= 'com_phocamenu';


	protected function canEditState($record)
	{
		//$user = JFactory::getUser();
		return parent::canEditState($record);
	}

	public function getTable($type = 'PhocaMenuConfig', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();

		$form 	= $this->loadForm('com_phocamenu.phocamenuconfig', 'phocamenuconfig', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamenu.edit.phocamenuconfig.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

		public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
            if (isset($item->metadata)) {
                $registry = new Registry;
                $registry->loadString($item->metadata);
                $item->metadata = $registry->toArray();
            }
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

		if ($table->date_from == '0') {
			$table->date_from = '00-00-00 00:00:00';
		}
		if ($table->date_to == '0') {
			$table->date_to = '00-00-00 00:00:00';
		}

		if ($table->date == '0') {
			$table->date = '00-00-00 00:00:00';
		}


		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamenu_config');
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

	protected function populateState($ordering = NULL, $direction = NULL) {

		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 		= Factory::getApplication('administrator');
		$type		= $app->input->get('type', 0, 'int');
		$language 	= $app->getUserStateFromRequest($this->context.'.filter.language'.(int)$type, 'filter_language', '');


		$this->setState('filter.language'.(int)$type, $language);

		parent::populateState();
	}

	/* Load new content with selected language
	 * <input type="hidden" name="task" value="phocamenuconfig.edit" />
	*/
	public function setLangAndLoadContent($language) {
		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 		= Factory::getApplication('administrator');
		$type		= $app->input->get('type', 0, 'int');

		$lang = $app->getUserStateFromRequest($this->context.'.filter.language'.(int)$type, 'filter_language', $language);

	}
}


?>
