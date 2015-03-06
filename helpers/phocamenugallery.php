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


class PhocaMenuGallery
{
	public static function getPhocaGalleryBehaviour($paramsG) {
		
		$document		= JFactory::getDocument();
		
		// LIBRARY
		$library 							= PhocaGalleryLibrary::getLibrary();
		$libraries['pg-group-shadowbox']	= $library->getLibrary('pg-group-shadowbox');
		$libraries['pg-group-highslide']	= $library->getLibrary('pg-group-highslide');
		
		// Window
		// =======================================================
		// DIFFERENT METHODS OF DISPLAYING THE DETAIL VIEW
		// =======================================================

		// BUTTON (IMAGE - standard, modal, shadowbox)
		$button = new JObject();
		$button->set('name', 'image');
		
		// -------------------------------------------------------
		// STANDARD POPUP
		// -------------------------------------------------------
		if ($paramsG['imagedetailwindow'] == 1) {
			$button->set('methodname', 'js-button');
			$button->set('options', "window.open(this.href,'win2','width=".$paramsG['frontmodalboxwidth'].",height=".$paramsG['frontmodalboxheight'].",menubar=no,resizable=yes'); return false;");			
		}
		
		// -------------------------------------------------------
		// MODAL BOX
		// -------------------------------------------------------
		else if ($paramsG['imagedetailwindow'] == 2) { 
			JHTML::_('behavior.modal', 'a.modal-button');
			$cssSbox = " #sbox-window {background-color:".$paramsG['modalboxbordercolor']	.";padding:".$paramsG['modalboxborderwidth']."px} \n"
					 .  " #sbox-overlay {background-color:".$paramsG['modalboxoverlaycolor'].";} \n";
			$document->addCustomTag( "<style type=\"text/css\">\n" . $cssSbox . "\n" . " </style>\n");
			// Button
			$button->set('modal', true);
			$button->set('methodname', 'modal-button');
			// Modal - Image only
			$button->set('options', "{handler: 'image', size: {x: 200, y: 150}, overlayOpacity: ".$paramsG['modalboxoverlayopacity'].", classWindow: 'phocagallery-phocamenu-window', classOverlay: 'phocagallery-phocamenu-overlay'}");
		}
		
		// -------------------------------------------------------
		// SHADOW BOX
		// -------------------------------------------------------

		else if ($paramsG['imagedetailwindow'] == 3) {
			JHTML::_('behavior.modal', 'a.modal-button');
			$button->set('methodname', 'shadowbox-button');
			$button->set('options', "shadowbox[PhocaGalleryPhocaMenu];options={slideshowDelay:0}");

		//	$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/shadowbox/adapter/shadowbox-mootools.js');
			$document->addStyleSheet(JURI::base(true).'/components/com_phocagallery/assets/shadowbox/shadowbox.css');
			$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/shadowbox/shadowbox.js');	
			
			if ( $libraries['pg-group-shadowbox']->value == 0 ) {
				
				//Shadowbox.loadSkin("classic", "'.JURI::base(true).'/components/com_phocagallery/assets/shadowbox/src/skin");
				//Shadowbox.loadLanguage("en", "'.JURI::base(true).'/components/com_phocagallery/assets/shadowbox/src/lang");
				//Shadowbox.loadPlayer(["img"], "'.JURI::base(true).'/components/com_phocagallery/assets/shadowbox/src/player");
				
				$document->addCustomTag('<script type="text/javascript">
				
				window.addEvent(\'domready\', function(){
						   Shadowbox.init();
				});
				</script>');
				$library->setLibrary('pg-group-shadowbox', 1);
			}
		}
		
		// -------------------------------------------------------
		// HIGHSLIDE JS
		// -------------------------------------------------------

		else if ($paramsG['imagedetailwindow'] == 5) {

			$all = '<script type="text/javascript">'
			.'//<![CDATA[' ."\n"
			.' hs.graphicsDir = \''.JURI::base(true).'/components/com_phocagallery/assets/highslide/graphics/\';'
			.'//]]>'."\n"
			.'</script>'."\n";
		
	
			$code = 'return hs.expand(this, {'
			//.'autoplay:\'true\','
			.' slideshowGroup: \'groupPMrounded-white\', ';
			//$code .= ' src: \'[phocahsfullimg]\',';
			$code .= ' wrapperClassName: \'rounded-white\',';
			$code .= ' outlineType : \'rounded-white\',';
			$code .= ' dimmingOpacity: 0, '
			.' align : \'center\', '
			.' transitions : [\'expand\', \'crossfade\'],'
			.' fadeInOut: true'
			.' });';
		
		
			$tag = '<script type="text/javascript">'
			.'//<![CDATA[' ."\n"
			.' var phocaZoomPM = { '."\n"
			.' objectLoadTime : \'after\',';
			$tag .= ' outlineType : \'rounded-white\',';
			$tag .= ' wrapperClassName: \'rounded-white\','
			.' outlineWhileAnimating : true,'
			.' enableKeyListener : false,'
			.' minWidth : '.$paramsG['frontmodalboxwidth'].','
			.' minHeight : '.$paramsG['frontmodalboxheight'].','
			.' dimmingOpacity: 0, '
			.' fadeInOut : true,'
			.' contentId: \'detail\','
			.' objectType: \'iframe\','
			.' objectWidth: '.$paramsG['frontmodalboxwidth'].','
			.' objectHeight: '.$paramsG['frontmodalboxheight'].''
			.' };';

			$tag .= 'hs.registerOverlay({
				html: \'<div class=\u0022closebutton\u0022 onclick=\u0022return hs.close(this)\u0022 title=\u0022'. JText::_( 'COM_PHOCAGALLERY_CLOSE_WINDOW' ).'\u0022></div>\',
				position: \'top right\',
				fade: 2
			});';
			
