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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
jimport('joomla.html.parameter');
class PhocaMenuFrontRender
{
	public static function renderFrontIcons($pdf = false, $print = false, $email = false, $printView = false, $rss = 0, $paramsIcons = ''){

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
					//$output .= PhocaMenuFrontRender::getIconEmail($paramsIcons) . '&nbsp;';
				}
				if ((int)$rss > 0) {
					$output .= PhocaMenuFrontRender::getIconRSS($paramsIcons) . '&nbsp;';
				}
			} else {
				$output .= '<div id="phPrintButton">'. PhocaMenuFrontRender::getIconPrintScreen($paramsIcons) . '&nbsp;'
						. ' <a href="javascript: void window.close()">'
						//. HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-close.png', Text::_( 'Close Window' ))
						.'<div class="icon-times fa-times icon-fw phc-grey" title="'.Text::_('COM_PHOCAMENU_CLOSE').'"></div>'
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
			$pluginPDFP	 	=PluginHelper::getPlugin('phocapdf', 'restaurantmenu');
			$pluginP 		= new Registry( $pluginPDFP->params );


			$pdfDestination	= $pluginP->get('pdf_destination', 'S');

			$view	= Factory::getApplication()->input->get( 'view' );
			$url	= 'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&format=pdf';

			$itemId	= Factory::getApplication()->input->get( 'Itemid', 0, 'int' );
			if ((int)$itemId > 0) {
				$url .= '&Itemid='.(int)$itemId;
			}

			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

			if ($paramsIcons) {
				//$text = HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-pdf.png', Text::_('COM_PHOCAMENU_PRINT_PDF'));
				$text = '<div class="icon-file-pdf fa-file-pdf icon-fw phc-red"></div>';
			} else {
				$text = Text::_('COM_PHOCAMENU_PRINT_PDF');
			}
			$attribs['title']	= Text::_( 'COM_PHOCAMENU_PDF' );

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

			$output	= HTMLHelper::_('link', Route::_($url), $text, $attribs);

		} else {
			$output = '<a href="#" title="'.Text::_('COM_PHOCAMENU_ERROR_PHOCA_PDF_RESTAURANT_MENU_PLUGIN_NOT_INSTALLED').'">'.HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-pdf-dis.png', Text::_('COM_PHOCAMENU_PRINT_PDF')) . '</a>';
		}


		return $output;
	}

	public static function getIconPrint($paramsIcons) {

		$view	= Factory::getApplication()->input->get( 'view' );
		$url	= 'index.php?option=com_phocamenu&view='.$view.'&tmpl=component&print=1';
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		$itemId	= Factory::getApplication()->input->get( 'Itemid', 0, 'int' );
		if ((int)$itemId > 0) {
			$url .= '&Itemid='.(int)$itemId;
		}

		if ($paramsIcons) {
			//$text = HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-print.png', Text::_('Print'));
			$text = '<div class="icon-print fa-print icon-fw phc-grey"></div>';
		} else {
			$text = Text::_('Print');
		}
		$attribs['title']	= Text::_( 'Print' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$output				= HTMLHelper::_('link', Route::_($url), $text, $attribs);
		return $output;
	}

	public static function getIconEmail($paramsIcons) {


		return '';
	/*	require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';

		$uri      = Uri::getInstance();
		$base     = $uri->toString(array('scheme', 'host', 'port'));
		$template = Factory::getApplication()->getTemplate();
		$link     = $uri->toString();
		$url      = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);


		$status = 'width=400,height=400,menubar=yes,resizable=yes';

		if ($paramsIcons) {
			$text = HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-email.png', Text::_('Email'));
		} else {
			$text = Text::_('Email');
		}
		$attribs['title']	= Text::_( 'Email' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$output				= HTMLHelper::_('link', Route::_($url), $text, $attribs);
		return $output;*/
	}

	public static function getIconRSS($paramsIcons) {

		$view	= Factory::getApplication()->input->get( 'view' );
		$url	= 'index.php?option=com_phocamenu&view='.$view.'&format=feed';

		$itemId	= Factory::getApplication()->input->get( 'Itemid', 0, 'int' );
		if ((int)$itemId > 0) {
			$url .= '&Itemid='.(int)$itemId;
		}

		if ($paramsIcons) {
			//$text = HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-feed.png', Text::_('RSS'));
			$text = '<div class="icon-feed fa-feed icon-fw phc-orange" title="'.Text::_('COM_PHOCAMENU_RSS').'"></div>';
		} else {
			$text = Text::_('COM_PHOCAMENU_RSS');
		}

		$output = '<a href="'.Route::_($url).'" title="'.Text::_('COM_PHOCAMENU_RSS').'">'.$text . '</a>';

		return $output;
	}

	public static function getIconPrintScreen($paramsIcons) {

		if ($paramsIcons) {
			//$text = HTMLHelper::_('image', 'media/com_phocamenu/images/icon-16-print.png', Text::_('Print'));
			$text = '<div class="icon-print fa-print icon-fw phc-red" title="'.Text::_('COM_PHOCAMENU_PRINT').'"></div>';
		} else {
			$text = Text::_('COM_PHOCAMENU_PRINT');
		}
		$output = '<a href="javascript: void()" onclick="document.getElementById(\'phPrintButton\').style.visibility = \'hidden\';window.print();return false;">'.$text.'</a>';
		return $output;
	}
}
?>
