<?php
class PhocaMenuCzechDate
{
	var $_month	= array();
	var $_day 	= array();
	
	public static function display($date = '') {
		$this->_month = array ( 'leden'		=> 'ledna',
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
		
		$this->_day = array ( 	'01.'	=> '1.',
								'02.'	=> '2.',
								'03.'	=> '3.',
								'04.'	=> '4.',
								'05.'	=> '5.',
								'06.'	=> '6.',
								'07.'	=> '7.',
								'08.'	=> '8.',
								'09.'	=> '9.');
				
		foreach ($this->_month as $key => $value) {

			$date = str_replace($key, $value, JString::strtolower($date));
		}
		
		foreach ($this->_day as $key2 => $value2) {
			$date = str_replace($key2, $value2, $date);
		}
		return JString::ucfirst($date);
	}
}
?>