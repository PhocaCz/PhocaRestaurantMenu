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
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

class TablePhocaMenuConfig extends Table
{
	function __construct( &$db ) {
		parent::__construct( '#__phocamenu_config', 'id', $db );
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new Registry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new Registry();
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}

	public function check()
	{
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = ApplicationHelper::stringURLSafe((string)$this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = Factory::getDate()->format("Y-m-d-H-i-s");
		}

		/*$registry = new JRegistry;
        if (isset($item->metadata)) {

		    $registry->loadString($this->metadata);
		    $this->metadata = $registry->toArray();
        }
		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metadata['metakey'])) {
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = StringHelper::str_ireplace($bad_characters, "", $this->metadata['metakey']); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();
			foreach($keys as $key) {
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metadata['metakey'] = implode(", ", $clean_keys); // put array back together delimited by ", "
		}
		$registry->loadArray($this->metadata);
		$this->metadata = (string)$registry;
		*/
		$this->metadata = '';

		return true;
	}
}
?>
