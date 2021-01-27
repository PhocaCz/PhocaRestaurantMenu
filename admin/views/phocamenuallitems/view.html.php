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

class PhocaMenuCpViewPhocaMenuAllitems extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	protected $type;
	protected $typeup;
	public $filterForm;
	public $activeFilters;
	protected $p;

	function display($tpl = null) {

		$this->t			= PhocaMenuUtils::setVars('allitem');
		$this->r 			= new PhocaMenuRenderAdminViews();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		//$this->type			= PhocaMenuHelper::getUrlType('item');



		// Override the model - populateState in case we really want exactly group/item/list
	/*	if ($this->type['method'] == 'get') {
			$this->state->set('filter.category_id', $this->type['valuecatid']);
		}*/

		foreach ($this->items as &$item) {
			$this->ordering[$item->catid][] = $item->id;
		}



		// Breadcrumbs
		///$this->typeup 	= PhocaMenuHelper::getBackCategoryUrl('group', $this->type['value'], $this->type['valuecatid']);

		///$this->t['breadcrumb']	= PhocaMenuHelper::getBreadcrumbs($this->type['info']['text'], $this->type['info']['backlink']. $this->typeup['urlup'], JText::_($this->type['info']['backlinktxt']), $this->typeup['backlink'], JText::_($this->typeup['backlinktxt']));
		///
		 $this->t['breadcrumb'] = '';

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		$params 			= JComponentHelper::getParams( 'com_phocamenu' );
	    $this->p['screenshot_css'] 		= $params->get('screenshot_css', '');
	    $this->p['enable_screenshot'] 	= $params->get('enable_screenshot', 0);
	    $this->p['remove_stylesheet_string'] 	= $params->get('remove_stylesheet_string', '');
		$bar 				= JToolbar::getInstance('toolbar');
		$this->state		= $this->get('State');
		$user  				= JFactory::getUser();



		$this->t['displaytoolbartools']	= $params->get( 'display_toolbar_tools', 1 );
		$displayToolbars 	= false;/// PhocaMenuHelper::displayToolbarTools($this->t['displaytoolbartools'], $this->type['value']);

		// Actual Category
		$categoryId 				= $this->state->get('filter.category_id');

		$this->type['actualcatid']	= 0;/// PhocaMenuHelper::getActualCategory('item', $this->type['value'], $categoryId);

		require_once JPATH_COMPONENT.'/helpers/phocamenuitems.php';
		$canDo	= phocamenuitemsHelper::getActions($this->t, $this->state->get('filter.category_id'));

		JToolbarHelper::title( /*$this->type['info']['text']*/ JText::_('COM_PHOCAMENU_ALL_ITEMS') , 'file-2' );

		//$bar->appendButton( 'Link', 'back', $this->type['info']['backlinktxt'], 'index.php?option=com_phocamenu'.$this->type['info']['backlink']. $this->typeup['urlup'] );

		///$dhtml = '<a href="index.php?option=com_phocamenu'.$this->type['info']['backlink']. $this->typeup['urlup'].'" class="btn btn-small"><i class="icon-ph-back" title="'.JText::_($this->type['info']['backlinktxt']).'"></i> '.JText::_($this->type['info']['backlinktxt']).'</a>';
		///$bar->appendButton('Custom', $dhtml);

		JToolbarHelper::divider();

		$backCatidSpec = '';
		if (isset($this->type['actualcatid']) && (int)$this->type['actualcatid'] > 0) {
			$backCatidSpec 	=  '&'.$this->type['info']['catid'].'='.(int)$this->type['actualcatid'];
		}

		$this->type['info']['frontview'] = '';
		$this->type['value'] = '';

		$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));
		$this->t['linkpreview'] = JURI::root().'index.php?option=com_phocamenu&view='.$this->type['info']['frontview'].'&tmpl=component&admin=1'.$langSuffix;
		$this->t['linkemail'] 	= 'index.php?option=com_phocamenu&task=phocamenuemail.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		$this->t['linkmultiple']= 'index.php?option=com_phocamenu&task=phocamenumultipleedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		$this->t['linkraw']= 'index.php?option=com_phocamenu&task=phocamenurawedit.edit&type='.$this->type['value'].'&typeback=item'.$backCatidSpec;
		//$this->t['linkpdf']	= PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 1);
		$this->t['linkconfig']	= 'index.php?option=com_phocamenu&task=phocamenuconfig.edit&type='.$this->type['value'].'&typeback=item'. $backCatidSpec;
		//ID must be added
		$this->t['linkedit']	= 'index.php?option=com_phocamenu&task=phocamenuitem.edit&type='.$this->type['value']. $backCatidSpec;


		if ($canDo->get('core.create')) {
			//JToolbarHelper::addNew( 'phocamenuitem.add','JToolbar_NEW');

			JHtml::_('bootstrap.renderModal', 'collapseModalNew');
			$title = JText::_('JToolbar_NEW');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModalNew\" class=\"btn btn-small button-new btn-success\">
						<i class=\"icon-new icon-white\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'new');
		}

		if ($canDo->get('core.edit')) {
			JToolbarHelper::editList('phocamenuitem.edit','JToolbar_EDIT');

		}



		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::divider();
			JToolbarHelper::custom('phocamenuallitems.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			JToolbarHelper::custom('phocamenuallitems.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			JToolbarHelper::deleteList( JText::_( 'COM_PHOCAMENU_WARNING_DELETE_ITEMS' ), 'phocamenuallitems.delete', 'COM_PHOCAMENU_DELETE');
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
