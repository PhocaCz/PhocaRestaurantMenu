<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class PhocaMenuCpControllerPhocaMenuRawEdit extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'rawedit';
	public $typeAlias 	= 'com_phocamenu.phocamenurawedit';


	public function cancel($key = NULL)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= Factory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$layout		= Factory::getApplication()->input->get('layout', 'edit');
		$append		= '';

		// Clean the session data and redirect.
		$app->setUserState($context.'.id',		null);
		$app->setUserState($context.'.data',	null);

		//$this->setRedirect(JRoute::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
		// WHEN RAW EDIT SAVES - there are new IDs for lists/days/groups/items so we go back to root
		$this->setRedirect(Route::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(2), false));
	}

	public function edit($key = NULL, $urlVar = NULL)
	{
		// Initialise variables.
		$app		= Factory::getApplication();
		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JFactory::getApplication()->input->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$layout		= Factory::getApplication()->input->get('layout', 'edit');
		$append		= '';
		$recordId	= '';

		$app->setUserState($context.'.id',	$recordId);
		$app->setUserState($context.'.data', null);
		$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend());
		return true;
	}



	function save($key = NULL, $urlVar = NULL) {

		$app		= Factory::getApplication();
		$post 		= $app->input->post->getArray();

		//$get		= $app->input->get->getArray();


		//$model		= $this->getModel();
		//$table		= $model->getTable();
		//$cid		= JFactory::getApplication()->input->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$layout		= Factory::getApplication()->input->get('layout', 'edit');
		$append		= '';

		$msg		= '';
		$errorMsg	= '';
		$model 	= $this->getModel( 'phocamenurawedit' );

		$return	= $model->save($post, $errorMsg);
		if ($return) {
			$msg 	.= Text::_( 'COM_PHOCAMENU_SUCCESS_MODIFICATION_SAVED_RAW' );
		} else {
			$errorMsg 	.= Text::_( 'COM_PHOCAMENU_ERROR_MODIFICATION_SAVED_RAW' );
		}

		if ($errorMsg != '') {
			$msg .= ' '.$errorMsg. ' ';
			$app->enqueueMessage($msg, 'error');
		} else {
			$app->enqueueMessage($msg, 'message');
		}



		switch ( Factory::getApplication()->input->get('task') ) {
			case 'apply':
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToItemAppend(), false));
			break;

			case 'save':
			default:
				// WHEN RAW EDIT SAVES - there are new IDs for lists/days/groups/items so we go back to root
				$this->setRedirect(Route::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(2), false));

			break;
		}

		return true;
	}


	function export() {
		$app		= Factory::getApplication();
		$post 		= $app->input->post->getArray();

		$file		= $post['menudata'];
		$lang		= $post['language'];

		$lang		= str_replace('*', '', strip_tags($lang));
		if ($lang != '') {
			$lang = '-'.$lang;
		}


		if (function_exists('mb_strlen')) {
			$fileSize = mb_strlen($file, '8bit');
		} else {
			$fileSize = strlen($file);
		}
		$mimeType = 'text/plain';

		// Clean the output buffer
		ob_end_clean();

		// test for protocol and set the appropriate headers
		jimport( 'joomla.environment.uri' );
		$_tmp_uri 		= Uri::getInstance( Uri::current() );
		$_tmp_protocol 	= $_tmp_uri->getScheme();
		if ($_tmp_protocol == "https") {
			// SSL Support
			header('Cache-Control: private, max-age=0, must-revalidate, no-store');
		} else {
			header("Cache-Control: public, must-revalidate");
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header("Pragma: no-cache");
			header("Expires: 0");
		} /* end if protocol https */
		header("Content-Description: File Transfer");
		header("Expires: Sat, 30 Dec 1990 07:07:07 GMT");
		header("Accept-Ranges: bytes");
		// Modified by Rene
		// HTTP Range - see RFC2616 for more informations (http://www.ietf.org/rfc/rfc2616.txt)
		$httpRange   = 0;
		$newFileSize = $fileSize - 1;
		// Default values! Will be overridden if a valid range header field was detected!
		$resultLenght = (string)$fileSize;
		$resultRange  = "0-".$newFileSize;
		// We support requests for a single range only.
		// So we check if we have a range field. If yes ensure that it is a valid one.
		// If it is not valid we ignore it and sending the whole file.
		if(isset($_SERVER['HTTP_RANGE']) && preg_match('%^bytes=\d*\-\d*$%', $_SERVER['HTTP_RANGE'])) {
			// Let's take the right side
			list($a, $httpRange) = explode('=', $_SERVER['HTTP_RANGE']);
			// and get the two values (as strings!)
			$httpRange = explode('-', $httpRange);
			// Check if we have values! If not we have nothing to do!
			if(!empty($httpRange[0]) || !empty($httpRange[1])) {
				// We need the new content length ...
				$resultLenght	= $fileSize - $httpRange[0] - $httpRange[1];
				// ... and we can add the 206 Status.
				header("HTTP/1.1 206 Partial Content");
				// Now we need the content-range, so we have to build it depending on the given range!
				// ex.: -500 -> the last 500 bytes
				if(empty($httpRange[0]))
					$resultRange = $resultLenght.'-'.$newFileSize;
				// ex.: 500- -> from 500 bytes to filesize
				elseif(empty($httpRange[1]))
					$resultRange = $httpRange[0].'-'.$newFileSize;
				// ex.: 500-1000 -> from 500 to 1000 bytes
				else
					$resultRange = $httpRange[0] . '-' . $httpRange[1];
				//header("Content-Range: bytes ".$httpRange . $newFileSize .'/'. $fileSize);
			}
		}
		header("Content-Length: ". $resultLenght);
		header("Content-Range: bytes " . $resultRange . '/' . $fileSize);
		header("Content-Type: " . (string)$mimeType);
		header('Content-Disposition: attachment; filename="prm-export'.$lang.'.txt"');
		header("Content-Transfer-Encoding: binary\n");


		echo $file;
		flush();
		exit;
	}

	protected function getRedirectToItemAppend($recordId = null, $key = 'id', $bUrlUse = 0)
	{
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$layout		= Factory::getApplication()->input->get('layout', 'edit');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);
		$bUrl		= PhocaMenuHelper::getUrlApend($this->typeview, 1);



		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		if ($layout) {
			$append .= '&layout='.$layout;
		}

		if ($recordId) {
			$append .= '&'.$key.'='.$recordId;
		}

		if ($bUrlUse == 1) {
			return $append . $bUrl;
		} else {
			return '&view='.$this->view_item.$append . $aUrl;
		}
	}

	protected function getRedirectToListAppend($bUrlUse = 0) {
		$tmpl   = Factory::getApplication()->input->get('tmpl');
		$append = '';
		$aUrl   = PhocaMenuHelper::getUrlApend($this->typeview);

		if ((int)$bUrlUse > 0) {
			$bUrl = PhocaMenuHelper::getUrlApend($this->typeview, (int)$bUrlUse);
		}

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		if ((int)$bUrlUse > 0) {
			return $append . $bUrl;
		} else {
			return '&view='.$this->view_list.$append . $aUrl;
		}
	}

}
?>
