<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.controller' );

class PhocaMenuController extends BaseController
{
	
	public function display($cachable = false, $urlparams = false)
	{
		$paramsC 	= ComponentHelper::getParams('com_phocamenu');
		$cache 		= $paramsC->get( 'enable_cache', 0 );
		$cachable 	= false;
		if ($cache == 1) {
			$cachable 	= true;
		}
		
		$document 	= Factory::getDocument();

		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');
			
			
		
		if ( ! Factory::getApplication()->input->get('view') ) {
			Factory::getApplication()->input->set('view', 'dailymenu' );
		}
		

		$document	= Factory::getDocument();
		$viewType	= $document->getType();
		$viewName	= Factory::getApplication()->input->get( 'view', $this->getName() );
		$view =  $this->getView( $viewName, $viewType, '' );
		$view->setModel( $this->getModel( 'Menu' ), true );
		//$view->display();

		parent::display($cachable,$safeurlparams);

		return $this;
	}
	
	
}
?>