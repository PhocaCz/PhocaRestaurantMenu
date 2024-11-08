<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

class PhocaMenuRenderViews
{

	/*
	 * VIEWS:
	 * 1 Email
	 * 2 PDF
	 * 3 Multiple Edit
	 * 4 Bootstrap 2
	 * 5 Bootstrap 3
	 * 6 Raw edit
	 * 7 Array (only daily menu)
	 * Default Table
	 */

	/*
	 *
	 *
	 * Daily Menu
	 *
	 *
	 */
	public static function renderDailyMenu($data, $tmpl, $params, $paramsG, $method = 0) {

		$method = self::setMethod($params, $method);
		$tag = PhocaMenuRenderViews::getStyle($method, $tmpl['phocagallery']);
		$paramsC['displaygroupmsg']	= $params->get( 'display_group_message', 2 );



		$oP = array();// Raw 6
		$oA = array();// Array 7

		$o = self::setMainId($method);

		// HEADER (Config)
		$oddEvenBox = 0;
		if (isset($data['config'])) {
			$o .= $tag['header-o'];
			$o .= PhocaMenuRenderViews::getCustomCode($tmpl['customclockcode'], $data['config']->header, $method);
			$o .= $tag['header-c'];

			if ($method == 3) {
				$o .= $tag['oddbox-o'];
				$oddEvenBox = 1;
			}

			$date = PhocaMenuHelper::getDate($data['config']->date, $tmpl['daydateformat'], $tmpl['dateclass'], $data['config']->language);

			if ($method == 3) {
				$o .= $tag['date-o'] . $date . '<br/>'
				. HTMLHelper::_('calendar', (string)$data['config']->date, 'date['.$data['config']->id.']', 'date'.$data['config']->id, "%Y-%m-%d", array('class'=>'inputbox', 'size'=>'45',  'maxlength'=>'45')) . $tag['date-c'];
			} else if ($method == 6) {
				$oP[] = '#'.$data['config']->date;
			} else if ($method == 7) {
				$oA['date']  = $data['config']->date;
			}	else {

				$o .= $tag['date-o'] . $date . $tag['date-c'];
			}
		}

		// BODY
		if (isset($data['group'])) {
			for ($g = 0, $gr = count($data['group']); $g < $gr; $g++) {

				if ($method == 3) {
					$o .= $tag['group-o'] . '<input size="30" class="form-control" type="text" name="group['.$data['group'][$g]->id.']" id="group'.$data['group'][$g]->id.'" value="'.$data['group'][$g]->title.'" />' . $tag['group-c'];
				} else if ($method == 6) {
					$oP[] = '###'.$data['group'][$g]->title;
					if (!empty($data['group'][$g]->message)) {
						$groupMsg = PhocaMenuHelper::cleanRawOutput($data['group'][$g]->message);
						if (!empty($groupMsg)) {$oP[] = '>'.$groupMsg;}
					}
				} else if ($method == 7) {
					$oA['groups'][$g]['title'] = $data['group'][$g]->title;
					if (!empty($data['group'][$g]->message)) {
						$oA['groups'][$g]['message'] = PhocaMenuHelper::cleanRawOutput($data['group'][$g]->message);
					}
					if (!empty($data['group'][$g]->type_group)) {
						$oA['groups'][$g]['type_group'] = $data['group'][$g]->type_group;
					}
				} else {
					$o .= $tag['group-o'] . $data['group'][$g]->title . $tag['group-c'];
					if ($paramsC['displaygroupmsg'] == 1) {
						$o .= $tag['message-o'] .  $data['group'][$g]->message . $tag['message-c'];
					}
				}

				if (isset($data['item'])) {
					$displayTaskIcons 	= 0;// we don't know if there is some row, if there will be then display icons
					$displayAddRow		= 0;// we don't know if there is some row, if not, don't display add row
					$o .= $tag['item-o'] . $tag['tableitem-o'];

					// Second Price, Header Group
					if ($method == 3 ) {
						$o .= PhocaMenuRenderViews::renderGroupHeaderME($data['group'][$g], $tag );
					} else {
						$o .= PhocaMenuRenderViews::renderGroupHeader($data['group'][$g]->display_second_price, $data['group'][$g]->header_price, $data['group'][$g]->header_price2, $tag, $method, 1 );
					}
					// END SP

					for ($i = 0, $it = count($data['item']); $i < $it; $i++) {
						// item must belong to own group

						if ($data['group'][$g]->id == $data['item'][$i]->catid) {

							// Possible tasks in multipleedit - display them only before first row
							if ($method == 3 && $displayTaskIcons == 0) {
								$o .= PhocaMenuRenderViews::renderTaskIconsME($data['group'][$g]->display_second_price);
								// there is some first row
								$displayTaskIcons 	= 1;
								$displayAddRow		= 1;
							}

							$image	= $tag['spaceimg'];

							if ($data['item'][$i]->image != '') {
								$altTitle = '';
								if ($data['item'][$i]->title != '') {
									$altTitle = htmlspecialchars(strip_tags($data['item'][$i]->title));
								}
								$image = '<div class="pmimage pmimage-full"><img src="'.Uri::base().$data['item'][$i]->image.'" alt="'.$altTitle.'" /></div>';
							} else {
								// PHOCAGALLERY Image  - - - - - -
								if ((int)$tmpl['phocagallery'] == 1) {
									$image = PhocaMenuGallery::getPhocaGalleryLink($tmpl['imagesize'], $data['item'][$i]->imageid, $data['item'][$i]->imagefilename, $data['item'][$i]->imagecatid, $data['item'][$i]->imageslug, $data['item'][$i]->imagecatslug, $paramsG['imagedetailwindow'], $tmpl['button'], $data['item'][$i]->imageextid,$data['item'][$i]->imageexts,$data['item'][$i]->imageextm,$data['item'][$i]->imageextl,$data['item'][$i]->imageextw,$data['item'][$i]->imageexth );

								}
								// - - - - - - - - - - - - - - - -
							}


							if (isset($data['item'][$i]->price) && $data['item'][$i]->price > 0) {
								$price 		= PhocaMenuHelper::getPriceFormat($data['item'][$i]->price, $params);
								$pricePref	= $tmpl['priceprefix'];
							} else {
								$price 		= '';
								$pricePref	= '';
							}

							// Second Price
							if ($data['group'][$g]->display_second_price == 1 && isset($data['item'][$i]->price2) && $data['item'][$i]->price2 > 0) {
								$price2 		= PhocaMenuHelper::getPriceFormat($data['item'][$i]->price2, $params);
								$pricePref2		= $tmpl['priceprefix'];
							} else {
								$price2 		= '';
								$pricePref2		= '';
							}
							// End SP

							if ($method == 3) {
								$o .= PhocaMenuRenderViews::renderFormItemME(1, $tag, $data['group'][$g], $data['item'][$i], $pricePref, $method, $price2, $pricePref2, $data['group'][$g]->display_second_price);
							} else if ($method == 6) {
								$oP[] = PhocaMenuRenderViews::renderFormItemRE(1, $tag, $data['group'][$g], $data['item'][$i], $pricePref, $method, $price2, $pricePref2, $data['group'][$g]->display_second_price);
							} else if ($method == 7) {
								$oA['groups'][$g]['items'][$i] = PhocaMenuRenderViews::renderFormItemAE(1, $tag, $data['group'][$g], $data['item'][$i], $pricePref, $method, $price2, $pricePref2, $data['group'][$g]->display_second_price);
							} else {
								$o .= PhocaMenuRenderViews::renderFormItem(1, $tag, $image, $data['item'][$i], $price, $pricePref, $method, $price2, $pricePref2, $data['group'][$g]->display_second_price);
							}
						}

						// End Item
					}

					$o .= $tag['tableitem-c'] . $tag['item-c'];
					if ($method == 3) {
						if ($displayAddRow == 1) {
							$o .= '<div class="pm-addrow"><small><a href="#" onclick="addRow('.$data['group'][$g]->id.', 1); return false;">'.Text::_('COM_PHOCAMENU_ADD_ROW').'</a></small></div>';
						}
					}
				}// end items

				 if ($method == 3) {
					$o .= $tag['message-o'] . '<textarea class="form-control" rows="2" cols="60" name="message[' . $data['group'][$g]->id .']" id="message' . $data['group'][$g]->id .'">'. $data['group'][$g]->message . '</textarea>'. $tag['message-c'];
				} else {
					if ($paramsC['displaygroupmsg'] == 2) {
						$o .= $tag['message-o'] .  $data['group'][$g]->message . $tag['message-c'];
					}
				}
			}
		} // end group

		if ($method == 3 && $oddEvenBox == 1) {
			$o .= $tag['bothbox-c'];
		}

		// FOOTER (Config)
		if (isset($data['config'])) {
			$o .=  $tag['footer-o'] . $data['config']->footer .  $tag['footer-c'];
		}

		$o .= '</div>';// end phocamenu

		if ($method == 3) {
			return $o;
		} else if ($method == 6) {
			return PhocaMenuRenderViews::renderFormCompleteRE($oP);
		} else if ($method == 7) {
			return $oA;
		}

		$enableBBCode = $params->get( 'enable_bb_code', 0 );
		if ((int)$enableBBCode == 1) {
			$o = PhocaMenuHelper::bbCodeReplace($o);
			$o = str_replace ( '[br]', '<br />', $o );
		} else if ((int)$enableBBCode  == 2) {
			$o = str_replace ( '[br]', '<br />', $o );
		}
		$o = PhocaMenuHelper::replaceTag($o, $method);

		// Remove empty table - because of TCPDF
		$o = str_replace($tag['tableitem-o'].$tag['tableitem-c'], '', $o);

		$o .= PhocaMenuHelper::renderCode($params->get( 'render_code', 1 ), $method);

		return $o;
	}




