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
jimport('joomla.application.component.view');
class PhocaMenuFrontViewPdf extends JViewLegacy
{
	public $button;
	protected $params;
	protected $paramsg;
	protected $tmpl;
	protected $data;
	
	function display($tpl = null) {
		
		$app				= JFactory::getApplication();		
		$this->params		= $app->getParams();
		$model 				= $this->getModel('Menu');
		$type				= PhocaMenuHelper::getTypeByView($this->_name);
		$this->data			= $model->getData($type);
		
		
		// Params
		$this->tmpl['dateclass']		= $this->params->get( 'date_class', 0 );
		$this->tmpl['customclockcode']	= '';
		$this->tmpl['daydateformat']	= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->tmpl['daydateformat']	= $this->params->get( 'day_date_format', 'l, d. F Y' );
		$this->tmpl['weekdateformat']	= $this->params->get( 'week_date_format', 'l, d. F Y' );
		$this->tmpl['priceprefix']		= $this->params->get( 'price_prefix', '...' );
		$this->tmpl['phocagallery']		= 0;
		$this->paramsg					= array();
		$this->button					= '';
		
		switch($type) {
			case 2:		$outputFnc = 'renderWeeklyMenu';	break;
			
			case 4:		$outputFnc = 'renderBeverageList';	break;
			
			case 3:
			case 5:		$outputFnc = 'renderCommonListMenu'; break;
			
			case 6:
			case 7:
			case 8:		$outputFnc = 'renderCommonMenu';	break;
			
			case 1:	
			default:	$outputFnc = 'renderDailyMenu';		break;
		}
		$outputFnc 	= (string)$outputFnc;
		$output 	=  PhocaMenuRenderViews::$outputFnc($this->data, $this->tmpl, $this->params, $this->paramsg, 2);
		echo $output;
	}
}
?>