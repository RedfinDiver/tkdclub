<?php

namespace Redfindiver\Plugin\Console\Bdreminder\CliCommand;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Registry\Registry;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper as HelperTkdclubHelper;
use Redfindiver\Component\Tkdclub\Administrator\TkdclubHelper;

// Load Library language
$lang = Factory::getLanguage();

// Get the admin panels default language, in this language the email will be sent
$admin_lang = ComponentHelper::getParams('com_languages')->get('administrator');

// Try the cli_tkdclub file in the current language (without allowing the loading of the file in the default language)
$lang->load('plg_console_bdreminder', JPATH_PLUGINS. '/console/bdreminder', $admin_lang, false, false)
	// Fallback to the cli_tkdclub file in the default language
	|| $lang->load('plg_console_bdreminder', JPATH_PLUGINS. '/console/bdreminder', null, true);



class RunBdreminderCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected static $defaultName = 'tkdclub:bdreminder';

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   4.0.0
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{

		$symfonyStyle = new SymfonyStyle($input, $output);

		$symfonyStyle->title(Text::_('PLG_CONSOLE_BDREMINDER'));

		$plugin = PluginHelper::getPlugin('console', 'bdreminder');
		$params = new Registry($plugin->params);

		$states = $params->get('reminder_for_status', array('active'));

		// check for todays birthdays
		$birthdays = $this->getBirthdays($states);

		// no birthdays, abort
		if (!$birthdays) {
			$symfonyStyle->info(Text::_('PLG_CONSOLE_BDREMINDER_NO_BIRTHDAYS'));
			return 0;
		}

		// check for usergroups to receive the reminder email
		$groups = $params->get('reminder_to');

		// no usergroups set, abort
		if (empty($groups)) {
			$symfonyStyle->error(Text::_('PLG_CONSOLE_BDREMINDER_NO_RECIPIENTS'));
			return 0;
		}

		$recipients = $this->getRecipients($groups);

		// Get the email sender
		$Config  = Factory::getConfig();
		$mailFrom = $Config->get('mailfrom');
		$fromName = $params->get('club_name', 'TKDClub Component');

		$nl = "\r\n";
		$email_subject = $fromName . ': ' . Text::_('PLG_CONSOLE_BDREMINDER_EMAIL_SUBJECT');
		$email_body    = Text::_('PLG_CONSOLE_BDREMINDER_EMAIL_BODY');

		$state_texts = array(
			'active' => 'PLG_CONSOLE_BDREMINDER_STATE_ACTIVE',
			'inactive' => 'PLG_CONSOLE_BDREMINDER_STATE_INACTIVE',
			'support' => 'PLG_CONSOLE_BDREMINDER_STATE_SUPPORT'
		);

		foreach ($birthdays as $member) {
			$email_body .= $nl . $nl;
			$email_body .= $member->firstname . ' ' . $member->lastname . $nl;
			$email_body .= Text::_('PLG_CONSOLE_BDREMINDER_AGE') . HelperTkdclubHelper::getAge($member->birthdate) . $nl;
			$email_body .= Text::_('PLG_CONSOLE_BDREMINDER_EMAIL') . $member->email . $nl;
			$email_body .= Text::_('PLG_CONSOLE_BDREMINDER_PHONE') . $member->phone . $nl;
			$email_body .= Text::_('PLG_CONSOLE_BDREMINDER_STATE') . Text::_($state_texts[$member->member_state]) . $nl;
		}

		// Send the emails
		foreach ($recipients as $recipient) {
			$mailer = Factory::getMailer();
			$mailer->setSender(array($mailFrom, $fromName));
			$mailer->addRecipient($recipient);
			$mailer->setSubject($email_subject);
			$mailer->setBody($email_body);
			$mailer->Send();
		}

		$symfonyStyle->text(($email_body));

		$symfonyStyle->success((Text::_('PLG_CONSOLE_BDREMINDER_EMAIL_SUCCSESS')));


		return 0;
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
		$db = Factory::getDBO();
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
		$model = Factory::getApplication()
					->bootComponent('com_tkdclub')
                    ->getMVCFactory()
                    ->createModel('Participant', 'Site');

		return $model->getOrganizerEmails($groups);
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function configure(): void
	{
		$this->setDescription('This command send emails as a reminder for member birthdays');
		$this->setHelp(
			<<<EOF
The <info>%command.name%</info> command send emails as a reminder for member birthdays
<info>php %command.full_name%</info>
EOF
		);
	}
}