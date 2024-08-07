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

defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

abstract class PhocaMenuBatch
{

	public static function item($published, $category = 0)
	{
		// Create the copy/move options.
		$options = array(
			HTMLHelper::_('select.option', 'c', Text::_('JLIB_HTML_BATCH_COPY')),
			HTMLHelper::_('select.option', 'm', Text::_('JLIB_HTML_BATCH_MOVE'))
		);

		$db = Factory::getDBO();

		$groupOptions =array();

		//$groupOptions[] = JHtml::_('select.option', '-1', JText::_('All Groups'));

		if ($category == 1) {
			$e = PhocaMenuHelper::isMenuEnabled();

			$groupOptions[] = HTMLHelper::_('select.option', '1,' . 0, '['.Text::_('COM_PHOCAMENU_DAILY_MENU').']');
			if ($e) {
				$groupOptions[] = HTMLHelper::_('select.option', '6,' . 0, '['.Text::_('COM_PHOCAMENU_BREAKFAST_MENU').']');
				$groupOptions[] = HTMLHelper::_('select.option', '7,' . 0, '['.Text::_('COM_PHOCAMENU_LUNCH_MENU').']');
				$groupOptions[] = HTMLHelper::_('select.option', '8,' . 0, '['.Text::_('COM_PHOCAMENU_DINNER_MENU').']');
			}

			$query = 'SELECT menulist.id as listid, menulist.title as listtitle, menulist.type as listtype'
					.' FROM #__phocamenu_list AS menulist'
					.' ORDER BY menulist.id';
			$db->setQuery($query);
			$lists = $db->loadObjectList();


			$query = 'SELECT menuday.id as dayid, menuday.title as daytitle, menuday.type as daytype'
					.' FROM #__phocamenu_day AS menuday'
					.' ORDER BY menuday.id';
			$db->setQuery($query);
			$days = $db->loadObjectList();


			foreach($days as $group){

				$title = '';
				$title .= '['.PhocaMenuHelper::getTitleByType((int)$group->daytype).'] - ';
				if (isset($group->daytitle) && $group->daytitle != '') {
					$title .= HTMLHelper::Date($group->daytitle, 'Y-m-d');
				}
				$title = str_replace('[np]', '', $title);
				$groupOptions[] = HTMLHelper::_('select.option', 'd,' . $group->dayid, $title);
			}
			foreach($lists as $group){

				$title = '';
				$title .= '['.PhocaMenuHelper::getTitleByType((int)$group->listtype).'] - ';
				if (isset($group->listtitle) && $group->listtitle != '') {
					$title .= $group->listtitle;
				}
				$title = str_replace('[np]', '', $title);
				$groupOptions[] = HTMLHelper::_('select.option', 'l,' . $group->listid, $title);
			}

		} else {

			$query = 'SELECT menugroup.id, menugroup.title, menugroup.type AS grouptype,'
					.' menuday.id as dayid, menuday.title as daytitle,'
					.' menulist.id as listid, menulist.title as listtitle'
					.' FROM #__phocamenu_group AS menugroup '
					.' LEFT JOIN #__phocamenu_day AS menuday ON menugroup.catid = menuday.id '
					.' LEFT JOIN #__phocamenu_list AS menulist ON menugroup.catid = menulist.id '
					.' LEFT JOIN #__phocamenu_item AS menuitem ON menuitem.catid = menugroup.id '
					.' GROUP BY menugroup.id'
					.' ORDER BY menugroup.id, menulist.id, menuday.id';
			$db->setQuery($query);
			$groups = $db->loadObjectList();


			$groupcount=0;
			foreach($groups as $group){

				$title = '';
				if (isset($group->grouptype) && (int)$group->grouptype > 0) {
					$title .= '['.PhocaMenuHelper::getTitleByType((int)$group->grouptype).'] - ';
				}
				if (isset($group->daytitle) && $group->daytitle != '') {
					$title .= HTMLHelper::Date($group->daytitle, 'Y-m-d') . " - " . $group->title;
				} else if (isset($group->listtitle) && $group->listtitle != '') {
					$title .= $group->listtitle . " - " . $group->title;
				} else {
					$title .= $group->title;
				}

				$title = str_replace('[np]', '', $title);

				$groupOptions[] = HTMLHelper::_('select.option', $group->id, $title);
				$groupcount++;
			}
		}

		/*$groupList=array();
		$groupList["title"]= Text::_('Group filter');
		$groupList["html"] = HTMLHelper::_('select.genericlist', $groupOptions, 'phocamenugroup_filter_value', 'class="form-control" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $this->filter_value );
		return $groupList;
*/

		// Create the batch selector to change select the category by which to move or copy.
		$lines = array(
			'<label id="batch-choose-action-lbl" for="batch-choose-action">',
			Text::_('COM_PHOCAMENU_SELECT_GROUP_MOVE_COPY'),
			'</label>',
			'<fieldset id="batch-choose-action" class="combo">',
				'<select name="batch[category_id]" class="form-control" id="batch-category-id">',
					/*'<option value="">'.JText::_('JSELECT').'</option>',
					/*JHtml::_('select.options',	JHtml::_('category.options', $extension, array('published' => (int) $published))),*/
					HTMLHelper::_('select.options',  $groupOptions ),
				'</select>',
				HTMLHelper::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'),
			'</fieldset>'
		);

		return implode("\n", $lines);
	}
}
