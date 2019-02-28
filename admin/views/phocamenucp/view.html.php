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
jimport('joomla.html.pane');

class PhocaMenuCpViewPhocaMenuCp extends JViewLegacy
{
	protected $t;
	protected $views;

	public function display($tpl = null) {

		$this->t	= PhocaMenuUtils::setVars();
		$this->views= array(
		'groups&type=1'	=> array('dm', $this->t['l'] . '_DAILY_MENU'),
		
		'info'			=> array('info', $this->t['l'] . '_INFO')
		);


		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$class	= $this->t['n'] . 'Utils';
		$this->t['version'] = $class::getExtensionVersion();
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t['c']);
		JToolbarHelper::title( JText::_( $this->t['l'].'_PRM_CONTROL_PANEL' ), 'home-2 cpanel' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar =  JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocamenu" class="btn btn-small"><i class="icon-home" title="'.JText::_('COM_PHOCAMENU_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAMENU_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences($this->t['o']);
			JToolbarHelper::divider();
		}
		JToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
