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

use Joomla\String\StringHelper;

class PhocaMenuCzechDate
{
	//var $_month	= array();
	//var $_day 	= array();
	
	public static function display($date = '') {
		$month = array ( 'leden'		=> 'ledna',
								'únor'		=> 'února',
								'březen'	=> 'března',
								'duben'		=> 'dubna',
								'květen'	=> 'května',
								'červen'	=> 'června',
								'červenec'	=> 'července',
								'srpen'		=> 'srpna',
								'září'		=> 'září',
								'říjen'		=> 'října',
								'listopad'	=> 'listopadu',
								'prosinec'	=> 'prosince',
								
								'červnace'	=> 'července',
								'červnaec'	=> 'července',
								'únoraa'	=> 'února',
								'listopaduu'=> 'listopadu');
		
		$day = array ( 	'01.'	=> '1.',
								'02.'	=> '2.',
								'03.'	=> '3.',
								'04.'	=> '4.',
								'05.'	=> '5.',
								'06.'	=> '6.',
								'07.'	=> '7.',
								'08.'	=> '8.',
								'09.'	=> '9.');
				
		foreach ($month as $key => $value) {

			$date = str_replace($key, $value, StringHelper::strtolower($date));
		}
		
		foreach ($day as $key2 => $value2) {
			$date = str_replace($key2, $value2, $date);
		}
		return StringHelper::ucfirst($date);
	}
}
?>