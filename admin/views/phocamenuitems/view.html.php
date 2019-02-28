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

class PhocaMenuCpViewPhocaMenuItems extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $type;
	protected $typeup;

	function display($tpl = null) {

		$this->t			= PhocaMenuUtils::setVars('item');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->type			= PhocaMenuHelper::getUrlType('item');

		// Override the model - populateState in case we really want exactly group/item/list
		if ($this->type['method'] == 'get') {
			$this->state->set('filter.category_id', $this->type['valuecatid']);
		}

		foreach ($this->items as &$item) {
			$this->ordering[$item->catid][] = $item->id;
		}

		JHTML::stylesheet( $this->t['s'] );

		// Breadcrumbs
		$this->typeup 	= PhocaMenuHelper::getBackCategoryUrl('group', $this->type['value'], $this->type['valuecatid']);
		$this->t['breadcrumb']	= PhocaMenuHelper::getBreadcrumbs($this->type['info']['text'], $this->type['info']['backlink']. $this->typeup['urlup'], JText::_($this->type['info']['backlinktxt']), $this->typeup['backlink'], JText::_($this->typeup['backlinktxt']));

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		$params 			= JComponentHelper::getParams( 'com_phocamenu' );
		$bar 				= JToolbar::getInstance('toolbar');
		$this->state		= $this->get('State');
		$user  				= JFactory::getUser();



		$this->t['displaytoolbartools']	= $params->get( 'display_toolbar_tools', 1 );
		$displayToolbars 	= PhocaMenuHelper::displayToolbarTools($this->t['displaytoolbartools'], $this->type['value']);

		// Actual Category
		$categoryId 				= $this->state->get('filter.category_id');

		$this->type['actualcatid']	= PhocaMenuHelper::getActualCategory('item', $this->type['value'], $categoryId);

		require_once JPATH_COMPONENT.'/helpers/phocamenuitems.php';
		$canDo	= phocamenuitemsHelper::getActions($this->t, $this->state->get('filter.category_id'));

		JToolbarHelper::title( $this->type['info']['text'] , 'file-2' );

		//$bar->appendButton( 'Link', 'back', $this->type['info']['backlinktxt'], 'index.php?option=com_phocamenu'.$this->type['info']['backlink']. $this->typeup['urlup'] );

		$dhtml = '<a href="index.php?option=com_phocamenu'.$this->type['info']['backlink']. $this->typeup['urlup'].'" class="btn btn-small"><i class="icon-ph-back" title="'.JText::_($this->type['info']['backlinktxt']).'"></i> '.JText::_($this->type['info']['backlinktxt']).'</a>';
		$bar->appendButton('Custom', $dhtml);

		JToolbarHelper::divider();

		$backCatidSpec = '';
		if (isset($this->type['actualcatid']) && (int)$this->type['actualcatid'] > 0) {
			$backCatidSpec 	=  '&'.$this->type['info']['catid'].'='.(int)$this->type['actualcatid'];
		}
		$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));
		$this->t['linkpreview'] = JURI::root().'index.php?option=com_phocamenu&view='.$this->type['info']['frontview'].'&tmpl=component'.$langSuffix;
		$this->t['linkemail'] 	= 'index.php?option=com_phocamenu&task=phocamenuemail.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		$this->t['linkmultiple']= 'index.php?option=com_phocamenu&task=phocamenumultipleedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		$this->t['linkraw']= 'index.php?option=com_phocamenu&task=phocamenurawedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		//$this->t['linkpdf']	= PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 1);
		$this->t['linkconfig']	= 'index.php?option=com_phocamenu&task=phocamenuconfig.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		//ID must be added
		$this->t['linkedit']	= 'index.php?option=com_phocamenu&task=phocamenuitem.edit&type='.$this->type['value']. $backCatidSpec;


		if ($canDo->get('core.create')) {
			JToolbarHelper::addNew( 'phocamenuitem.add','JToolbar_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolbarHelper::editList('phocamenuitem.edit','JToolbar_EDIT');
			if ($displayToolbars) {
				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkmultiple'].'"><span class="icon-32-multiple" title="'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a>');
				$dhtml = '<a href="'.$this->t['linkmultiple'].'" class="btn btn-small"><i class="icon-ph-multiple" title="'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'"></i> '.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a>';
				$bar->appendButton('Custom', $dhtml);
				$dhtml = '<a href="'.$this->t['linkraw'].'" class="btn btn-small"><i class="icon-ph-raw" title="'.JText::_('COM_PHOCAMENU_RAW_EDIT').'"></i> '.JText::_('COM_PHOCAMENU_RAW_EDIT').'</a>';
				$bar->appendButton('Custom', $dhtml);
			}
		}

		if ($canDo->get('core.manage')) {

			if ($displayToolbars) {
				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkemail'].'"><span class="icon-32-email" title="'.JText::_('COM_PHOCAMENU_EMAIL').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_EMAIL').'</a>');
				//JToolbarHelper::preview( JURI::root().$this->t['linkpreview'] );
				//$bar->appendButton( 'Popup', 'prmpreview', 'COM_PHOCAMENU_PREVIEW', $this->t['linkpreview']);

				$dhtml = '<a href="'.$this->t['linkemail'].'" class="btn btn-small"><i class="icon-ph-email" title="'.JText::_('COM_PHOCAMENU_EMAIL').'"></i> '.JText::_('COM_PHOCAMENU_EMAIL').'</a>';
				$bar->appendButton('Custom', $dhtml);

				JHtml::_('behavior.framework');
				JHtml::_('behavior.modal');

				$dhtml = '<a class="btn btn-small" onclick="SqueezeBox.fromElement(this, {handler:\'iframe\', size: {x: 600, y: 450}, url:\''.$this->t['linkpreview'].'\'})"> <i class="icon-ph-preview" title="'.JText::_('COM_PHOCAMENU_PREVIEW').'"></i>'.JText::_('COM_PHOCAMENU_PREVIEW').'</a>';
				$bar->appendButton('Custom', $dhtml);

				$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));
				$bar->appendButton( 'Custom', PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 0, $langSuffix));

			}
		}

		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::divider();
			JToolbarHelper::custom('phocamenuitems.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			JToolbarHelper::custom('phocamenuitems.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			JToolbarHelper::deleteList( JText::_( 'COM_PHOCAMENU_WARNING_DELETE_ITEMS' ), 'phocamenuitems.delete', 'COM_PHOCAMENU_DELETE');
		}

		// Add a batch button
		if ($user->authorise('core.edit'))
		{
			JHtml::_('bootstrap.renderModal', 'collapseModal');
			$title = JText::_('JToolbar_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		JToolbarHelper::divider();

		if ($canDo->get('core.manage')) {
			//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkconfig'].'"><span class="icon-32-settings" title="'.JText::_('COM_PHOCAMENU_SETTINGS').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_SETTINGS').'</a>');

			JToolbarHelper::divider();
			$dhtml = '<a href="'.$this->t['linkconfig'].'" class="btn btn-small"><i class="icon-ph-settings" title="'.JText::_('COM_PHOCAMENU_SETTINGS').'"></i> '.JText::_('COM_PHOCAMENU_SETTINGS').'</a>';
				$bar->appendButton('Custom', $dhtml);
		}

		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.phocamenu', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> JText::_('JGRID_HEADING_ORDERING'),
			'a.quantity' 	=> JText::_($this->t['l'] . '_QUANTITY'),
			'a.title' 		=> JText::_($this->t['l'] . '_TITLE'),
			'a.price' 		=> JText::_($this->t['l'] . '_PRICE'),
			'a.price2' 		=> JText::_($this->t['l'] . '_PRICE2'),
			'a.catid' 		=> JText::_($this->t['l'] . '_GROUP'),
			'a.published' 	=> JText::_($this->t['l'] . '_PUBLISHED'),
			'language' 		=> JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}
}
?>
