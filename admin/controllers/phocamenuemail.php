<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Mail\MailHelper;
class PhocaMenuCpControllerPhocaMenuEmail extends PhocaMenuControllerForm
{
	protected $option 	= 'com_phocamenu';
	protected $typeview	= 'email';
	public $typeAlias 	= 'com_phocamenu.phocamenuemail';



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


		$this->setRedirect(Route::_('index.php?option=com_phocamenu'.$this->getRedirectToListAppend(1), false));
	}

	/*
	 * changed redirects
	 */
	 /*
	 public function cancel($key = null)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= Factory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId	= $app->input->getInt($key);

		// Attempt to check-in the current record.
		if ($recordId) {
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId)) {
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(Text::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
				$this->setMessage($this->getError(), 'error');
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

				return false;
			}

			if ($checkin) {
				if ($model->checkin($recordId) === false) {
					// Check-in failed, go back to the record and display a notice.
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1));

					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context.'.data',	null);

		$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

		return true;
	}*/

	/*
	 * added loading of id if there is no cid
	 * changed redirects
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		// Initialise variables.
		$app		= Factory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		$context	= "$this->option.edit.$this->context";
		$append		= '';

		if (empty($key)) {
			$key = $table->getKeyName();
		}

		//Language
		$filterLanguage		= Factory::getApplication()->input->get('filter_language', array(), 'post', 'string');
		$model->setLangAndLoadContent($filterLanguage);

		// Try to find config by type (only one id used)
		$existingId = 0;
		if(empty($cid) || (isset($cid[0]) && (int)$cid == 0)) {
			$existingId = $this->getExistingId();
		}
		if ((int)$existingId > 0) {
			$cid[0] = $existingId;
		}


		// Get the previous record id (if any) and the current record id.
		$recordId	= (int) (count($cid) ? $cid[0] : 0);
		//$recordId	= (int) (count($cid) ? $cid[0] : $app->input->getInt($key));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			//$this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
			$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId)) {
			// Check-out failed, display a notice but allow the user to see the record.
			//$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key));

			return false;
		}
		else {
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context.'.data', null);
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key));

			return true;
		}
	}

	// Solved in PhocaMenuControllerForm construct
	//public function sendandsave(){}

	/*
	 * changed redirects
	 */

	public function save($key = NULL, $urlVar = NULL)
	{

		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= Factory::getApplication();
		$lang		= Factory::getLanguage();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$data		= Factory::getApplication()->input->get('jform', array(), 'post', 'array');
		//$message	= JFactory::getApplication()->input->get('message', '', 'post', 'array');
		//$data['messagemail'] = $message[0];
		//$data['messagemail']= JFactory::getApplication()->input->get( 'message', null, '', 'html' );
		$data['messagemail']= $app->input->get( 'message', '', 'raw');

		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$task		= $this->getTask();

		if (empty($key)) {
			$key = $table->getKeyName();
		}



		$recordId	= $app->input->getInt($key);


		$session	= Factory::getSession();
		$registry	= $session->get('registry');

		// PHOCAEDIT
		if (empty($recordId) && isset($data['id']) && (int)$data['id'] > 0 ) {
			$recordId = (int)$data['id'];
		} else {
			if (!$this->checkEditId($context, $recordId)) {
				// Somehow the person just went to the form and saved it - we don't allow that.
				//$this->setError(Text::_('JLIB_APPLICATION_ERROR_UNHELD_ID'));
				$this->setMessage(Text::_('JLIB_APPLICATION_ERROR_UNHELD_ID'), 'error');
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

				return false;
			}
		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Check-in the original row.
			if ($checkin  && $model->checkin($data[$key]) === false) {
				// Check-in failed, go back to the item and display a notice.
				//$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
				$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId,'id',1));

				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data[$key]	= 0;
			$task		= 'apply';
		}

		// Access check.
		if (!$this->allowSave($data)) {
			//$this->setError(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
			$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		if (!isset($data['id'])) { $data['id'] = 0;}
		// Test if the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key,1), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Redirect back to the edit screen.
			$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
			$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1), false));

			return false;
		}

		// Save succeeded, check-in the record.

		if ($checkin && $model->checkin($validData[$key]) === false) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Check-in failed, go back to the record and display a notice.

			$app->enqueueMessage(Text::sprintf('Error_Checkin_saved', $model->getError()), 'error');
			$this->setRedirect('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key, 1));

			return false;
		}

		$this->setMessage(Text::_(($lang->hasKey($this->text_prefix.'_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') .  '_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context.'.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToItemAppend($recordId, $key), false));
				break;

			case 'sendandsave':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Doesn't return true or false, failure message instead of false
				$msg		= '';
				$errorMsg 	= '';

				PhocaMenuCpControllerPhocaMenuEmail::sendEmail($data, $errorMsg);

				if ($errorMsg != '') {
					$msg .= $errorMsg . '<br />' . Text::_('COM_PHOCAMENU_EMAIL_NOT_SENT');
				} else {
					$msg .= Text::_( 'COM_PHOCAMENU_EMAIL_IF_NO_ERROR_EMAIL_SENT' ) . '.';
				}

				// Redirect to the list screen.
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false), $msg);
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false));
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model);

		return true;
	}



	protected function getExistingId() {
		// Only one id for the one type
		$typeValue	= Factory::getApplication()->input->get('type', 0, '', 'int');

		//Language
		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}
		$app 	= Factory::getApplication('administrator');
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');


		$db = Factory::getDBO();
		$query = 'SELECT a.id'
				.' FROM #__phocamenu_email AS a'
				.' WHERE a.type = '.(int)$typeValue
				.' AND a.language = '.$db->Quote($language);
		$db->setQuery($query);
		$dataId = $db->loadObject();


		if (isset($dataId->id) && (int)$dataId->id > 0) {
			return $dataId->id;
		}

		return false;
	}



	function send() {


		$app		= Factory::getApplication();
		$post 		= $app->input->post->getArray();
		$post				= $post['jform'];


		$cid				= Factory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );
		$post['id'] 		= (int) $cid[0];//only one item in the database for every view
		$aUrl				= PhocaMenuHelper::getUrlApend($this->typeview);
		$post['messagemail']= $app->input->get( 'message', '', 'raw');
		$post['message']	= '';// it is automatically generated, cannot be saved here (into the database)
		$post['published']	= 1;
		$append		= '';
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$layout		= Factory::getApplication()->input->get('layout', 'edit');


		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}
		if ($layout) {
			$append .= '&layout='.$layout;
		}

		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$model 	= $this->getModel( 'phocamenuemail' );

		// Doesn't return true or false, failure message instead of false
		$msg		= '';
		$errorMsg 	= '';
		PhocaMenuCpControllerPhocaMenuEmail::sendEmail($post, $errorMsg);
		/*if ( PhocaMenuCpControllerPhocaMenuEmail::sendEmail($post)) {
			$msg .= ". ".Text::_( 'Email sent' );
		} else {
			$msg .= ". ".Text::_( 'Email not sent' );
		}*/
		if ($errorMsg != '') {
			$msg .= $errorMsg . '<br />' . Text::_('COM_PHOCAMENU_EMAIL_NOT_SENT');
		} else {
			$msg .= Text::_( 'COM_PHOCAMENU_EMAIL_IF_NO_ERROR_EMAIL_SENT' ) . '.';
		}

		$this->setRedirect(Route::_('index.php?option='.$this->option.$this->getRedirectToListAppend(1), false), $msg);

	}

	function sendEmail($post, &$errorMsg) {

		$app		= Factory::getApplication();
		$db 		= Factory::getDBO();
		$siteName 	= $app->get( 'sitename' );
		$document	= Factory::getDocument();



		jimport('joomla.mail.helper');
		// FROM
		if (isset($post['from']) && $post['from'] != '' && MailHelper::isEmailAddress($post['from'])) {
			$from = $post['from'];
		} else {
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE LOWER( name ) = "super user"';
			$db->setQuery( $query );
			$userData = $db->loadObject();
			if (isset($userData->email) && $userData->email != '') {
				$from = $userData->email;
			} else {
				$errorMsg = Text::_('COM_PHOCAMENU_NO_EMAIL_FROM_FOUND' );
				return false;
			}
		}

		// FROM NAME
		if (isset($post['fromname']) && $post['fromname'] != '') {
			$fromName = $post['fromname'];
		} else {
			$fromName = $app->get( 'sitename' );
		}

		// TO
		if (isset($post['to']) && $post['to'] != '') {
			$to	= trim( $post['to'] );
			$to = explode( ',', $to);
		} else {
			$to = array();
		}

		// CC
		if (isset($post['cc']) && $post['cc'] != '') {
			$cc	= trim( $post['cc'] );
			$cc = explode( ',', $cc);
		} else {
			$cc = array();
		}

		// BCC
		if (isset($post['bcc']) && $post['bcc'] != '') {
			$bcc	= trim( $post['bcc'] );
			$bcc 	= explode( ',', $bcc);
		} else {
			$bcc = array();
		}

		if (isset($post['subject']) && $post['subject'] != '') {
			$subject	= $post['subject'];
		} else {
			$subject	= Text::_('Menu');
		}

		if (isset($post['messagemail']) && $post['messagemail'] != '') {
			$message	= $post['messagemail'];
		} else {
			$message	= '';
		}


		// Remove images
		$pattern 		= '/<img(.*)>/Ui';
		$replacement 	= '';
		$message 		= preg_replace($pattern, $replacement, $message);


		$htmlMessage ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
		.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$document->language.'" lang="'.$document->language.'" dir="'.$document->direction.'" >'
		.'<head>'
		.'<meta http-equiv="content-type" content="text/html; charset=utf-8" />'
		.'<title>'.$subject.'</title></head>'
		.'<body>'
		.$message
		.'</body></html>';

		// Check the email addresses
		$wrongTo 	= array();
		$wrongCc 	= array();
		$wrongBcc 	= array();

		foreach ($to as $kt => $vt) {
			if ( $vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($to[$kt]);
			}
			$vt 		= trim($vt);
			if (!MailHelper::isEmailAddress($vt)) {
				$wrongTo[] = $vt;
			} else {
				$to[$kt] = $vt;
			}
		}

		if (!empty($wrongTo)) {
			$errorMsg = Text::_('COM_PHOCAMENU_INCORRECT_EMAIL_TO' ) . ': ';
			foreach ($wrongTo as $key => $value) {
				$errorMsg .= $value . '<br />';
			}
			return false;
		}

		foreach ($cc as $kt => $vt) {
			if ($vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($cc[$kt]);
			}
			$vt 		= trim($vt);
			if (!MailHelper::isEmailAddress($vt)) {
				$wrongCc[] = $vt;
			} else {
				$cc[$kt] = $vt;
			}
		}

		if (!empty($wrongCc)) {
			$errorMsg = Text::_('COM_PHOCAMENU_INCORRECT_EMAIL_CC' ) . ': ';
			foreach ($wrongCc as $key => $value) {
				$errorMsg .= $value . '<br />';
			}
			return false;
		}

		foreach ($bcc as $kt => $vt) {
			if ($vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($bcc[$kt]);
			}
			$vt 		= trim($vt);
			if (!MailHelper::isEmailAddress($vt)) {
				$wrongBcc[] = $vt;
			} else {
				$bcc[$kt] = $vt;
			}
		}


		if (!empty($wrongBcc)) {
			$errorMsg = Text::_('COM_PHOCAMENU_INCORRECT_EMAIL_BCC' ) . ': ';
			foreach ($wrongBcc as $key => $value) {
				$errorMsg .= $value . '<br />';
			}
			return false;
		}

		$replyto 		= $from;
		$replytoname	= $fromName;


		$mail = Factory::getMailer();
		//$mail->sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null, $replyTo = null, $replyToName = null)
		//sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null,$replyTo = null, $replyToName = null)

		if ($mail->sendMail($from, $fromName, $to, $subject, $htmlMessage, true, $cc, $bcc, null, $replyto, $replytoname)) {
			return true;
		} else {
			return false;
		}
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

	protected function getRedirectToListAppend($bUrlUse = 0)
	{
		$tmpl		= Factory::getApplication()->input->get('tmpl');
		$append		= '';
		$aUrl		= PhocaMenuHelper::getUrlApend($this->typeview);
		$bUrl		= PhocaMenuHelper::getUrlApend($this->typeview, 1);

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		if ($bUrlUse == 1) {
			return $append . $bUrl;
		} else {
			return '&view='.$this->view_list.$append . $aUrl;
		}
	}
}
?>
