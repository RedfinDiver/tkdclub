<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;


/**
 * Model for email to members and newsletter subscribers
 */
class EmailModel extends AdminModel
{
	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm	A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'email',  $options);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_tkdclub.email.data', array());

		return $data;
	}

	/**
	 * Send the email
	 *
	 * @return  boolean
	 */
	public function send()
	{
		$app    = Factory::getApplication();
		$user   = Factory::getUser();
        $params = ComponentHelper::getParams('com_tkdclub');
		$data  = $app->input->post->get('jform', array(), 'array');

		// set sender email to site adress
		$mailfrom = $app->get('mailfrom');
		$fromname = $app->get('fromname');

		// set reply to user that initiated the email 
		$replyemail = $user->get('email');
		$replyname  = $user->get('name');

		// Check for a message body and subject
		if (!array_key_exists('message', $data) || !array_key_exists('subject', $data))
		{
			$app->setUserState('com_tkdclub.email.data', $this->prepareData($data));
			$this->setError(Text::_('COM_TKDCLUB_MAIL_PLEASE_FILL_IN_THE_FORM_CORRECTLY'));

			return false;
		}

		// Check for existing email adresses
		$recipients = $this->getRecipients($data);
		if ($recipients === false) // no recipient selected
		{
			return false;
		}

		if (empty($recipients)) // no email adresses present
		{
			$app->setUserState('com_tkdclub.email.data', $this->prepareData($data));
			$this->setError(Text::_('COM_TKDCLUB_MAIL_NO_EMAIL_ADRESSES'));

			return false;
		}
		
		$attachments  = $this->getAttachments();

		// Get the Mailer
		$mailer = Factory::getMailer();

		// Build email message
		$mailer->setSender(array($mailfrom, $fromname));
		$mailer->setSubject($params->get('mail_prefix') . stripslashes($data['subject']));
		$mailer->setBody($data['message'] . $params->get('mail_signature'));
		$mailer->addReplyTo($replyemail, $replyname);

		// Add recipients
		$mailer->addBcc($recipients);

		// Add attachments
		$mailer->addAttachment($attachments['paths'], $attachments['filenames']);

		// Send the Mail
		$rs = $mailer->Send();

		// Check for an error
		if ($rs instanceof Exception)
		{
			$app->setUserState('com_tkdclub.email.data', $this->prepareData($data));
			$this->setError($rs->getError());

			return false;
		}
		elseif (empty($rs))
		{
			$app->setUserState('com_tkdclub.email.data', $this->prepareData($data));
			$this->setError(Text::_('COM_TKDCLUB_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));

			return false;
		}
		else
		{
			$app->setUserState('com_tkdclub.email.data', array());
			$app->enqueueMessage(Text::plural('COM_TKDCLUB_MAIL_EMAIL_SENT_TO_N_USERS', count($recipients)), 'message');

			return true;
		}
	}

	/**
	 * Prepare the form data for saving in the session
	 */
	public function prepareData($data)
	{	
		/**
		 * Fill the data (specially for the 'active', 'inactive', 'supporter' and 'newsletter':
		 * they could not exist in the array when the box is not checked and in this case, the default value would be
		 * used instead of the '0' one
		 */
		array_key_exists('active', $data) ? $data['active'] = '1' : $data['active'] = '0';
		array_key_exists('inactive', $data) ? $data['inactive'] = '1' : $data['inactive'] = '0';
		array_key_exists('supporter', $data) ? $data['supporter'] = '1' : $data['supporter'] = '0';
		array_key_exists('newsletter', $data) ? $data['newsletter'] = '1' : $data['newsletter'] = '0';

		return $data;
	}

	/**
	 * Get all unique email addresses from database 
	 * 
	 * @return	mixed	array with unique addresses from database
	 * 					array with test adress if given in configuration
	 * 					boolean false when no recipient is selected
	 */
	public function getRecipients($data)
	{	
		// First of all check for test address in configuration
		if ($test_address[] = ComponentHelper::getParams('com_tkdclub')->get('email_test'))
		{
			return $test_address;
		}
		
		$app = Factory::getApplication();

		// array for member recipients
		$member_recipients = array();
		array_key_exists('active', $data) ? $member_recipients[] = 'active' : null;
		array_key_exists('inactive', $data) ? $member_recipients[] = 'inactive' : null;
		array_key_exists('supporter', $data) ? $member_recipients[] = 'support' : null;

		// Check for existing recipients
		if (empty($member_recipients) && !array_key_exists('newsletter', $data) && !is_numeric($data['event']))
		{
			$app->enqueueMessage(Text::_('COM_TKDCLUB_NO_RECIPIENTS_SELECTED'), 'error');
			$app->setUserState('com_tkdclub.email.data', $data);
			
			return false;
		}

		// Go to database and fetch email addresses
		$db = $this->getDbo();
		$email_adresses = array();

		// query recipients from memberstable
		if (!empty($member_recipients))
		{
			$q_members = $db->getQuery(true);
			$q_members->select('DISTINCT ' . $db->quoteName('email'))->from('#__tkdclub_members');
			$query_string = '';
			$or = '';
			foreach ($member_recipients as $value) 
			{
				$query_string .= $or . $db->quoteName('member_state') . ' = ' . $db->quote($value);
				$or = ' OR ';
			}
			$q_members->where('(' . $query_string . ')');
			$q_members->where('NOT ' . $db->quoteName('email') . ' = ' . $db->quote(''));

			$email_adresses = array_merge($email_adresses, $db->setQuery($q_members)->loadColumn());
		}

		// query recipients from newsletter table
		if (array_key_exists('newsletter', $data))
		{
			$q_subscribers = $db->getQuery(true);
			$q_subscribers->select('DISTINCT' . $db->quoteName('email'))->from($db->quoteName('#__tkdclub_newsletter_subscribers'));
			$q_subscribers->where('NOT ' . $db->quoteName('email') . ' = ' . $db->quote(''));

			$email_adresses = array_merge($email_adresses, $db->setQuery($q_subscribers)->loadColumn());
		}

		// query recipients of event
		if (is_numeric($data['event']))
		{
			$q_event_participants = $db->getQuery(true);
			$q_event_participants->select('DISTINCT' . $db->quoteName('email'))->from($db->quoteName('#__tkdclub_event_participants'));
			$q_event_participants->where($db->quoteName('event_id') . ' = ' . (int) $data['event']);
			$q_event_participants->where('NOT ' . $db->quoteName('email') . ' = ' . $db->quote(''));
			$q_event_participants->where($db->quoteName('published') . ' = ' . 1);

			$email_adresses = array_merge($email_adresses, $db->setQuery($q_event_participants)->loadColumn());
		}

		return array_unique($email_adresses);
	}

	/**
	 * Get the attached files from the request
	 * 
	 * @return	array	array of file paths and names
	 */
	public function getAttachments()
	{
		$app   = Factory::getApplication();
		$data  = $app->input->post->get('jform', array(), 'array');
		$files = $app->input->files->get('jform');
		$allowed_extensions = ComponentHelper::getParams('com_tkdclub')->get('allowed_extensions', 'pdf');
		$allowed_extensions_array =  explode(',', $allowed_extensions);

		$attachments = array();
		$paths = array();
		$filenames = array();

		foreach($files as $file)
		{
			if($file['name'] != '' && $file['error'] == (int) 0)
			{	
				// Check for right file extension
				if(!in_array(File::getExt($file['name']), $allowed_extensions_array))
				{
					$app->enqueueMessage(Text::_('COM_TKDCLUB_EMAIL_ATTACHMENT_NOT_ALLOWED') . $allowed_extensions, 'error');
					$app->setUserState('com_tkdclub.email.data', $data);
					
					return false;	
				}

				$paths[] = $file['tmp_name'];
				$filenames[] = $file['name'];
			}
		}

		if(empty($paths))
		{
			return false;
		}

		$attachments['paths'] = $paths;
		$attachments['filenames'] = $filenames;

		return $attachments;
	}
}