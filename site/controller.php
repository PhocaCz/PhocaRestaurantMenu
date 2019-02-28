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
jimport( 'joomla.application.component.controller' );

class PhocaMenuController extends JControllerLegacy
{
	
	public function display($cachable = false, $urlparams = false)
	{
		$paramsC 	= JComponentHelper::getParams('com_phocamenu');
		$cache 		= $paramsC->get( 'enable_cache', 0 );
		$cachable 	= false;
		if ($cache == 1) {
			$cachable 	= true;
		}
		
		$document 	= JFactory::getDocument();

		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');
			
			
		
		if ( ! JFactory::getApplication()->input->get('view') ) {
			JFactory::getApplication()->input->set('view', 'dailymenu' );
		}
		

		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JFactory::getApplication()->input->get( 'view', $this->getName() );
		$view =  $this->getView( $viewName, $viewType, '' );
		$view->setModel( $this->getModel( 'Menu' ), true );
		//$view->display();

		parent::display($cachable,$safeurlparams);

		return $this;
	}
	
	
}
?>