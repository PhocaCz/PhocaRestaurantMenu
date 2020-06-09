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
use Joomla\CMS\HTML\HTMLHelper;
defined('_JEXEC') or die;
jimport('joomla.html.parameter');
class PhocaMenuFrontRender
{
	public static function renderFrontIcons($pdf = false, $print = false, $email = false, $printView = false, $rss = 0, $paramsIcons){

		$output = '';
		if ($pdf || $print || $email || (int)$rss > 0) {

			$output = '<div id="phocamenuicons"><div class="pm-buttons">';

			if (!$printView) {

				if ($pdf) {
					$output .= PhocaMenuFrontRender::getIconPDF($paramsIcons) . '&nbsp;';
				}

				if ($print) {
					$output .= PhocaMenuFrontRender::getIconPrint($paramsIcons) . '&nbsp;';
				}

				if ($email) {
					$output .= PhocaMenuFrontRender::getIconEmail($paramsIcons) . '&nbsp;';
				}
				if ((int)$rss > 0) {
					$output .= PhocaMenuFrontRender::getIconRSS($paramsIcons) . '&nbsp;';
				}
			} else {
				$output .= '<div id="phPrintButton">'. PhocaMenuFrontRender::getIconPrintScreen($paramsIcons) . '&nbsp;'
						. ' <a href="javascript: void window.close()">'
						. JHTML::_('image', 'media/com_phocamenu/images/icon-16-close.png', JText::_( 'Close Window' ))
						. '</a></div>';
			}


			$output .= '</div></div>';
		}
		return $output;
	}

	public static function getIconPDF($paramsIcons) {

		//Phoca PDF Restaurant Menu Plugin:
		$pluginPDF	 	=PhocaMenuExtensionHelper::getExtensionInfo('restaurantmenu', 'plugin', 'phocapdf');
		$componentPDF	=PhocaMenuExtensionHelper::getExtensionInfo('com_phocapdf');

		// Plugin is installed, Plugin is enabled
		if ($pluginPDF == 1 && $componentPDF == 1) {
			$pluginPDFP	 	=JPluginHelper::getPlugin('phocapdf', 'restaurantmenu');
			$pluginP 		= new JRegistry( $pluginPDFP->params );


			$pdfDestination	= $pluginP->get('pdf_destination', 'S');

			$view	= JFactory::getApplication()->input->get( 'view' );
			$url	= 'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&format=pdf';
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

			if ($paramsIcons) {
				$text = JHTML::_('image', 'media/com_phocamenu/images/icon-16-pdf.png', JText::_('COM_PHOCAMENU_PRINT_PDF'));
			} else {
				$text = JText::_('COM_PHOCAMENU_PRINT_PDF');
			}
			$attribs['title']	= JText::_( 'COM_PHOCAMENU_PDF' );

			$browser = PhocaMenuHelper::PhocaMenuBrowserDetection('browser');
			if ($browser == 'msie7' || $browser == 'msie8') {
				$attribs['target'] 	= "_blank";
			} else {
				if ($pdfDestination == 'I' || $pdfDestination == 'D') {
					// Remome OnClick
				} else {
					$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
				}
			}

			$output	= JHTML::_('link', JRoute::_($url), $text, $attribs);

		} else {
			$output = '<a href="#" title="'.JText::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'">'.JHTML::_('image', 'media/com_phocamenu/images/icon-16-pdf-dis.png', JText::_('COM_PHOCAMENU_PRINT_PDF')) . '</a>';
		}


		return $output;
	}

	public static function getIconPrint($paramsIcons) {

		$view	= JFactory::getApplication()->input->get( 'view' );
		$url	= 'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&print=1';
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		if ($paramsIcons) {
			$text = JHTML::_('image', 'media/com_phocamenu/images/icon-16-print.png', JText::_('Print'));
		} else {
			$text = JText::_('Print');
		}
		$attribs['title']	= JText::_( 'Print' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$output				= JHTML::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

	public static function getIconEmail($paramsIcons) {


		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';

		$uri      = JUri::getInstance();
		$base     = $uri->toString(array('scheme', 'host', 'port'));
		$template = JFactory::getApplication()->getTemplate();
		$link     = $uri->toString();
		$url      = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);


		$status = 'width=400,height=400,menubar=yes,resizable=yes';

		if ($paramsIcons) {
			$text = JHTML::_('image', 'media/com_phocamenu/images/icon-16-email.png', JText::_('Email'));
		} else {
			$text = JText::_('Email');
		}
		$attribs['title']	= JText::_( 'Email' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$output				= JHTML::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

	public static function getIconRSS($paramsIcons) {

		$view	= JFactory::getApplication()->input->get( 'view' );
		$url	= 'index.php?option=com_phocamenu&view='.$view.'&format=feed';

		if ($paramsIcons) {
			$text = JHTML::_('image', 'media/com_phocamenu/images/icon-16-feed.png', JText::_('RSS'));
		} else {
			$text = JText::_('RSS');
		}

		$output = '<a href="'.JRoute::_($url).'" title="'.JText::_('RSS').'">'.$text . '</a>';

		return $output;
	}

	public static function getIconPrintScreen($paramsIcons) {

		if ($paramsIcons) {
			$text = JHTML::_('image', 'media/com_phocamenu/images/icon-16-print.png', JText::_('Print'));
		} else {
			$text = JText::_('Print');
		}
		$output = '<a href="javascript: void()" onclick="document.getElementById(\'phPrintButton\').style.visibility = \'hidden\';window.print();return false;">'.$text.'</a>';
		return $output;
	}
}
?>
