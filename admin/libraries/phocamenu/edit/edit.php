<?php
/**
 * @package   Phoca Component
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Phoca\Text\Text as PhocaText;
use Phoca\Utils\Utils;
use Joomla\CMS\Date\Date;


defined( '_JEXEC' ) or die( 'Restricted access' );
class PhocamenuEdit
{
	public static function store(&$options) {

		$option     = 'com_phocamenu';
		$lng		= strtoupper($option);

		$user 		= Factory::getUser();
		$canCreate  = $user->authorise('core.create', $option);
        $canEdit    = $user->authorise('core.edit', $option);


        $paramsC = ComponentHelper::getParams($option);
        $admin_eip_title 	= $paramsC->get('admin_eip_title', 3);
        $comma_point		= $paramsC->get( 'comma_point', 0 );


        if ($canCreate || $canEdit) {
		} else {
        	$options['msg'] = Text::_($lng . '_NO_RIGHTS_EDIT_ITEMS');
        	return false;
		}





		$idA = explode(':', $options['id']);//table:column:id

		// Test Date value
		// type date ... Y-m-d
		// tpye datetime ... Y-m-d H:i:s
		if ($options['type'] == 'date') {

			// We need to convert date type to database type and check it before saving
			$options['value'] = $options['value'] . " 00:00:00";

			$format = 'Y-m-d H:i:s';// Database date format
    		$date = DateTime::createFromFormat($format, $options['value']);


    		if ($date && $date->format($format) == $options['value']) {

			} else {
    			$options['msg'] = Text::_($lng. '_WRONG_DATE_OR_DATE_FORMAT');
    			return false;
			}

		}


		$tableDb 	= '';// No direct access to table - this is why tables are listed here
		$tableDbName = '';
		$column = '';// No direct access to column - this is why columns are listed here
		$allowedTables = array(
			'#__phocamenu_group' => 'PhocaMenuGroup',
			'#__phocamenu_item' => 'PhocaMenuItem',
			'#__phocamenu_list' => 'PhocaMenuList',
			'#__phocamenu_day' => 'PhocaMenuDay',
		);
		$allowedColumns = array(
			'title', 'price', 'price2', 'quantity'
		);


		// Alias can be edited
		if ($admin_eip_title == 3 || $admin_eip_title == 4) {
			$allowedColumns[] = 'alias';
		}


		$requiredColumns = array(
			'title', 'alias'
		);
		/* This can be specified for different tables
		 * if ($tableDb == 'products') {
			$requiredColumns = array(
				'title', 'alias'
			);
		}*/


		if (isset($idA[0])) {
			$tableDbTest = '#__phocamenu_'. PhocaText::filterValue($idA[0], 'alphanumeric2');
			if (array_key_exists ($tableDbTest, $allowedTables)) {
				$tableDb = $tableDbTest;
				$tableDbName = $allowedTables[$tableDbTest];
			}
		}




		if (isset($idA[1])) {
			$columnTest = $idA[1];
			if (in_array($columnTest, $allowedColumns)) {
				$column = PhocaText::filterValue($columnTest, 'alphanumeric2');
			}

			if (in_array($columnTest, $requiredColumns)) {
				if ($options['value'] == '') {
					$options['msg'] = Text::_($lng. '_VALUE_CANNOT_BE_EMPTY');
        			return false;
				}

			}
		}



		switch($column) {

			case 'price':
			case 'price2':
			case 'price_original':
			case 'exchange_rate':
			case 'tax_rate':
			case 'discount':
            case 'cost':
				$options['value'] = $comma_point == 1 ? Utils::replaceCommaWithPoint($options['value']) : $options['value'];
				$options['value'] = (float)$options['value'];
			break;
			case 'stock':
				$options['value'] = (int)$options['value'];
			break;

			case 'title':
			case 'alias':
				$options['value'] = strip_tags($options['value']);
			break;

			// Date value is tested at top

		}

		if ($tableDb == '') {
			$options['msg'] = Text::_($lng.'_TABLE_EMPTY_OR_NOT_ALLOWED');
			return false;
		}

		if ($column == '') {
			$options['msg'] = Text::_($lng.'_COLUMN_EMPTY_OR_NOT_ALLOWED');
			return false;
		}


		if ($tableDbName != '' && $tableDb != '' && $column != '' && isset($idA[2]) && (int)$idA[2] > 0) {

			$idRow = (int)$idA[2];


			// TEST CHECKOUT
			$user = JFactory::getUser();

			// Get an instance of the row to checkout.
			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$option.'/tables');
			$table = Table::getInstance($tableDbName, 'Table');

			if (!$table->load($idRow)) {
				$options['msg'] = $table->getError();
				//throw new RuntimeException($tableDb->getError());
				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id')) {
				$options['msg'] = Text::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH');
				//throw new RuntimeException(JText::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH'));
				return false;
			}

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $idRow)) {
				$options['msg'] = $table->getError();
				//throw new RuntimeException($tableDb->getError());
				return false;
			}

			// DATA
			$data = array();
			$db	= Factory::getDBO();
			$data[$column]  = $options['value'];

			if ($column == 'title') {
				// Update even alias it this is set in options
				// Alias can be overwritten by title
				if ($admin_eip_title == 2 || $admin_eip_title == 4) {

					$options['valuecombined'] = strip_tags(Utils::getAliasName($options['value']));
					if (isset($idA[0])) {
						$options['idcombined'] = strip_tags($idA[0]) . ':alias:' . (int)$idRow;
					}
					$data['alias'] = $options['valuecombined'];
				}
			}

			// After saving the item will be free
			$data['checked_out'] = 0;
			$data['checked_out_time'] = '0000-00-00 00:00:00';

			if (!$table->bind($data)) {
				$options['msg'] = $table->getError();
				return false;
			}

			if (!$table->check()) {
				$options['msg'] = $table->getError();
				return false;
			}

			if (!$table->store()) {
				$options['msg'] = $table->getError();
				return false;
			}



			// RETURN SECOND VALUE
			// For example date - return the date to the edited field
			// But return even its formatted variation e.g. below the field

			if ($options['type'] == 'date' && isset($idA[0]) && $idA[0] != '' && isset($idA[2]) && (int)$idA[2] > 0) {

				// Which value we set
				$options['valuecombined'] = HTMLHelper::Date($options['value'],Text::_($options['dateformat']));
				// To which ID
				$options['idcombined'] = $idA[0]. ':dateformat:' . (int)$idA[2];

				// Remove database date when returning back the value
				$options['value'] = str_replace(' 00:00:00', '', $options['value']);
			}


			/*
			$db	= JFactory::getDBO();
			$q	= 'UPDATE '.$tableDb.' SET '.$db->quoteName($column).' = '.$db->quote($options['value']).' WHERE id = '.(int)$idRow;

			$db->setQuery($q);
			$db->execute();

			if ($column == 'title') {

				// Update even alias
				$column = 'alias';
				$options['valuecombined'] = strip_tags(PhocacartUtils::getAliasName($options['value']));
				if (isset($idA[0])) {
					$options['idcombined'] = strip_tags($idA[0]).':alias:' . (int)$idRow;
				}
				$q	= 'UPDATE '.$tableDb.' SET '.$db->quoteName($column).' = '.$db->quote($options['valuecombined']).',  WHERE id = '.(int)$idRow;

				$db->setQuery($q);
				$db->execute();
			}
			*/


			return true;
		} else {
			$options['msg'] = Text::_($lng.'_TABLE_OR_COLUMN_EMPTY');
		}
		return false;
	}
}
?>