			$tag .= '//]]>'."\n"
			.'</script>'."\n";
		
		
			$button->set('methodname', 'highslide');
			
			$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/highslide/highslide-full.js');
			$document->addStyleSheet(JURI::base(true).'/components/com_phocagallery/assets/highslide/highslide.css');
			
			if ( $libraries['pg-group-highslide']->value == 0 ) {		
				$document->addCustomTag( $all);
				$document->addCustomTag('<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="'.JURI::base(true).'/components/com_phocagallery/assets/highslide/highslide-ie6.css" /><![endif]-->');
				$library->setLibrary('pg-group-highslide', 1);
			}
			$document->addCustomTag($tag);
			$tmpl['highslideonclick']	= $code;
			$button->set('highslideonclick', $tmpl['highslideonclick']);

		}
		return $button;
	}
	
	public static function getPhocaGalleryLink($imageSize, $imageId, $imageFileName, $imageCatid, $imageSlug, $imageCatSlug, $imageDetailWindow, $dataGallery, $imageextid, $imageexts, $imageextm, $imageextl, $imageextw, $imageexth) {
	
		if ((int)$imageId < 1) {
			return '';
		}
		$imageOutput	= '';
	
		$app 	= JFactory::getApplication('site');
		$menu  = $app->getMenu();
		
		if ($imageextid != '') {
			if ($imageSize == 'medium'){
				$file_thumbnail = $imageextm;
			} else {
				$file_thumbnail = $imageexts;
			}
		} else {
			$file_thumbnail = PhocaGalleryImageFront::displayCategoryImageOrNoImage($imageFileName, $imageSize);
			
			$noImageS = $noImageM = false;
			$noImageM = preg_match("/phoca_thumb_m_no_image/i", $file_thumbnail);
			$noImageS = preg_match("/phoca_thumb_s_no_image/i", $file_thumbnail);
			if($noImageM || $noImageS) {
				return '';
			}
		}
		
		// Is there a Itemid for category SEF PROBLEM - - - - - 
		$items	 = $menu->getItems('link', 'index.php?option=com_phocagallery&view=category&id='.(int)$imageCatid);
		$itemscat= $menu->getItems('link', 'index.php?option=com_phocagallery&view=categories');
		
		if(isset($itemscat[0])) {
			$itemid = $itemscat[0]->id;
			$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $imageCatSlug .'&id='. $imageSlug .'&Itemid='.$itemid . '&tmpl=component&detail='.$imageDetailWindow );
		} else if(isset($items[0])) {
			$itemid = $items[0]->id;
			$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $imageCatSlug .'&id='. $imageSlug .'&Itemid='.$itemid . '&tmpl=component&detail='.$imageDetailWindow );
		} else {
			$itemid = 0;
			$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $imageCatSlug.'&id='. $imageSlug . '&tmpl=component&detail='.$imageDetailWindow );
		}
		// - - - - - - - - - - - - - - - 
		
		// Different links for different actions: image, zoom icon, download icon
		$thumbLink	= PhocaGalleryFileThumbnail::getThumbnailName($imageFileName, 'large');
		$imgLink	= JURI::base(true) .'/'. $thumbLink->rel;
		
		// External Image
		if ($imageextid != '') {
			$imgLink		= $imageextl;
		}
		
		if ($imageDetailWindow == 2 ) {
			$imageLinkOutput = $imgLink;
		} else if ( $imageDetailWindow == 3 ) {
			$imageLinkOutput = $imgLink;
		} else if ( $imageDetailWindow == 5 ) {
			$imageLinkOutput = $imgLink;
		} else {
			$imageLinkOutput = $siteLink . '&buttons=0&ratingimg=0';
		}
		
		$imageOutput = '<div class="pmimage"><a class="'.$dataGallery->methodname.'" title="'.JText::_('Image Detail').'" href="'. JRoute::_($imageLinkOutput).'"';
		
		// DETAIL WINDOW
		if ($imageDetailWindow == 1) {
			$imageOutput .= ' onclick="'. $dataGallery->options.'"';
		} else if ($imageDetailWindow == 5) {
			$imageOutput .= ' onclick="'. $dataGallery->highslideonclick.'"';
		} else {
			$imageOutput .= ' rel="'.$dataGallery->options.'"';
		}
		
		$imageOutput .= ' >' . "\n";
		$imageOutput .= '<img src="'.$file_thumbnail.'" alt="'.JText::_('Image Detail').'" /></a>';
		$imageOutput .= '</div>';
		
		return $imageOutput;
	}
}
?>