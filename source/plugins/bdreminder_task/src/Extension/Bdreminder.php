<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Redfindiver\Plugin\Task\Bdreminder\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper as HelperTkdclubHelper;

/**
 * Task plugin to send email reminders for member birthdays
 *
 * @since  5.0.0
 */
final class Bdreminder extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;
    use DatabaseAwareTrait;

    /**
     * @var string[]
     * @since 4.1.0
     */
    protected const TASKS_MAP = [
        'birthday.remind' => [
            'langConstPrefix' => 'PLG_TASK_BDREMINDER',
            'form'            => 'settings',
            'method'          => 'remindBirthdays',
        ],
    ];

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return string[]
     *
     * @since 4.1.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    /**
     * @var boolean
     * @since 4.1.0
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor.
     *
     * @param   DispatcherInterface  $dispatcher     The dispatcher
     * @param   array                $config         An optional associative array of configuration settings
     *
     * @since   5.0.0
     */
    public function __construct(DispatcherInterface $dispatcher, array $config)
    {
        parent::__construct($dispatcher, $config);

    }

    /**
     * Standard routine method for the birthday reminder routine.
     *
     * @param   ExecuteTaskEvent  $event  The onExecuteTask event
     *
     * @return integer  The exit code
     *
     * @since 4.1.0
     * @throws \Exception
     */
    protected function remindBirthdays(ExecuteTaskEvent $event): int
    {
       // Load the parameters
       $states  = $event->getArgument('params')->reminder_for_status ?? '';
       $groups   = $event->getArgument('params')->reminder_to ?? '';

       // check for todays birthdays
		$birthdays = $this->getBirthdays($states);

		// no birthdays, abort
		if (!$birthdays) {
			return Status::OK;
		}

        $recipients = $this->getRecipients($groups);

        if (!$recipients) {
            return Status::KNOCKOUT;
        }

        // Get the email sender
		$Config  = Factory::getConfig();
		$mailFrom = $Config->get('mailfrom');
		$fromName = ComponentHelper::getParams('com_tkdclub')->get('club_name', 'TKD Club component');

		$nl = "\r\n";
		$email_subject = $fromName . ': ' . Text::_('PLG_TASK_BDREMINDER_EMAIL_SUBJECT');
		$email_body    = Text::_('PLG_TASK_BDREMINDER_EMAIL_BODY');

		$state_texts = array(
			'active' => 'PLG_TASK_BDREMINDER_STATE_ACTIVE',
			'inactive' => 'PLG_TASK_BDREMINDER_STATE_INACTIVE',
			'support' => 'PLG_TASK_BDREMINDER_STATE_SUPPORT'
		);

		foreach ($birthdays as $member) {
			$email_body .= $nl . $nl;
			$email_body .= $member->firstname . ' ' . $member->lastname . $nl;
			$email_body .= Text::_('PLG_TASK_BDREMINDER_AGE') . HelperTkdclubHelper::getAge($member->birthdate) . $nl;
			$email_body .= Text::_('PLG_TASK_BDREMINDER_EMAIL') . $member->email . $nl;
			$email_body .= Text::_('PLG_TASK_BDREMINDER_PHONE') . $member->phone . $nl;
			$email_body .= Text::_('PLG_TASK_BDREMINDER_STATE') . Text::_($state_texts[$member->member_state]) . $nl;
		}

		// Send the emails
		foreach ($recipients as $recipient) {
			$mailer = Factory::getMailer();
			$mailer->setSender(array($mailFrom, $fromName));
			$mailer->addCc($recipient);
			$mailer->setSubject($email_subject);
			$mailer->setBody($email_body);
			$mailer->send();
		}

        return Status::OK;
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
}