	public static function getStyle($method, $phocaGallery, $suffix = '') {

		$app						= Factory::getApplication();
		$paramsC 					= $app->isClient('administrator') ? ComponentHelper::getParams('com_phocamenu') : $app->getParams();
		// Bootstrap for image will have length x instead of one (must be changed in renderformitem function too)
		$bs_image_length	= $paramsC->get( 'bs_image_length', 1 );

		// 2 means, there are two prices, all the table tds must be recalculated, e.g. $tag['desc2-c'] - this is not a method
		switch ($method) {

			// Email
			case 1:
				$tag['header-o']		= '<div style="font-size:140%;font-weight:bold;margin:10px">';
				$tag['header-c']		= '</div>';
				$tag['date-o']			= '<div style="font-size:120%;font-weight:bold;margin:10px;text-align:right;">';
				$tag['date-c']			= '</div>';
				$tag['datesub-o']		= '<div style="font-size:110%;font-weight:bold;margin:10px;margin-top:20px;text-decoration:underline">';
				$tag['datesub-c']		= '</div>';
				$tag['list-o']			= '<div style="font-weight:bold;margin:10px;">';
				$tag['list-c']			= '</div>';
				$tag['group-o']			= '<div style="font-weight:bold;margin:10px;">';
				$tag['group-c']			= '</div>';
				$tag['groupleft-o']		= '<div style="overflow:visible;position:relative;float:left;width:43%;margin:1% 2% 1% 1%;">';
				$tag['groupleft-c']		= '</div>';
				$tag['groupright-o']	= '<div style="overflow:visible;position:relative;float:right;width:43%;margin:1% 1% 1% 2%;">';
				$tag['groupright-c']	= '</div>';
				$tag['item-o']			= '<div style="margin:10px;width:100%">';
				$tag['item-c']			= '</div>';
				$tag['tableitem-o']		= '<table width="90%" cellspacing="3" cellpadding="3" style="border:0px;border-collapse:collapse;">';
				$tag['tableitem-c']		= '</table>';

				if ($suffix == '-clm') {
					$tag['message-o']		= '<div style="text-align:left">';
				} else {
					$tag['message-o']		= '<div style="text-align:center">';
				}
				$tag['message-c']		= '</div>';

				$tag['image-o']			= '<td class="pmimage">';
				$tag['image-rs-o']		= '<td class="pmimage" ';//Not closed - waiting for rowspan
				$tag['image-c']			= '</td>';

				if ((int)$phocaGallery == 1) {
					$tag['quantity-o']		= '<td style="vertical-align:middle;width:8%;white-space:nowrap;">';
					if ($suffix == '-clm') {
						$tag['title-o']		= $tag['title2-o']	= '<td style="vertical-align:middle;width:auto;font-weight:bold">';
					} else {
						$tag['title-o']		= $tag['title2-o']	= '<td style="vertical-align:middle;width:auto;">';
					}
					$tag['priceprefix-o']	= '<td style="vertical-align:middle;width:2%">';
					$tag['price-o']			= '<td style="vertical-align:middle;width:5%;white-space:nowrap;padding-left: 10px;">';
					$tag['price2-o']		= '<td style="vertical-align:middle;width:5%;white-space:nowrap;padding-left: 10px;">';
				} else {
					$tag['quantity-o']		= '<td style="width:8%;white-space:nowrap;">';
					if ($suffix == '-clm') {
						$tag['title-o']		= $tag['title2-o']	= '<td style="vertical-align:middle;width:auto;font-weight:bold">';
					} else {
						$tag['title-o']		= $tag['title2-o']	= '<td style="vertical-align:middle;width:auto;">';
					}

					$tag['priceprefix-o']	= '<td style="width:2%">';
					$tag['price-o']			= '<td style="width:5%;white-space:nowrap;padding-left: 10px;">';
					$tag['price2-o']		= '<td style="width:5%;white-space:nowrap;padding-left: 10px;">';
				}
				$tag['quantity-c']		= '</td>';
				$tag['title-c']			= '</td>';
				$tag['title2-c']		= '</td>';
				$tag['priceprefix-c']	= '</td>';
				$tag['price-c']			= '</td>';
				$tag['price2-c']		= '</td>';


				$tag['desc-o']			= $tag['desc2-o'] = '<td style="font-style:italic;margin:5px">';
				$tag['desc-c']			= $tag['desc2-c'] = '</td>';
				$tag['addinfo-o']		= $tag['addinfo2-o'] = '<td style="font-style:italic;margin:5px">';
				$tag['addinfo-c']		= $tag['addinfo2-c'] = '</td>';

				$tag['groupheader1-o']			= '<td align="right">';
				$tag['groupheader1-c']			= '</td>'. "\n";
				$tag['groupheader2-o']			= '<td align="right" >';
				$tag['groupheader2-c']			= '</td>'. "\n";



				$tag['footer-o']		= '<div class="pm-footer">';
				$tag['footer-c']		= '</div>';

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '&nbsp;';


			break;

			//PDF
			case 2:

				$tag['header-o']		= '<div>';
				$tag['header-c']		= '</div>';
				$tag['date-o']			= '<p style="font-weight: bold;font-size: x-large;text-align: right;">';
				$tag['date-c']			= '</p>';
				$tag['datesub-o']		= '<p style="font-weight: bold;font-size: large;text-decoration: underline">';
				$tag['datesub-c']		= '</p>';
				$tag['list-o']			= '<p style="font-weight: bold;font-size: large;text-decoration: underline">';
				$tag['list-c']			= '</p>';
				$tag['group-o']			= '<p style="font-weight:bold;font-size:x-large;">';
				$tag['group-c']			= '</p>';

				$tag['item-o']			= '<div>';
				$tag['item-c']			= '</div>';
				$tag['tableitem-c']		= '</table>';
				$tag['message-o']		= '<table border="0"><tr><td>';
				$tag['message-c']		= '<br /></td></tr></table>';

				$tag['image-o']			= '<td width="2pt">';
				$tag['image-rs-o']		= '<td width="2pt" ';//Not closed - waiting for rowspan
				$tag['image-c']			= '</td>';

				if ($suffix == '-bl') {

					$tag['tableitem-o']		= '<table border="0" cellpadding="0"  >';

					$tag['groupleft-o']		= '<table border="0"><tr><td width="245pt">';
					$tag['groupleft-c']		= '</td>';
					$tag['groupright-o']	= '<td width="20pt">&nbsp;</td><td width="245pt">';
					$tag['groupright-c']	= '</td></tr></table>';

					$tag['quantity-o']		= '<td width="40pt" style="text-align:right">';
					$tag['title-o']			= '<td width="138pt" >&nbsp;';
					$tag['title2-o']		= '<td width="88pt" >&nbsp;';//Second Price
					$tag['priceprefix-o']	= '<td width="17pt" style="text-align:right;">';
					$tag['price-o']			= $tag['price2-o'] =  '<td width="50pt" style="text-align:right;">';

				} else if($suffix == '-clm') {

					$tag['tableitem-o']		= '<table border="0" cellpadding="1" >';

					$tag['quantity-o']		= '<td width="50pt" style="text-align:right">';
					$tag['title-o']			= '<td width="386pt" style="font-weight:bold;font-size:large;">';
					$tag['title2-o']		= '<td width="336pt" style="font-weight:bold;font-size:large;">';//Second Price
					$tag['priceprefix-o']	= '<td width="22pt" style="text-align:right;">';
					$tag['price-o']			= $tag['price2-o'] =  '<td width="50pt" style="text-align:right;">';

				} else {

					$tag['tableitem-o']		= '<table border="0" cellpadding="1">';

					$tag['quantity-o']		= '<td width="50pt" style="text-align:right">';
					$tag['title-o']			= '<td width="386pt">';
					$tag['title2-o']		= '<td width="336pt">';//Second Price
					$tag['priceprefix-o']	= '<td width="22pt" style="text-align:right;">';
					$tag['price-o']			= $tag['price2-o'] = '<td width="50pt" style="text-align:right;">';
				}

				$tag['quantity-c']		= '</td>';
				$tag['title-c']			= '</td>';
				$tag['title2-c']		= '</td>';
				$tag['priceprefix-c']	= '</td>';
				$tag['price-c']			= '</td>';
				$tag['price2-c']		= '</td>'. "\n";

				$tag['groupheader1-o']			= '<td width="50pt" style="text-align:right;">';
				$tag['groupheader1-c']			= '</td>'. "\n";
				$tag['groupheader2-o']			= '<td width="50pt" style="text-align:right;">';
				$tag['groupheader2-c']			= '</td>'. "\n";

				if ($suffix == '-bl') {
					$tag['desc-o']			= '<td width="192pt" style="font-style:italic;">';
					$tag['desc2-o']			= '<td width="142pt" style="font-style:italic;">';// Second Price

					$tag['addinfo-o']			= '<td width="192pt" style="font-style:italic;">';
					$tag['addinfo2-o']			= '<td width="142pt" style="font-style:italic;">';// Second Price
				} else {
					$tag['desc-o']			= '<td width="386pt" style="font-style:italic;">';
					$tag['desc2-o']			= '<td width="336pt" style="font-style:italic;">';// Second Price
					$tag['addinfo-o']			= '<td width="386pt" style="font-style:italic;">';
					$tag['addinfo2-o']			= '<td width="336pt" style="font-style:italic;">';// Second Price
				}
				$tag['desc-c']			= '</td>';
				$tag['desc2-c']			= '</td>';
				$tag['addinfo-c']			= '</td>';
				$tag['addinfo2-c']			= '</td>';

				$tag['footer-o']		= '<table border="0"><tr><td>';
				$tag['footer-c']		= '</td></tr></table>';

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '';

			break;

			// Bootstrap 2 (Frontend)
			case 4:

				$tag['header-o']		= '<div class="pm-header">';
				$tag['header-c']		= '</div>'. "\n";
				$tag['date-o']			= '<div class="pm-date">';
				$tag['date-c']			= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['datesub-o']		= '<div class="pm-date-sub">';
				$tag['datesub-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['list-o']			= '<div class="pm-list">';
				$tag['list-c']			= '</div>'. "\n";
				$tag['group-o']			= '<div class="row row-fluid pm-group">';
				$tag['group-c']			= '</div>'. "\n";
				$tag['evenbox-o']		= '<div class="row row-fluid">';
				$tag['oddbox-o']		= '<div class="row row-fluid">';
				$tag['groupleft-o']		= '<div class="span6 pm-group-left-bs">';
				$tag['groupleft-c']		= '</div>'. "\n";
				$tag['groupright-o']	= '<div class="span6 pm-group-right-bs">';
				$tag['groupright-c']	= '</div>'. "\n";
				$tag['item-o']			= '';
				$tag['item-c']			= ''. "\n";
				$tag['tableitem-o']		= '';
				$tag['tableitem-c']		= ''. "\n";
				$tag['message-o']		= '<div class="pm-message">';
				$tag['message-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				// Tag TITLE AND TAG DESCRIPTION IS COUNTED BELOW

				$tag['row-box-o']		= '<div class="ph-item-row-box">';
				$tag['row-box-c']		= '</div>';
				$tag['row-o']			= '<div class="row row-fluid pm-item-row">';
				$tag['row-c']			= '</div>';
				$tag['row-desc-o']		= '<div class="row row-fluid pm-desc-row">';
				$tag['row-desc-c']		= '</div>';
				$tag['row-addinfo-o']		= '<div class="row row-fluid pm-addinfo-row">';
				$tag['row-addinfo-c']		= '</div>';
				$tag['row-group-h-o']	= '<div class="row row-fluid pm-group-header-row">';
				$tag['row-group-h-c']	= '</div>';

				$tag['image-o']			= '<div class="span'.(int)$bs_image_length.' pmimage">'; // when changed then the count of columns must be changed too: renderFormItem (1257)
				$tag['quantity-o']		= '<div class="span1 pmquantity">';
				$tag['priceprefix-o']	= '<div class="span1 pmpriceprefix pm-right">';

				$tag['price-o-span2']	= '<div class="span2 pmprice pm-right">';
				$tag['price-o-span1']	= '<div class="span1 pmprice pm-right">';


				$tag['groupheader1-o-span1']	= '<div class="span1 pmgroupheader1">';
				$tag['groupheader1-o-span2']	= '<div class="span2 pmgroupheader1">';
				$tag['groupheader2-o-span1']	= '<div class="span1 pmgroupheader2">';
				$tag['groupheader2-o-span2']	= '<div class="span2 pmgroupheader2">';

				$tag['image-c']			= '</div>'. "\n";
				$tag['quantity-c']		= '</div>'. "\n";
				$tag['priceprefix-c']	= '</div>'. "\n";
				$tag['price-c']			= '</div>'. "\n";

				$tag['groupheader1-c']	= '</div>'. "\n";
				$tag['groupheader2-c']	= '</div>'. "\n";



				$tag['footer-o']		= '<div class="pm-footer">';
				$tag['footer-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '&nbsp;';

			break;

		/*	// Bootstrap 3 (Frontend)
			case 5:

				$tag['header-o']		= '<div class="pm-header">';
				$tag['header-c']		= '</div>'. "\n";
				$tag['date-o']			= '<div class="pm-date">';
				$tag['date-c']			= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['datesub-o']		= '<div class="pm-date-sub">';
				$tag['datesub-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['list-o']			= '<div class="pm-list">';
				$tag['list-c']			= '</div>'. "\n";
				$tag['group-o']			= '<div class="row row-fluid pm-group">';
				$tag['group-c']			= '</div>'. "\n";
				$tag['evenbox-o']		= '<div class="row row-fluid">';
				$tag['oddbox-o']		= '<div class="row row-fluid">';
				$tag['groupleft-o']		= '<div class="col-xs-12 col-sm-6 col-md-6 pm-group-left-bs">';
				$tag['groupleft-c']		= '</div>'. "\n";
				$tag['groupright-o']	= '<div class="col-xs-12 col-sm-6 col-md-6 pm-group-right-bs">';
				$tag['groupright-c']	= '</div>'. "\n";
				$tag['item-o']			= '';
				$tag['item-c']			= ''. "\n";
				$tag['tableitem-o']		= '';
				$tag['tableitem-c']		= ''. "\n";
				$tag['message-o']		= '<div class="pm-message">';
				$tag['message-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				// Tag TITLE AND TAG DESCRIPTION IS COUNTED BELOW

				$tag['row-box-o']		= '<div class="ph-item-row-box">';
				$tag['row-box-c']		= '</div>';
				$tag['row-o']			= '<div class="row row-fluid pm-item-row">';
				$tag['row-c']			= '</div>';
				$tag['row-desc-o']		= '<div class="row row-fluid pm-desc-row">';
				$tag['row-desc-c']		= '</div>';
				$tag['row-group-h-o']	= '<div class="row row-fluid pm-group-header-row">';
				$tag['row-group-h-c']	= '</div>';

				$tag['image-o']			= '<div class="col-xs-12 col-sm-1 col-md-1 pmimage">';// when changed then the count of columns must be changed too: renderFormItem (1257)
				$tag['quantity-o']		= '<div class="col-xs-12 col-sm-1 col-md-1 pmquantity">';
				$tag['priceprefix-o']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmpriceprefix pm-right">';

				$tag['price-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmprice pm-right">';
				$tag['price-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmprice pm-right">';


				$tag['groupheader1-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmgroupheader1">';
				$tag['groupheader1-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmgroupheader1">';
				$tag['groupheader2-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmgroupheader2">';
				$tag['groupheader2-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmgroupheader2">';

				$tag['image-c']			= '</div>'. "\n";
				$tag['quantity-c']		= '</div>'. "\n";
				$tag['priceprefix-c']	= '</div>'. "\n";
				$tag['price-c']			= '</div>'. "\n";

				$tag['groupheader1-c']	= '</div>'. "\n";
				$tag['groupheader2-c']	= '</div>'. "\n";



				$tag['footer-o']		= '<div class="pm-footer">';
				$tag['footer-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '&nbsp;';

			break;
		*/

			// Bootstrap 5 (Frontend)
			case 5:

				$tag['header-o']		= '<div class="pm-header">';
				$tag['header-c']		= '</div>'. "\n";
				$tag['date-o']			= '<div class="pm-date">';
				$tag['date-c']			= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['datesub-o']		= '<div class="pm-date-sub">';
				$tag['datesub-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['list-o']			= '<div class="pm-list">';
				$tag['list-c']			= '</div>'. "\n";
				$tag['group-o']			= '<div class="row pm-group">';
				$tag['group-c']			= '</div>'. "\n";
				$tag['evenbox-o']		= '<div class="row">';
				$tag['oddbox-o']		= '<div class="row">';
				$tag['groupleft-o']		= '<div class="row"><div class="col-xs-12 col-sm-6 col-md-6 pm-group-left-bs">';
				$tag['groupleft-c']		= '</div>'. "\n";
				$tag['groupright-o']	= '<div class="col-xs-12 col-sm-6 col-md-6 pm-group-right-bs">';
				$tag['groupright-c']	= '</div></div>'. "\n";
				$tag['item-o']			= '';
				$tag['item-c']			= ''. "\n";
				$tag['tableitem-o']		= '';
				$tag['tableitem-c']		= ''. "\n";
				$tag['message-o']		= '<div class="pm-message">';
				$tag['message-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				// Tag TITLE AND TAG DESCRIPTION IS COUNTED BELOW

				$tag['row-box-o']		= '<div class="container ph-item-row-box">';
				$tag['row-box-c']		= '</div>';
				$tag['row-o']			= '<div class="row pm-item-row">';
				$tag['row-c']			= '</div>';
				$tag['row-desc-o']		= '<div class="row pm-desc-row">';
				$tag['row-desc-c']		= '</div>';
				$tag['row-addinfo-o']		= '<div class="row pm-addinfo-row">';
				$tag['row-addinfo-c']		= '</div>';
				$tag['row-group-h-o']	= '<div class="row pm-group-header-row">';
				$tag['row-group-h-c']	= '</div>';

				// when changed then the count of columns must be changed too: renderFormItem (1257)
				$tag['image-o']			= '<div class="col-xs-12 col-sm-'.(int)$bs_image_length.' col-md-'.(int)$bs_image_length.' pmimage">';
				$tag['quantity-o']		= '<div class="col-xs-12 col-sm-1 col-md-1 pmquantity">';
				$tag['priceprefix-o']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmpriceprefix pm-right">';

				$tag['price-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmprice pm-right">';
				$tag['price-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmprice pm-right">';


				$tag['groupheader1-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmgroupheader1">';
				$tag['groupheader1-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmgroupheader1">';
				$tag['groupheader2-o-span1']	= '<div class="col-xs-12 col-sm-1 col-md-1 pmgroupheader2">';
				$tag['groupheader2-o-span2']	= '<div class="col-xs-12 col-sm-2 col-md-2 pmgroupheader2">';

				$tag['image-c']			= '</div>'. "\n";
				$tag['quantity-c']		= '</div>'. "\n";
				$tag['priceprefix-c']	= '</div>'. "\n";
				$tag['price-c']			= '</div>'. "\n";

				$tag['groupheader1-c']	= '</div>'. "\n";
				$tag['groupheader2-c']	= '</div>'. "\n";



				$tag['footer-o']		= '<div class="pm-footer">';
				$tag['footer-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '&nbsp;';

			break;



			// Multiple Edit
			case 3:
			// Raw Edit
			case 6:
			// Front Table
			default:

				$tag['header-o']		= '<div class="pm-header">';
				$tag['header-c']		= '</div>'. "\n";
				$tag['date-o']			= '<div class="pm-date">';
				$tag['date-c']			= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['datesub-o']		= '<div class="pm-date-sub">';
				$tag['datesub-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['list-o']			= '<div class="pm-list">';
				$tag['list-c']			= '</div>'. "\n";
				$tag['group-o']			= '<div class="pm-group">';
				$tag['group-c']			= '</div><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['groupleft-o']		= '<div class="pm-group-left">';
				$tag['groupleft-c']		= '</div><div class="cr"></div>'. "\n";
				$tag['groupright-o']	= '<div class="pm-group-right">';
				$tag['groupright-c']	= '</div><div class="cl"></div>'. "\n";
				$tag['item-o']			= '<div class="pm-item' .$suffix. '">';
				$tag['item-c']			= '</div>'. "\n";
				$tag['tableitem-o']		= '<table style="border:0px;">';
				$tag['tableitem-c']		= '</table><div class="clearfix ph-clearfix"></div>'. "\n";
				$tag['message-o']		= '<div class="pm-message">';
				$tag['message-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				$tag['image-o']			= '<td class="pmimage">';
				$tag['image-rs-o']		= '<td class="pmimage" ';//Not closed - waiting for rowspan
				$tag['image-c']			= '</td>'. "\n";

				if ((int)$phocaGallery == 1) {
					$tag['quantity-o']		= '<td class="pmquantity" style="vertical-align:middle">';
					$tag['title-o']			= '<td class="pmtitle" style="vertical-align:middle">';
					$tag['title2-o']		= '<td class="pmtitle2" style="vertical-align:middle">';
					$tag['priceprefix-o']	= '<td class="pmpriceprefix" style="vertical-align:middle">';
					$tag['price-o']			= '<td class="pmprice" style="vertical-align:middle">';
					$tag['price2-o']		= '<td class="pmprice2" style="vertical-align:middle">';
				} else {
					$tag['quantity-o']		= '<td class="pmquantity">';
					$tag['title-o']			= '<td class="pmtitle">';
					$tag['title2-o']		= '<td class="pmtitle2">';
					$tag['priceprefix-o']	= '<td class="pmpriceprefix">';
					$tag['price-o']			= '<td class="pmprice">';
					$tag['price2-o']		= '<td class="pmprice2">';
				}
				$tag['quantity-c']		= '</td>'. "\n";
				$tag['title-c']			= '</td>'. "\n";
				$tag['title2-c']		= '</td>'. "\n";
				$tag['priceprefix-c']	= '</td>'. "\n";
				$tag['price-c']			= '</td>'. "\n";
				$tag['price2-c']		= '</td>'. "\n";

				$tag['desc-o']			= $tag['desc2-o'] = '<td class="pmdesc">';
				$tag['desc-c']			= $tag['desc2-c'] = '</td>'. "\n";

				$tag['addinfo-o']			= $tag['addinfo2-o'] = '<td class="pmaddinfo">';
				$tag['addinfo-c']			= $tag['addinfo2-c'] = '</td>'. "\n";

				$tag['groupheader1-o']			= '<td class="pmgroupheader1">';
				$tag['groupheader1-c']			= '</td>'. "\n";
				$tag['groupheader2-o']			= '<td class="pmgroupheader2">';
				$tag['groupheader2-c']			= '</td>'. "\n";


				$tag['footer-o']		= '<div class="pm-footer">';
				$tag['footer-c']		= '</div><div class="clearfix ph-clearfix"></div>'. "\n";

				$tag['space']			= '&nbsp;';
				$tag['spaceimg']		= '&nbsp;';

				if ($method == 3) {
					$tag['evenbox-o']		= '<div class="pmeven">';
					$tag['oddbox-o']		= '<div class="pmodd">';
					$tag['bothbox-c']		= '</div>';
					$tag['date-c']			= '</div><div style="clear:both"></div>';
					$tag['groupleft-o']		= '<div class="pm-group">';// no float
					$tag['groupright-o']	= '<div class="pm-group">';// no float
				}
			break;
		}
		return $tag;
	}



	public static function renderFormItem($type, $tag, $image, $itemObject, $price, $pricePref, $method, $price2, $pricePref2, $displaySecondPrice, $suffix = '') {



		$app						= Factory::getApplication();
		$paramsC 					= $app->isClient('administrator') ? ComponentHelper::getParams('com_phocamenu') : $app->getParams();
		$additional_info_title		= $paramsC->get( 'additional_info_title', '' );
		$display_additional_info	= $paramsC->get( 'display_additional_info', 1 );

		// Bootstrap for image will have length x instead of one (must be changed in getStyle function too)
		$bs_image_length	= $paramsC->get( 'bs_image_length', 1 );

		// Image
		$noImage = 1;
		if($image == '' || $image == '&nbsp;') {
			$noImage = 1;
		} else {
			$noImage = 0;
		}

		// If there is second price, add the prefix before both prices
		if ($price == '') {
			// Seems, there is no price but maybe there is second price
			if ($displaySecondPrice == 1 && $price2 != '') {
				$pricePref = $pricePref2;
			}
		}


		$rowSpan 	= '';
		$descRow 	= '';
		$addRow		= '';
		$row 		= array();
		$desc		= array();

		if ($method == 4 || $method == 5) {
			// ============= BOOTSTRAP 2 | BOOTSTRAP 3 =============

			$c = 0;

			if ($method == 4) {
				$span = 'span';
				$span2 = '';
			} else {
				$span = 'col-xs-12 col-sm-';
				$span2= ' col-md-';
			}

			if($noImage == 1) {
				$row['img'] = '';
				$desc['img']= '';
			} else {
				$row['img'] = $tag['image-o'] . $image . $tag['image-c'];
				$desc['img']= $tag['image-o'] . '' . $tag['image-c'];
				$c = $c + (int)$bs_image_length;//CHANGE DEPEND ON ROW 1297 (BS2) or 1365 (BS3) if span1 or com-md-1 then +1, if span2 or col-md-2 then + 2
			}

			$row['quantity'] = $tag['quantity-o'] . $itemObject->quantity . $tag['space']  . $tag['quantity-c'];
			$desc['quantity']= $tag['quantity-o'] . '' . $tag['quantity-c'];
			$c = $c + 1;

			// Matter of taste - add the priceprefix to first price
			$row['prefix'] = '';
			$desc['prefix'] = '';
			if ($pricePref != '') {
				$row['prefix'] = $tag['priceprefix-o'] . $pricePref. $tag['space'] . $tag['priceprefix-c'];
				$desc['prefix']= $tag['priceprefix-o'] . '' . $tag['priceprefix-c'];
				$c = $c + 1;
			}

			// Price
			// Type = 4 - Beverage list - there are two columns so we need to add one column - the header is changed too
			if ($type == 4) {
				$cP = 2;// span2 for price
				$sP = 'price-o-span2';
			} else {
				$cP = 1;// span1 for price
				$sP = 'price-o-span1';
			}

			if ($displaySecondPrice == 1) {
				$row['price1'] =  $tag[$sP]  . $price . $tag['price-c'];// REMOVE priceprefix possible
				$desc['price1']=  $tag[$sP] . '' . $tag['price-c'];
				$c = $c + $cP;
				$row['price2'] =  $tag[$sP]  . $price2 . $tag['price-c'];
				$desc['price2']=  $tag[$sP] . '' . $tag['price-c'];
				$c = $c + $cP;
			} else {
				$row['price1'] =  $tag[$sP]  . $price . $tag['price-c'];// REMOVE priceprefix possible
				$desc['price1']=  $tag[$sP] . '' . $tag['price-c'];
				$row['price2'] = '';
				$desc['price2'] = '';
				$c = $c + $cP;
			}

			// TITLE
			$titleColumn = 12 - (int)$c;

			$pmtitleO = '';
			if ($suffix == '-clm') {
				$pmtitleO = 'pmtitle-clm';
			} else if ($type == 4) {
				$pmtitleO = 'pmtitle-bl';
			}

			$class = '';
			if ($span != '') {
				$class .= $span . $titleColumn;
			}
			if ($span2 != '') {
				$class .= $span2 . $titleColumn;
			}

			$row['title'] = '<div class="'.$class.' pmtitle '.$pmtitleO.'">'.$itemObject->title.'</div>';


			$o = $tag['row-box-o'] . $tag['row-o'] . $row['img'] . $row['quantity'] . $row['title'] . $row['prefix'] . $row['price1'] . $row['price2'] . $tag['row-c'];

			// DESCRIPTION
			if ($itemObject->description != '') {

				$descRow .= $tag['row-desc-o'];
				$descRow .= $desc['img'] . $desc['quantity'];
				$descRow .= '<div class="'.$class.' pmdesc">';
				$descRow .= $itemObject->description;
				$descRow .= '</div>';
				$descRow .= $desc['prefix'] . $desc['price1'] . $desc['price2'];
				$descRow .= $tag['row-desc-c'];
			}


			// Additional Information
			if ($display_additional_info == 1 && isset($itemObject->additional_info) && $itemObject->additional_info != '') {

				$addRow .= $tag['row-addinfo-o'];
				$addRow .= $desc['img'] . $desc['quantity'];
				$addRow .= '<div class="'.$class.' pmaddinfo">';
				if ($additional_info_title != '') {
					$addRow .= '<div class="'.$class.' pmaddinfotitle">'.Text::_($additional_info_title).'</div>';
				}
				$addRow .= $itemObject->additional_info;
				$addRow .= '</div>';
				$addRow .= $desc['prefix'] . $desc['price1'] . $desc['price2'];
				$addRow .= $tag['row-addinfo-c'];
			}

			$o = $o . $descRow . $addRow . $tag['row-box-c'];


		} else {
			// ============= TABLE =============

			$rowspanV = 2;
			if ($itemObject->description != '' && ($display_additional_info == 1 && isset($itemObject->additional_info) && $itemObject->additional_info != '')) {
				$rowspanV = 3;
			}

			if ($itemObject->description != '') {
				$rowSpan = 'rowspan="'.$rowspanV.'"';
				$descRow = '<tr>';

				if ($method == 2) {
					$descRow .= $tag['image-o'] . $tag['spaceimg'] . $tag['image-c'];
				}

				$descRow .= $tag['quantity-o']  . $tag['space'] . $tag['quantity-c'];

				if ($displaySecondPrice == 1) {
					$descRow.= $tag['desc2-o'] . $itemObject->description . $tag['desc2-c'];
				} else {
					$descRow.= $tag['desc-o'] . $itemObject->description . $tag['desc-c'];
				}

				$descRow .= $tag['priceprefix-o'] . $tag['space'] . $tag['priceprefix-c']
						  . $tag['price-o'] . $tag['space']  . $tag['price-c'];
				if ($displaySecondPrice == 1) {
					$descRow .= $tag['price2-o'] . $tag['space']  . $tag['price2-c'];
				}



				$descRow .= '</tr>';
			}

			if ($display_additional_info == 1 && isset($itemObject->additional_info) && $itemObject->additional_info != '') {
				$rowSpan = 'rowspan="'.$rowspanV.'"';
				$addRow = '<tr>';

				if ($method == 2) {
					$addRow .= $tag['image-o'] . $tag['spaceimg'] . $tag['image-c'];
				}

				$addRow .= $tag['quantity-o']  . $tag['space'] . $tag['quantity-c'];

				$additionalInfoOutput  = '';
				if ($additional_info_title != '') {
					 $additionalInfoOutput = '<div class="pmaddinfotitle">'.Text::_($additional_info_title).'</div>';
				}

				if ($displaySecondPrice == 1) {
					$addRow.= $tag['addinfo2-o'] . $additionalInfoOutput . $itemObject->additional_info . $tag['addinfo2-c'];
				} else {
					$addRow.= $tag['addinfo-o'] . $additionalInfoOutput . $itemObject->additional_info . $tag['addinfo-c'];
				}

				$addRow .= $tag['priceprefix-o'] . $tag['space'] . $tag['priceprefix-c']
						  . $tag['price-o'] . $tag['space']  . $tag['price-c'];
				if ($displaySecondPrice == 1) {
					$addRow .= $tag['price2-o'] . $tag['space']  . $tag['price2-c'];
				}



				$addRow .= '</tr>';
			}

			$o = '<tr>';

			if ($method == 2) {
				$o .= $tag['image-o'] . $tag['spaceimg'] . $tag['image-c']; // PDF - no rowspan
			} else {
				if ($itemObject->description != '' || ($display_additional_info == 1 && isset($itemObject->additional_info) && $itemObject->additional_info != '')) {
					$o .= $tag['image-rs-o'] . $rowSpan .'>' . $image . $tag['image-c'];
				} else {
					$o .= $tag['image-o'] . $image . $tag['image-c'];
				}
			}

			$o .= $tag['quantity-o'] .$itemObject->quantity . $tag['space'] . $tag['quantity-c'];

			if ($displaySecondPrice == 1) {
				$o .= $tag['title2-o'] . $itemObject->title . $tag['title2-c'];
			} else {
				$o .= $tag['title-o'] . $itemObject->title . $tag['title-c'];
			}

			if ($displaySecondPrice == 1) {
				$o .= $tag['priceprefix-o'] . $pricePref. $tag['space'] . $tag['priceprefix-c']
				. $tag['price-o'] . $price . $tag['price-c'] ;
				if ($price2 == '') {
					$price2 = $tag['space'];
				}
				$o .= $tag['price2-o'] .$price2 . $tag['price2-c'];
			} else {
				$o .= $tag['priceprefix-o'] . $pricePref. $tag['space'] . $tag['priceprefix-c']
				. $tag['price-o'] . $price . $tag['price-c'] ;

			}

			$o .= '</tr>';

			// Description
			if ($itemObject->description != '') {
				$o .= $descRow;
			}
			// Additional Info
			if ($display_additional_info == 1 && isset($itemObject->additional_info) && $itemObject->additional_info != '') {
				$o .= $addRow;
			}
		}



		return $o;
	}


	public static function renderFormItemRE($type, $tag, $groupObject, $itemObject, $pricePref, $method, $price2, $pricePref2, $displaySecondPrice) {

		$app			= Factory::getApplication();
		$paramsC 		= $app->isClient('administrator') ? ComponentHelper::getParams('com_phocamenu') : $app->getParams();
		$item_delimiter	= $paramsC->get( 'item_delimiter', 1 );
		if ($item_delimiter == 1) {
			$item_delimiter = ";";
		} else {
			$item_delimiter = "\t";
		}

		$oPI = array();

		$oPI[] = strip_tags(trim((string)$itemObject->quantity));
		$oPI[] = strip_tags(trim((string)$itemObject->title));
		$oPI[] = strip_tags(trim((string)$itemObject->price));
		$oPI[] = strip_tags(trim((string)$itemObject->price2));
		$oPI[] = strip_tags(trim((string)$itemObject->description));
		$oPI[] = strip_tags(trim((string)$itemObject->additional_info));

		if ($itemObject->imageid == 0) {
			$itemObject->imageid = '';
		}
		$oPI[] = trim((string)$itemObject->imageid);


		if (!empty($oPI)) {

			return implode($item_delimiter, $oPI);
		}
		return '';
	}


	public static function renderFormItemAE($type, $tag, $groupObject, $itemObject, $pricePref, $method, $price2, $pricePref2, $displaySecondPrice) {

		$app			= Factory::getApplication();
		$paramsC 		= $app->isClient('administrator') ? ComponentHelper::getParams('com_phocamenu') : $app->getParams();
		$item_delimiter	= $paramsC->get( 'item_delimiter', 1 );
		if ($item_delimiter == 1) {
			$item_delimiter = ";";
		} else {
			$item_delimiter = "\t";
		}

		$oPI = array();

		$oPI['quantity'] = strip_tags(trim((string)$itemObject->quantity));
		$oPI['title'] = strip_tags(trim((string)$itemObject->title));
		$oPI['price'] = strip_tags(trim((string)$itemObject->price));
		$oPI['price2'] = strip_tags(trim((string)$itemObject->price2));
		$oPI['description'] = strip_tags(trim((string)$itemObject->description));
		$oPI['additional_info'] = strip_tags(trim((string)$itemObject->additional_info));

		if ($itemObject->imageid == 0) {
			$itemObject->imageid = '';
		}
		$oPI['image'] = trim((string)$itemObject->imageid);


		if (!empty($oPI)) {

			return $oPI;
		}
		return array();
	}

	public static function renderFormItemME($type, $tag, $groupObject, $itemObject, $pricePref, $method, $price2, $pricePref2, $displaySecondPrice) {


		// Javascript counts fields (Add row), so they must exist
		if (!isset($itemObject->description)) {
			$itemObject->description = '';
		}
		if (!isset($itemObject->additional_info)) {
			$itemObject->additional_info = '';
		}

		$o = '<tr class="pm-tr-row-'.$groupObject->id.'">'
		. $tag['quantity-o'] .'<input size="8" class="form-control" type="text" name="itemquantity['.$itemObject->id.']" id="itemquantity'.$itemObject->id.'" value="'.$itemObject->quantity.'" />'. $tag['quantity-c']
		. $tag['title-o'] .'<input size="60" class="form-control" type="text" name="itemtitle['.$itemObject->id.']" id="itemtitle'.$itemObject->id.'" value="'.$itemObject->title.'" />' . $tag['title-c']
		. $tag['priceprefix-o'] . $pricePref. $tag['space'] . $tag['priceprefix-c']
		. $tag['price-o'].'<input size="8" class="form-control" type="text" name="itemprice['.$itemObject->id.']" id="itemprice'.$itemObject->id.'" value="'.$itemObject->price.'" />'. $tag['price-c'];
		if ($displaySecondPrice == 1) {
			$o .= $tag['price2-o'].'<input size="8" class="form-control" type="text" name="itemprice2['.$itemObject->id.']" id="itemprice2'.$itemObject->id.'" value="'.$itemObject->price2.'" />'. $tag['price2-c'];
		}
		$o .= '<td align="center"><input type="radio" name="itempublish['.$itemObject->id.']" id="itempublish'.$itemObject->id.'" value="1" '. (((int)$itemObject->published == 1) ? 'checked="checked"' : '' ).' /></td>'
		. '<td align="center"><input type="radio" name="itempublish['.$itemObject->id.']" id="itempublish'.$itemObject->id.'" value="0" '. (((int)$itemObject->published != 1) ? 'checked="checked"' : '' ).' /></td>'

		.'<td align="center"><input id="cb'.$itemObject->id.'" name="itemdelete['.$itemObject->id.']" value="0" onclick="Joomla.isChecked(this.checked);" type="checkbox" /></td>'

		.'</tr>';

		// Display Description in ME in every case, if it is emtpy, display it too
		$tmpl['enablemeeditor'] = 0;
		if ($tmpl['enablemeeditor'] == 1) {
			$editor = \Joomla\CMS\Editor\Editor::getInstance();
			$description = $editor->display( 'itemdesc[' . $itemObject->id .']',  $itemObject->description, '550', '300', '60', '2', array('pagebreak', 'phocadownload', 'readmore') );
		} else {
			$description = '<textarea rows="2" class="form-control" cols="60" name="itemdesc[' . $itemObject->id .']" id="itemdesc' . $itemObject->id .'">'. $itemObject->description . '</textarea>';
		}

		$additionalInfo = '<input size="8" class="form-control" type="text" name="itemadditionalinfo['.$itemObject->id.']" id="itemadditionalinfo'.$itemObject->id.'" value="'.$itemObject->additional_info.'" />';

		if ($displaySecondPrice == 1) {
			$colspan = 4;
		} else {
			$colspan = 3;
		}


		if (isset($itemObject->description)) {
			$o .= '<tr class="pmdesctr pm-tr-row-desc-'.$groupObject->id.'">'
			. $tag['quantity-o']  . $tag['space'] . $tag['quantity-c']
			. $tag['desc-o'] . $description . $tag['desc-c']
			. $tag['priceprefix-o'] . $tag['space'] . $tag['priceprefix-c']
			. $tag['price-o'] . $tag['space'] . $tag['price-c']
			. '<td colspan="'.$colspan.'"></td>'
			.'</tr>';
		}
		if (isset($itemObject->additional_info)) {

			$o .= '<tr class="pmaddinfotr pm-tr-row-addinfo-'.$groupObject->id.'">'
			. $tag['quantity-o']  . $tag['space'] . $tag['quantity-c']
			. $tag['addinfo-o'] . $additionalInfo . $tag['addinfo-c']
			. $tag['priceprefix-o'] . $tag['space'] . $tag['priceprefix-c']
			. $tag['price-o'] . $tag['space'] . $tag['price-c']
			. '<td colspan="'.$colspan.'"></td>'
			.'</tr>';
		}
		return $o;
	}

	public static function renderTaskIconsME($displaySecondPrice) {

		if ($displaySecondPrice == 1) {
			$colspan = 5;
		} else {
			$colspan = 4;
		}

		$o = '<tr>'
		. '<td colspan="'.$colspan.'">'
		. '<td align="center" title="'.Text::_('COM_PHOCAMENU_PUBLISH').'"><div class="icon-publish fa-publish icon-fw phc-green" title="'.Text::_('COM_PHOCAMENU_PUBLISH').'"></div></td>'
		. '<td align="center" title="'.Text::_('COM_PHOCAMENU_UNPUBLISH').'"><div class="icon-delete fa-delete icon-fw phc-red" title="'.Text::_('COM_PHOCAMENU_UNPUBLISH').'"></div></td>'
		. '<td align="center" title="'.Text::_('COM_PHOCAMENU_DELETE').'"><div class="icon-purge fa-purge icon-fw phc-brown" title="'.Text::_('COM_PHOCAMENU_DELETE').'"></div></td>'
		. '</tr>';



		return $o;
	}

	public static function renderGroupHeader($displaySecondPrice, $headerPrice, $headerPrice2, $tag, $method, $type ) {

		$o 				= '';
		$c 				= 0;
		$headerGroup 	= '';

		if ($method == 4 || $method == 5) {
			// ============= BOOTSTRAP 2 | BOOTSTRAP 3=============
			if ($method == 4) {
				$span = 'span';
			} else {
				$span = 'col-xs-12 col-sm-12 col-md-';
			}


			if ($type == 4) {
				$cP = 2;
				$sP1 = 'groupheader1-o-span2';
				$sP2 = 'groupheader2-o-span2';
			} else {
				$cP = 1;
				$sP1 = 'groupheader1-o-span1';
				$sP2 = 'groupheader2-o-span1';
			}
			if ($headerPrice != '') {
				$headerGroup .= $tag[$sP1]	. str_replace(' ', '&nbsp;', $headerPrice) . $tag['groupheader1-c']	;
				$c = $c + $cP;
			}
			if ($displaySecondPrice == 1 && $headerPrice2 != '') {
				$headerGroup .= $tag[$sP2]	. str_replace(' ', '&nbsp;', $headerPrice2) .$tag['groupheader2-c']	;
				$c = $c + $cP;
			}
			$spaceColumn = 12 - (int)$c;
			if ($headerGroup != '') {
				$span = 'col-xs-12 col-sm-'.$spaceColumn.' col-md-'.$spaceColumn;
				$o .= $tag['row-box-o'];
				$o .= $tag['row-group-h-o'] . '<div class="'.$span. '"></div>'. $headerGroup . $tag['row-group-h-c'];
				$o .= $tag['row-box-c'];
			}
		} else {
			// ============= TABLE =============
			if ($headerPrice != '') {
				$headerGroup .= $tag['groupheader1-o']	. str_replace(' ', '&nbsp;', $headerPrice) . $tag['groupheader1-c']	;
			}
			if ($displaySecondPrice == 1 && $headerPrice2 != '') {
				$headerGroup .= $tag['groupheader2-o']	. str_replace(' ', '&nbsp;', $headerPrice2) .$tag['groupheader2-c']	;
			}
			if ($headerGroup != '') {
				$o .= '<tr>';
				$o .=$tag['image-o'] . $tag['space']  . $tag['image-c']; // Images disabled or PDF
				$o .= $tag['quantity-o']  .  $tag['space'] . $tag['quantity-c'];
				if ($displaySecondPrice == 1 ) {
					$o .= $tag['title2-o'] . $tag['space']  . $tag['title2-c'];
				} else {
					$o .= $tag['title-o'] . $tag['space']  . $tag['title-c'];
				}
				$o .= $tag['priceprefix-o'] . $tag['space']  . $tag['priceprefix-c']. $headerGroup;
				$o .= '</tr>';
			}
		}

		return $o;
	}

	public static function renderGroupHeaderME($groupObject, $tag ) {

		$o = '';
		$headerGroup = '';
		if ($groupObject->header_price != '') {
			$headerGroup .= $tag['groupheader1-o']	. '<input size="8" class="form-control" type="text" name="groupheaderprice['.$groupObject->id.']" id="groupheaderprice'.$groupObject->id.'" value="'.$groupObject->header_price.'" />' . $tag['groupheader1-c']	;
		} else {
			$headerGroup .= '<td></td>';
		}
		if ($groupObject->display_second_price == 1 && $groupObject->header_price2 != '') {
			$headerGroup .= $tag['groupheader2-o']	. '<input size="8" class="form-control" type="text" name="groupheaderprice2['.$groupObject->id.']" id="groupheaderprice'.$groupObject->id.'" value="'.$groupObject->header_price2.'" />' .$tag['groupheader1-c']	;
		} else {
			$headerGroup .= '<td></td>';
		}

		if ($headerGroup != '') {
			$o .= '<tr>'
			//. $tag['image-o'] . $tag['space'] . $tag['image-c'] // Images disabled or PDF
			. $tag['quantity-o']  .  $tag['space'] . $tag['quantity-c'];
			if ($groupObject->display_second_price == 1 ) {
				$o .= $tag['title2-o'] . $tag['space'] . $tag['title2-c'];
			} else {
				$o .= $tag['title-o'] . $tag['space'] . $tag['title-c'];
			}
			$o .= $tag['priceprefix-o'] . $tag['space'] . $tag['priceprefix-c']. $headerGroup;
		}

		if ($groupObject->display_second_price == 1) {
			$colspan = 4;
		} else {
			$colspan = 2;
		}

		$o .= '<td colspan="'.$colspan.'"></td>';
		$o .= '</tr>';
		return $o;
	}

	public static function renderFormCompleteRE($oP) {

		$oPO = '<textarea class="form-control" name="menudata" id="menudata" rows="30" style="width: 90%;">';
		if (!empty($oP)) {
			$oPO .= implode("\n", $oP);
		}
		$oPO .= '</textarea>';

		return $oPO;
	}

	public static function getCustomCode($code, $header, $method) {
		$o = '';
		if ($code != '') {
			if ($method == 4) {
				$o .=  '<div class="row fluid-row pm-customcode">'
				 .'<div class="span3 pmclock">'.$code.'</div>'
				 .'<div class="span9 pmtext">' . $header . '</div>'
				 .'</div>';
			} if ($method == 5) {
				$o .=  '<div class="row fluid-row pm-customcode">'
				 .'<div class="col-xs-12 col-sm-3 col-md-3 pmclock">'.$code.'</div>'
				 .'<div class="col-xs-12 col-sm-9 col-md-9 pmtext">' . $header . '</div>'
				 .'</div>';
			}else {
				$o .=  '<table><tr>'
				 .'<td class="pmclock" colspan="2">'.$code.'</td>'
				 .'<td class="pmtext">' . $header . '</td>'
				 .'</tr></table>';
			}
		} else {
			$o .=  $header;
		}
		return $o;
	}

	public static function setMethod($params, $method) {

		$app = Factory::getApplication();
		$print = $app->input->get('print', 0, 'int');
		$admin = $app->input->get('admin', 0, 'int');

		if ($method == 1) {
			return $method;
		}

		if ($print == 1) {
			return $method;
		}
		if ($method == 2) {
			return $method;//PDF
		}
		if ($method == 3) {
			return $method;//Form Multiple EDIT
		}
		if ($method == 6) {
			return $method;//Form Raw Edit
		}

		if ($admin == 1) {
		    if ($params->get( 'html_output_admin', 1 ) == 2 ) {
                return 4;
            }
            if ($params->get( 'html_output_admin', 1 ) == 3 ) {
                return 5;
            }
            if ($params->get( 'html_output_admin', 1 ) == 5 ) {
                return 5;
            }
        } else {
		    if ($params->get( 'html_output', 1 ) == 2 ) {
			    return 4;
		    }
            if ($params->get( 'html_output', 1 ) == 3 ) {
                return 5;
            }
            if ($params->get( 'html_output', 1 ) == 5 ) {
                return 5;
            }
        }



		return $method;
	}

	public static function setMainId($method) {

		$o = '';
		switch($method) {
			case 3:		$o = '<div id="phocamenumultipleedit">';break;
			case 4:		$o = '<div id="phocamenu" class="bts2">';break;
			case 5:		$o = '<div id="phocamenu" class="bts3">';break;
			case 6:		$o = '<div id="phocamenurawedit">';break;
			default:	$o = '<div id="phocamenu">'; break;
		}
		return $o;

	}
}
?>
