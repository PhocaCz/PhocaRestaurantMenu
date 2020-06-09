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

class PhocaMenuCpViewPhocaMenuGroups extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	public $filterForm;
	public $activeFilters;
	protected $p;

	function display($tpl = null) {

		$this->t			= PhocaMenuUtils::setVars('group');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->type			= PhocaMenuHelper::getUrlType('group');



		// Override the model - populateState in case we really want exactly group/item/list
		if ($this->type['method'] == 'get') {
			switch ($this->type['value']){
				case 2:
				case 3:
				case 4:
				case 5:
					$this->state->set('filter.category_id', $this->type['valuecatid']);
				break;
			}
		}

		foreach ($this->items as &$item) {
			$this->ordering[$item->catid][] = $item->id;
		}

		JHTML::stylesheet( $this->t['s'] );

		$this->t['breadcrumb']	= PhocaMenuHelper::getBreadcrumbs($this->type['info']['text'], $this->type['info']['backlink'], JText::_($this->type['info']['backlinktxt']));

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
		$displayToolbars 				= PhocaMenuHelper::displayToolbarTools($this->t['displaytoolbartools'], $this->type['value']);

		// Actual Category
		$categoryId 				= $this->state->get('filter.category_id');
		$this->type['actualcatid']	= PhocaMenuHelper::getActualCategory('group', $this->type['value'], $categoryId);

		require_once JPATH_COMPONENT.'/helpers/phocamenugroups.php';

		switch ($this->type['value']){
			case 2:
			case 3:
			case 4:
			case 5:
				$canDo	= PhocaMenuGroupsHelper::getActions($this->t, $this->state->get('filter.category_id'));
			break;
			default:
				$canDo	= PhocaMenuGroupsHelper::getActions($this->t);
			break;
		}

		JToolbarHelper::title( $this->type['info']['text'] , 'file-2' );

		$dhtml = '<a href="index.php?option=com_phocamenu'.$this->type['info']['backlink'].'" class="btn btn-small"><i class="icon-ph-back" title="'.JText::_($this->type['info']['backlinktxt']).'"></i> '.JText::_($this->type['info']['backlinktxt']).'</a>';
		$bar->appendButton('Custom', $dhtml);

		JToolbarHelper::divider();

		$backCatidSpec = '';
		if (isset($this->type['actualcatid']) && (int)$this->type['actualcatid'] > 0) {
			$backCatidSpec 	=  '&'.$this->type['info']['catid'].'='.(int)$this->type['actualcatid'];
		}
		$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));


		$this->t['linkpreview'] = JURI::root().'index.php?option=com_phocamenu&view='.$this->type['info']['frontview'].'&tmpl=component&admin=1'.$langSuffix;
		$this->t['linkemail'] 	= 'index.php?option=com_phocamenu&task=phocamenuemail.edit&type='.$this->type['value'].'&typeback=group'. $backCatidSpec;
		$this->t['linkmultiple']= 'index.php?option=com_phocamenu&task=phocamenumultipleedit.edit&type='.$this->type['value'].'&typeback=group'.$backCatidSpec;
		$this->t['linkraw']= 'index.php?option=com_phocamenu&task=phocamenurawedit.edit&type='.$this->type['value'].'&typeback=group'.$backCatidSpec;
		//$this->t['linkpdf']	= PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 1);
		$this->t['linkconfig']	= 'index.php?option=com_phocamenu&task=phocamenuconfig.edit&type='.$this->type['value'].'&typeback=group'. $backCatidSpec;
		//ID must be added
		$this->t['linkedit']	= 'index.php?option=com_phocamenu&task=phocamenugroup.edit&type='.(int)$this->type['value']. $backCatidSpec;


		if ($canDo->get('core.create')) {
			JToolbarHelper::addNew( 'phocamenugroup.add','JToolbar_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolbarHelper::editList('phocamenugroup.edit','JToolbar_EDIT');
			if ($displayToolbars) {

				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkmultiple'].'"><span class="icon-ph-multiple" title="'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a>');
				$dhtml = '<a href="'.$this->t['linkmultiple'].'" class="btn btn-small"><i class="icon-ph-multiple" title="'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'"></i> '.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a>';
				$bar->appendButton('Custom', $dhtml);
				$dhtml = '<a href="'.$this->t['linkraw'].'" class="btn btn-small"><i class="icon-ph-raw" title="'.JText::_('COM_PHOCAMENU_RAW_EDIT').'"></i> '.JText::_('COM_PHOCAMENU_RAW_EDIT').'</a>';
				$bar->appendButton('Custom', $dhtml);

			}
		}

		if ($canDo->get('core.manage')) {

			if ($displayToolbars) {
				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkemail'].'"><span class="icon-ph-email" title="'.JText::_('COM_PHOCAMENU_EMAIL').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_EMAIL').'</a>');

				$dhtml = '<a href="'.$this->t['linkemail'].'" class="btn btn-small"><i class="icon-ph-email" title="'.JText::_('COM_PHOCAMENU_EMAIL').'"></i> '.JText::_('COM_PHOCAMENU_EMAIL').'</a>';
				$bar->appendButton('Custom', $dhtml);

				// PREVIEW
				//JToolbarHelper::preview( JURI::root().$this->t['linkpreview'] );
				//$bar->appendButton( 'Popup', 'prmpreview', 'COM_PHOCAMENU_PREVIEW', $this->t['linkpreview']);

				JHtml::_('jquery.framework');

				$html 		= array();
				$idA		= 'phMenuPreview';

				// Screenshot
				$buttonScreenshot = '';
				if ($this->p['enable_screenshot'] == 1) {
					$buttonScreenshot = ' <button type="button" class="btn btn-primary phPrintButton" data-id="'.$idA.'">' . JText::_('COM_PHOCAMENU_TAKE_SCREENSHOT') . '</button>';
					PhocamenuRender::renderScreenshotScript($idA, $this->p);
				}


				$html[] = '<a href="#'.$idA.'" role="button" class="btn btn-small" data-toggle="modal" title="' . JText::_('COM_PHOCAMENU_PREVIEW') . '">'
					. '<span class="icon-ph-preview"></span> '
					. JText::_('COM_PHOCAMENU_PREVIEW') . '</a>';

				$html[] = JHtml::_(
					'bootstrap.renderModal',
					$idA,
					array(

						'url'    => $this->t['linkpreview'],
						'title'  => JText::_('COM_PHOCAMENU_PREVIEW'),
						'width'  => '700px',
						'height' => '400px',
						'modalWidth' => '80',
						'bodyHeight' => '70',
						'footer' => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
							. JText::_('COM_PHOCAMENU_CLOSE') . '</button>'. $buttonScreenshot
					)
				);

				$dhtml = implode("\n", $html);
				$bar->appendButton('Custom', $dhtml);
				// END PREVIEW


				$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));
				$bar->appendButton( 'Custom', PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 0, $langSuffix));

			}
		}

		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::divider();
			JToolbarHelper::custom('phocamenugroups.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			JToolbarHelper::custom('phocamenugroups.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			JToolbarHelper::deleteList( JText::_( 'COM_PHOCAMENU_WARNING_DELETE_ITEMS' ), 'phocamenugroups.delete', 'COM_PHOCAMENU_DELETE');
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

		//JToolbarHelper::divider();

		if ($canDo->get('core.manage')) {
			//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkconfig'].'"><span class="icon-ph-settings" title="'.JText::_('COM_PHOCAMENU_SETTINGS').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_SETTINGS').'</a>');
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
			'a.title' 		=> JText::_($this->t['l'] . '_TITLE'),
			'a.published' 	=> JText::_($this->t['l'] . '_PUBLISHED'),
			'language' 		=> JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}

}
?>
