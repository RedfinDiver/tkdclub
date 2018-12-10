<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;


/**
 * Model for email to members and newsletter subscribers
 */
class TkdclubModelEmail extends JModelAdmin
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
		$data   = $app->input->post->get('jform', array(), 'array');

		$subject      = array_key_exists('subject', $data) ? $data['subject'] : '';
		$message_body = array_key_exists('message', $data) ? $data['message'] : '';
		$active       = array_key_exists('active', $data) ? (int) $data['active'] : 0;
		$inactive     = array_key_exists('inactive', $data) ? (int) $data['inactive'] : 0;
		$supporter    = array_key_exists('supporter', $data) ? (int) $data['supporter'] : 0;

		$recipients   = $this->getRecipients();
		$attachments  = $this->getAttachments();
		

		// Check for a message body and subject
		if (!$message_body || !$subject)
		{
			$app->setUserState('com_tkdclub.email.data', $data);
			$this->setError(Text::_('COM_TKDCLUB_MAIL_PLEASE_FILL_IN_THE_FORM_CORRECTLY'));

			return false;
		}

		// Get the Mailer
		$mailer = Factory::getMailer();

		// Build email message
		$mailer->setSender(array($user->get('email'), $user->get('name')));
		$mailer->setSubject($params->get('mail_prefix') . stripslashes($subject));
		$mailer->setBody($message_body . $params->get('mail_signature'));

		// Add recipients
		$mailer->addBcc($recipients);

		// Add attachments
		$mailer->addAttachment($attachments['paths'], $attachments['filenames']);

		// Send the Mail
		$rs = $mailer->Send();

		// Check for an error
		if ($rs instanceof Exception)
		{
			$app->setUserState('com_tkdclub.email.data', $data);
			$this->setError($rs->getError());

			return false;
		}
		elseif (empty($rs))
		{
			$app->setUserState('com_tkdclub.email.data', $data);
			$this->setError(Text::_('COM_TKDCLUB_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));

			return false;
		}
		else
		{
			/**
			 * Fill the data (specially for the 'active', 'inactive', 'supporter' and (in future versions) 'newsletter':
			 * they could not exist in the array when the box is not checked and in this case, the default value would be
			 * used instead of the '0' one
			 */
			$data['supporter'] = $supporter;
            $data['active']    = $active;
            $data['inactive']  = $inactive;  
 			$data['subject']   = $subject;
			$data['message']   = $message_body;
			// TODO prepared for future versions
            // $data['newsletter'] = $newsletter;

			$app->setUserState('com_tkdclub.email.data', array());
			$app->enqueueMessage(Text::plural('COM_TKDCLUB_MAIL_EMAIL_SENT_TO_N_USERS', count($recipients)), 'message');

			return true;
		}
	}

	/**
	 * Get all unique email addresses from database 
	 * 
	 * @return	mixed	array with unique addresses from database
	 * 					array with test adress if given in configuration
	 */
	public function getRecipients()
	{	
		// First of all check for test address in configuration
		if ($test_address[] = ComponentHelper::getParams('com_tkdclub')->get('email_test'))
		{
			return $test_address;
		}

		$app = Factory::getApplication();
		$input = $app->input->post->get('jform', array(), 'array');
		
		// Create an array with recipients from the members table
		$member_recipients = array();
		array_key_exists('active', $input) ? $member_recipients[] = 'active' : null;
		array_key_exists('inactive', $input) ? $member_recipients[] = 'inactive' : null;
		array_key_exists('supporter', $input) ? $member_recipients[] = 'support' : null;

		// TODO Message to newsletter subscribers
		// array_key_exists('newsletter', $input) ? $newsletter = 'newsletter' : null;

		// Check for existing recipients
		if (empty($member_recipients) && !$newsletter)
		{
			$app->enqueueMessage(Text::_('COM_TKDCLUB_NO_RECIPIENTS_SELECTED'), 'error');
			$app->setUserState('com_tkdclub.email.data', $input);
			
			return false;
		}

		// Go to database and fetch email addresses
		$db = $this->getDbo();

		// First build the query for the memberstable
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
		}

		/* TODO Build query for newsletter subscribers table
		if ($newsletter)
		{
			$q_subscribers = $db->getQuery(true);
			$q_subscribers->select('DISTINCT' . $db->quoteName('email'))->from($db->quoteName('#__tkdclub_newsletter_subscribers'));
			$q_subscribers->where('NOT ' . $db->quoteName('email') . ' = ' . $db->quote(''));
		}
		*/		
		
		/* TODO Now determine what query has to run
		if (!empty($member_recipients) && $newsletter)
		{
			$query = $q_members->union($q_subscribers);
		}

		if (empty($member_recipients) && $newsletter)
		{
			$query = $q_subscribers;
		}

		if (!empty($member_recipients) && !$newsletter)
		{
			$query = $q_members;
		}
		*/

		$query = $q_members;

		return $db->setQuery($query)->loadColumn();
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