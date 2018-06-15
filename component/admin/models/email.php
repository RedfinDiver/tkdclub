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
		$form = $this->loadForm('com_tkdclub.email', 'email', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = Factory::getApplication()->getUserState('com_tkdclub.display.email.data', array());

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
		$access = new JAccess;
        $params = ComponentHelper::getParams('com_tkdclub');
		
		// Get the data from the input
		$data   = $app->input->post->get('jform', array(), 'array');
		$subject      = array_key_exists('subject', $data) ? $data['subject'] : '';
		$message_body = array_key_exists('message', $data) ? $data['message'] : '';

		$recipients = $this->getRecipients();

		return;

		// Automatically removes html <formatting></formatting>
        $message_body = JFilterInput::getInstance()->clean($message_body, 'string');

		// Check for a message body and subject
		if (!$message_body || !$subject)
		{
			$app->setUserState('com_tkdclub.display.email.data', $data);
			$this->setError(Text::_('COM_TKDCLUB_MAIL_PLEASE_FILL_IN_THE_FORM_CORRECTLY'));

			return false;
		}
                
		if (!$params->get('email_test')) // If test-email ist empty, proceed
		{
			
		}				
		elseif ($params->get('email_test')) //when test email ist set, just set it as only one
		{
			$rows = array($params->get('email_test'));
		}

		// Get the Mailer
		$mailer = Factory::getMailer();
		$params = ComponentHelper::getParams('com_tkdclub');

		// Build email message
		$mailer->setSender(array($user->get('email'), $user->get('name')));
		$mailer->setSubject($params->get('mail_prefix') . stripslashes($subject));
		$mailer->setBody($message_body . $params->get('mail_signature'));

		// Add recipients
		$mailer->addBcc($rows);
		$mailer->addRecipient($user->get('email')); //copy to sender

		// Send the Mail
		$rs = $mailer->Send();

		// Check for an error
		if ($rs instanceof Exception)
		{
			$app->setUserState('com_tkdclub.display.email.data', $data);
			$this->setError($rs->getError());

			return false;
		}
		elseif (empty($rs))
		{
			$app->setUserState('com_tkdclub.display.email.data', $data);
			$this->setError(Text::_('COM_TKDCLUB_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));

			return false;
		}
		else
		{
			/**
			 * Fill the data (specially for the 'mode', 'group' and 'bcc': they could not exist in the array
			 * when the box is not checked and in this case, the default value would be used instead of the '0'
			 * one)
			 */
			$data['supporter'] = $supporter;
                        $data['active']    = $active;
                        $data['inactive']  = $inactive;
  			$data['bcc']       = $bcc;    
                        //prepared for future versions
                        //$data['newsletter'] = $newsletter;
 			$data['subject']   = $subject;
			$data['message']   = $message_body;
			$app->setUserState('com_tkdclub.display.email.data', array());
			$app->enqueueMessage(Text::plural('COM_TKDCLUB_MAIL_EMAIL_SENT_TO_N_USERS', count($rows)), 'message');

			return true;
		}
	}

	/**
	 * Get all unique email addresses from database 
	 * 
	 * @return	array	array with unique addresses
	 * 
	 */
	public function getRecipients()
	{	
		// First of all check for test address in configuration
		if ($test_address = ComponentHelper::getParams('com_tkdclub')->get('email_test'))
		{
			return $test_address;
		}

		$app = Factory::getApplication();
		$input  = $app->input->post->get('jform', array(), 'array');
		
		// Create an array with recipients from the members table
		$member_recipients = array();
		array_key_exists('active', $input) ? $member_recipients[] = 'active' : null;
		array_key_exists('inactive', $input) ? $member_recipients[] = 'inactive' : null;
		array_key_exists('supporter', $input) ? $member_recipients[] = 'support' : null;

		// Message to newsletter subscribers
		array_key_exists('newsletter', $input) ? $newsletter = 'newsletter' : null;

		// Check for existing recipients
		if (empty($member_recipients) && !$newsletter)
		{
			$app->enqueueMessage(Text::_('COM_TKDCLUB_NO_RECIPIENTS_SELECTED'), 'error');
			$app->setUserState('com_tkdclub.display.email.data', $input);
			
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

		// Build query for newsletter subscribers table
		if ($newsletter)
		{
			$q_subscribers = $db->getQuery(true);
			$q_subscribers->select('DISTINCT' . $db->quoteName('email'))->from($db->quoteName('#__tkdclub_newsletter_subscribers'));
			$q_subscribers->where('NOT ' . $db->quoteName('email') . ' = ' . $db->quote(''));
		}		
		
		// Now determine what query has to run
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

		return $db->setQuery($query)->loadColumn();
	} 
}