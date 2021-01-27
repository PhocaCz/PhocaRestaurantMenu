<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class PhocaMenuCpViewPhocaMenuInfo extends JViewLegacy
{
	protected $t;
	protected $r;

	function display($tpl = null) {

		$this->t	= PhocaMenuUtils::setVars();

		$this->r		= new PhocaMenuRenderAdminView();
		$this->t['component_head'] 	= $this->t['l'].'_PHOCA_MENU';
		$this->t['component_links']	= $this->r->getLinks(1);
		//JHTML::_('behavior.tooltip');
		$class	= $this->t['n'] . 'Utils';
		$this->t['version'] = $class::getExtensionVersion();
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t['c']);

		JToolbarHelper::title( JText::_($this->t['l'].'_PRM_INFO' ), 'info' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar =  JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocamenu" class="btn btn-small"><i class="icon-home" title="'.JText::_('COM_PHOCAMENU_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAMENU_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_'.$this->t['c']);
		}
		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
