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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.html.category');
phocagalleryimport('phocagallery.html.categoryhtml');


class JFormFieldPhocaMenuGalleryCategory extends FormField
{
	protected $type 		= 'PhocaMenuGalleryCategory';

	protected function getInput() {

		$db = Factory::getDBO();
		$ignorePublished = 0;

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocagallery_categories AS a';
		if ($ignorePublished == 0) {
			$query .= ' WHERE a.published = 1';
		}
		$query .= ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$phocagallerys = $db->loadObjectList();

		$catId	= -1;

		//$javascript 	= 'class="form-control" size="1" onchange="Joomla.submitform( );"';

		$attr = '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= ' class="form-control"';

		$tree = array();
		$text = '';
		$tree = PhocaGalleryCategoryhtml::CategoryTreeOption($phocagallerys, $tree, 0, $text, $catId);
		array_unshift($tree, HTMLHelper::_('select.option', '', '- '.Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY').' -', 'value', 'text'));
		return HTMLHelper::_('select.genericlist',  $tree,  $this->name, trim($attr), 'value', 'text', $this->value, $this->id );
	}
}
?>
