<?php

// Set flag that this is a parent file.
const _JEXEC = 1;

use Redfindiver\Tkdclub\Administrator\Helper;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_tkdclub');

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load Library language
$lang = JFactory::getLanguage();

// Get the admin panels default language, in this language the email will be sent
$admin_lang = JComponentHelper::getParams('com_languages')->get('administrator');

// Try the cli_tkdclub file in the current language (without allowing the loading of the file in the default language)
$lang->load('cli_tkdclub', JPATH_COMPONENT_ADMINISTRATOR, $admin_lang, false, false)
	// Fallback to the cli_tkdclub file in the default language
	|| $lang->load('cli_tkdclub', JPATH_COMPONENT_ADMINISTRATOR, null, true);

class TkdclubBirthdayreminder extends JApplicationCli
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 */
	public function doExecute()
	{
		$params = JComponentHelper::getComponent('com_tkdclub')->params;
		$states = $params->get('reminder_for_status', array('active'));

		// check for todays birthdays
		$birthdays = $this->getBirthdays($states);

		// no birthdays, abort
		if (!$birthdays) {
			$this->out(JText::_('CLI_TKDCLUB_BDREMINDER_NO_BIRTHDAYS'));
			return;
		}

		// check for usergroups to receive the reminder email
		$groups = $params->get('reminder_to');

		// no usergroups set, abort
		if (empty($groups)) {
			$this->out(JText::_('CLI_TKDCLUB_BDREMINDER_NO_RECIPIENTS'));
			return;
		}

		$recipients = $this->getRecipients($groups);

		// Get the email sender
		$Config  = JFactory::getConfig();
		$mailFrom = $Config->get('mailfrom');
		$fromName = $params->get('club_name', 'TKDClub Component');

		$nl = "\r\n";
		$email_subject = $fromName . ': ' . JText::_('CLI_TKDCLUB_BDREMINDER_EMAIL_SUBJECT');
		$email_body    = JText::_('CLI_TKDCLUB_BDREMINDER_EMAIL_BODY');

		JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

		$state_texts = array(
			'active' => 'CLI_TKDCLUB_BDREMINDER_STATE_ACTIVE',
			'inactive' => 'CLI_TKDCLUB_BDREMINDER_STATE_INACTIVE',
			'support' => 'CLI_TKDCLUB_BDREMINDER_STATE_SUPPORT'
		);

		foreach ($birthdays as $member) {
			$email_body .= $nl . $nl;
			$email_body .= $member->firstname . ' ' . $member->lastname . $nl;
			$email_body .= JText::_('CLI_TKDCLUB_BDREMINDER_AGE') . Helper::getAge($member->birthdate) . $nl;
			$email_body .= JText::_('CLI_TKDCLUB_BDREMINDER_EMAIL') . $member->email . $nl;
			$email_body .= JText::_('CLI_TKDCLUB_BDREMINDER_PHONE') . $member->phone . $nl;
			$email_body .= JText::_('CLI_TKDCLUB_BDREMINDER_STATE') . JText::_($state_texts[$member->member_state]) . $nl;
		}

		// Send the emails
		foreach ($recipients as $recipient) {
			$mailer = JFactory::getMailer();
			$mailer->setSender(array($mailFrom, $fromName));
			$mailer->addRecipient($recipient);
			$mailer->setSubject($email_subject);
			$mailer->setBody($email_body);
			$mailer->Send();
		}
	}

	/**
	 * Fetch data from database for today birthdays
	 * 
	 * @param	array		array with states to look for
	 * 
	 * @return	object		when there are members with todays birthday
	 * 			boolean	 	false when no members with birthday are found
	 */
	public function getBirthdays($states)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->qn('#__tkdclub_members'));
		$query->where('DAY(' . $db->qn('birthdate') . ')' . ' = '
			. 'DAY(CURDATE())');

		$query->where('MONTH(' . $db->qn('birthdate') . ')' . ' = '
			. 'MONTH(CURDATE())');

		// now building the where with or clause
		$memberstates = "'" . implode("','", $states) . "'";
		$query->where($db->qn('member_state') . ' IN (' . $memberstates . ')');

		$db->setQuery($query);

		return $db->loadObjectList() ? $db->loadObjectList() : false;
	}

	/**
	 * Get the email adresses for the usergroups
	 * 
	 * @param	array	array with integers
	 * 
	 * @return	array	indexed array with email adresses
	 */
	private function getRecipients($groups)
	{
		// Use the model from site to get the recipients
		JLoader::register('TkdClubModelParticipant', JPATH_SITE . '/components/com_tkdclub/models/participant.php');
		$model = new TkdClubModelParticipant;

		return $model->getOrganizerEmails($groups);
	}
}

JApplicationCli::getInstance('TkdclubBirthdayreminder')->execute();
