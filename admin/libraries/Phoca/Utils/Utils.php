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

namespace Phoca\Utils;

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;

class Utils
{

	public static function getAliasName($alias) {

		if (Factory::getConfig()->get('unicodeslugs') == 1) {
			$alias= OutputFilter::stringURLUnicodeSlug($alias);
		} else {
			$alias = OutputFilter::stringURLSafe((string)$alias);
		}

		if (trim(str_replace('-', '', $alias)) == '') {
			$alias = Factory::getDate()->format("Y-m-d-H-i-s");
		}
		return $alias;
	}

	public static function replaceCommaWithPoint($item) {

		$item = self::getDecimalFromString($item);
		return str_replace(',', '.', $item);

	}

	public static function getDecimalFromString($string) {

		if (empty($string)) {
			return '0.0';
		}

		return $string;
	}


}
?>
