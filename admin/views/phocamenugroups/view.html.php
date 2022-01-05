<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view' );

class PhocaMenuCpViewPhocaMenuGroups extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	public $filterForm;
	public $activeFilters;
	protected $p;

	function display($tpl = null) {

		$this->t			= PhocaMenuUtils::setVars('group');
		$this->r 			= new PhocaMenuRenderAdminViews();
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



		$this->t['breadcrumb']	= PhocaMenuHelper::getBreadcrumbs($this->type['info']['text'], $this->type['info']['backlink'], Text::_($this->type['info']['backlinktxt']));

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		$params 			= ComponentHelper::getParams( 'com_phocamenu' );
	    $this->p['screenshot_css'] 		= $params->get('screenshot_css', '');
	    $this->p['enable_screenshot'] 	= $params->get('enable_screenshot', 0);
	    $this->p['remove_stylesheet_string'] 	= $params->get('remove_stylesheet_string', '');

		$bar 				= Toolbar::getInstance('toolbar');
		$this->state		= $this->get('State');
		$user  				= Factory::getUser();

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

		ToolbarHelper::title( $this->type['info']['text'] , 'file-2' );

		$dhtml = '<joomla-toolbar-button><a href="index.php?option=com_phocamenu'.$this->type['info']['backlink'].'" class="btn btn-small"><i class="icon-ph-back" title="'.Text::_($this->type['info']['backlinktxt']).'"></i> '.Text::_($this->type['info']['backlinktxt']).'</a></joomla-toolbar-button>';
		$bar->appendButton('Custom', $dhtml);

		ToolbarHelper::divider();

		$backCatidSpec = '';
		if (isset($this->type['actualcatid']) && (int)$this->type['actualcatid'] > 0) {
			$backCatidSpec 	=  '&'.$this->type['info']['catid'].'='.(int)$this->type['actualcatid'];
		}
		$langSuffix = PhocaMenuHelper::getLangSuffix($this->state->get('filter.language'));


		$this->t['linkpreview'] = Uri::root().'index.php?option=com_phocamenu&view='.$this->type['info']['frontview'].'&tmpl=component&admin=1'.$langSuffix;
		$this->t['linkemail'] 	= 'index.php?option=com_phocamenu&task=phocamenuemail.edit&type='.$this->type['value'].'&typeback=group'. $backCatidSpec;
		$this->t['linkmultiple']= 'index.php?option=com_phocamenu&task=phocamenumultipleedit.edit&type='.$this->type['value'].'&typeback=group'.$backCatidSpec;
		$this->t['linkraw']= 'index.php?option=com_phocamenu&task=phocamenurawedit.edit&type='.$this->type['value'].'&typeback=group'.$backCatidSpec;
		//$this->t['linkpdf']	= PhocaMenuRender::getIconPDFAdministrator($this->type['info']['frontview'], 1);
		$this->t['linkconfig']	= 'index.php?option=com_phocamenu&task=phocamenuconfig.edit&type='.$this->type['value'].'&typeback=group'. $backCatidSpec;
		//ID must be added
		$this->t['linkedit']	= 'index.php?option=com_phocamenu&task=phocamenugroup.edit&type='.(int)$this->type['value']. $backCatidSpec;


		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew( 'phocamenugroup.add','JToolbar_NEW');
		}
		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocamenugroup.edit','JToolbar_EDIT');
			if ($displayToolbars) {

				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkmultiple'].'"><span class="icon-ph-multiple" title="'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a>');
				$dhtml = '<joomla-toolbar-button><a href="'.$this->t['linkmultiple'].'" class="btn btn-small"><i class="icon-ph-multiple" title="'.Text::_('COM_PHOCAMENU_MULTIPLE_EDIT').'"></i> '.Text::_('COM_PHOCAMENU_MULTIPLE_EDIT').'</a></joomla-toolbar-button>';
				$bar->appendButton('Custom', $dhtml);
				$dhtml = '<joomla-toolbar-button><a href="'.$this->t['linkraw'].'" class="btn btn-small"><i class="icon-ph-raw" title="'.Text::_('COM_PHOCAMENU_RAW_EDIT').'"></i> '.Text::_('COM_PHOCAMENU_RAW_EDIT').'</a></joomla-toolbar-button>';
				$bar->appendButton('Custom', $dhtml);
			}
		}

		$this->t['modal_bottom'] = '';
		if ($canDo->get('core.manage')) {

			if ($displayToolbars) {
				//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkemail'].'"><span class="icon-ph-email" title="'.JText::_('COM_PHOCAMENU_EMAIL').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_EMAIL').'</a>');

				$dhtml = '<joomla-toolbar-button><a href="'.$this->t['linkemail'].'" class="btn btn-small"><i class="icon-ph-email" title="'.Text::_('COM_PHOCAMENU_EMAIL').'"></i> '.Text::_('COM_PHOCAMENU_EMAIL').'</a></joomla-toolbar-button>';
				$bar->appendButton('Custom', $dhtml);

				// PREVIEW
				//JToolbarHelper::preview( JUri::root().$this->t['linkpreview'] );
				//$bar->appendButton( 'Popup', 'prmpreview', 'COM_PHOCAMENU_PREVIEW', $this->t['linkpreview']);

				HTMLHelper::_('jquery.framework');

				$html 		= array();
				$idA		= 'phMenuPreview';

				// Screenshot
				$buttonScreenshot = '';
				if ($this->p['enable_screenshot'] == 1) {
					$buttonScreenshot = ' <button type="button" class="btn btn-primary phPrintButton" data-id="'.$idA.'">' . Text::_('COM_PHOCAMENU_TAKE_SCREENSHOT') . '</button>';
					PhocamenuRender::renderScreenshotScript($idA, $this->p);
				}


				$html[] = '<joomla-toolbar-button><a href="'.$this->t['linkpreview'].'" role="button" class="btn btn-small" data-bs-toggle="modal" data-bs-target="#'.$idA.'"  title="' . Text::_('COM_PHOCAMENU_PREVIEW') . '">'
					. '<span class="icon-ph-preview"></span> '
					. Text::_('COM_PHOCAMENU_PREVIEW') . '</a></joomla-toolbar-button>';



				// TO DO - waiting for final solution in Joomla
				//https://github.com/joomla/joomla-cms/issues/35506
				//$html[] = HTMLHelper::_(
				// paste to the bottom of default.php
				$this->t['modal_bottom'] = HTMLHelper::_(
					'bootstrap.renderModal',
					$idA,
					array(

						'url'    => $this->t['linkpreview'],
						'title'  => Text::_('COM_PHOCAMENU_PREVIEW'),
						'width'  => '',
						'height' => '',
						'modalWidth' => '80',
						'bodyHeight' => '70',
						'footer' => '<button type="button" class="btn" data-bs-dismiss="modal" aria-hidden="true">'
							. Text::_('COM_PHOCAMENU_CLOSE') . '</button>'. $buttonScreenshot
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
			ToolbarHelper::divider();
			ToolbarHelper::custom('phocamenugroups.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocamenugroups.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( 'COM_PHOCAMENU_WARNING_DELETE_ITEMS', 'phocamenugroups.delete', 'COM_PHOCAMENU_DELETE');
		}

		// Add a batch button
		if ($user->authorise('core.edit'))
		{
			/*HTMLHelper::_('bootstrap.renderModal', 'collapseModal');
			$title = Text::_('JToolbar_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');*/

			$bar->popupButton('batch')
				->text('JTOOLBAR_BATCH')
				->selector('collapseModal')
				->listCheck(true);


		}

		//JToolbarHelper::divider();

		if ($canDo->get('core.manage')) {
			//$bar->appendButton( 'Custom', '<a href="'.$this->t['linkconfig'].'"><span class="icon-ph-settings" title="'.JText::_('COM_PHOCAMENU_MENU_SETTINGS').'" type="Custom"></span>'.JText::_('COM_PHOCAMENU_MENU_SETTINGS').'</a>');
			ToolbarHelper::divider();
			$dhtml = '<joomla-toolbar-button><a href="'.$this->t['linkconfig'].'" class="btn btn-small"><i class="icon-ph-settings" title="'.Text::_('COM_PHOCAMENU_MENU_SETTINGS').'"></i> '.Text::_('COM_PHOCAMENU_MENU_SETTINGS').'</a></joomla-toolbar-button>';
				$bar->appendButton('Custom', $dhtml);
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamenu', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_($this->t['l'] . '_TITLE'),
			'a.published' 	=> Text::_($this->t['l'] . '_PUBLISHED'),
			'language' 		=> Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}

}
?>
