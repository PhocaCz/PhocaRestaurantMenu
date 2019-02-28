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
defined( '_JEXEC' ) or die();
jimport( 'joomla.application.component.modellist' );

class PhocaMenuCpModelPhocaMenuItems extends JModelList
{
	protected	$option 		= 'com_phocamenu';
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'quantity', 'a.quantity',
				'price', 'a.price',
				'price2', 'a.price2',
				'catid', 'a.catid',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'published','a.published'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{

		$app 	= JFactory::getApplication('administrator');
		$type 	= PhocaMenuHelper::getUrlType('item');
	

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		//$accessId = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		//$this->setState('filter.access', $accessId);

		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $state);
		
		// IMPORTANT - when $ GET and we get the ID by GET, then this all can be overriden in view.html.php
		// as here in model it does not take any effect
		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', 0);
		// Don't user populateState if we are returning back from Edit View (it can happen that here the categor will be changed
		$postCatid	= JFactory::getApplication()->input->get('filter_category_id', 0, 'POST', 'int');
		if ((int)$postCatid > 0) {
			$this->setState('filter.category_id', $categoryId);
		} else {
			$this->setState('filter.category_id', $type['valuecatid']);
		}
		

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_phocamenu');
		$this->setState('params', $params);

		// List state information.
		
		
		parent::populateState('a.title', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		
		//$type 	= PhocaMenuHelper::getUrlType('item');
		$id	.= ':'.$this->getState('filter.search');
		//$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.item_id');
		
		

		return parent::getStoreId($id);
	}
	
	
	protected function getListQuery()
	{
		$type 	= PhocaMenuHelper::getUrlType('item');
		
		$categoryId = $this->getState('filter.category_id');
		
		/*
		$query = 'SELECT a.*, cc.title AS category, u.name AS editor '
				.' FROM #__phocamenu_item AS a '
				.' LEFT JOIN #__phocamenu_group AS cc ON cc.id = a.catid '
				.' LEFT JOIN #__users AS u ON u.id = a.checked_out '
				. $where
				. $orderby;
		return $query;
		}
		*/
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		
		$query->from('`#__phocamenu_item` AS a');
		
		
		$query->where('a.type = '.(int)$type['value']);
		
		$query->select('c.title AS category_title, c.id AS category_id');
		$query->join('LEFT', '#__phocamenu_group AS c ON c.id=a.catid');
		
		// Filter Catid ($ POST) or Session or Catid ($ GET)
		$categoryId 		= $this->getState('filter.category_id');
		$actualCategoryId	= PhocaMenuHelper::getActualCategory('item', $type['value'], $categoryId);	
	
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $actualCategoryId);
		}

		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		//	$query->where('a.access = '.(int) $access);
		//}

		// Filter by published state.
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}
		else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}
		

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.title LIKE '.$search.'');
			}
		}
		
		$query->group('a.id');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__', 'jos_', $query->__toString()));
		return $query;
	}
}
?>