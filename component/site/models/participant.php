<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class TkdClubModelParticipant extends FormModel
{
    public function getTable($type = 'Participants', $prefix = 'TkdClubTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'participant',  $options);
        
        // Removing not displayed fields from the form and labeling of userfields
        $params = Factory::getApplication()->getUserState('com_tkdclub.participant.itemparams');
        !$params->show_email ? $form->removeField('email') : null;
        !$params->show_age ? $form->removeField('age') && $form->removeField('age_dist') : null;
        !$params->show_grade ? $form->removeField('grade') && $form->removeField('grade_dist') : null;
        !$params->show_club ? $form->removeField('clubname') : null;
        !$params->show_kupgradeachieve ? $form->removeField('kupgradesachieve') : null;
        
        if (!$params->allow_multi)
        {
            $form->setFieldAttribute('group', 'hidden', 'true');
        }
        
        if ($params->show_user1)
        {
            $form->setFieldAttribute('user1', 'label', $params->user1);
            $form->setFieldAttribute('user1', 'description', $params->user1);
        }
        else
        {
            $form->removeField('user1');
        }
        
        if ($params->show_user2)
        {
            $form->setFieldAttribute('user2', 'label', $params->user2);
            $form->setFieldAttribute('user2', 'description', $params->user2);
        }
        else 
        {
            $form->removeField('user2');
        }
        
        if ($params->show_user3)
        {
            $form->setFieldAttribute('user3', 'label', $params->user3);
            $form->setFieldAttribute('user3', 'description', $params->user3);
        }
        else 
        {
            $form->removeField('user3');
        }
        
        if ($params->show_user4)
        {
            $form->setFieldAttribute('user4', 'label', $params->user4);
            $form->setFieldAttribute('user4', 'description', $params->user4);
        }
        else 
        {
            $form->removeField('user4');
        }
        
        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }

    protected function loadFormData()
    {
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.participant.data', array());

        return $data;
    }

    /**
     * Method to get data for an event
     * 
     * @param   integer  $event_id  The id of the event.
     *
     * @return  integer  Number of subscribed participants.
     *
     */
    public function getEventData()
    {   
        $event_id = Factory::getApplication()->getParams()->toObject()->event_id;

        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('title, date, deadline, event_id, min, max')
                ->from($db->quoteName('#__tkdclub_events'))
                ->where('event_id = '. intval($event_id));

        $db->setQuery($query);
        $event_data = $db->loadAssoc();
        $event_data['subscribed'] = $this->getSubscribedParticipants($event_id);

        return $event_data;
    }

    /**
     * Method to get number of subscribed participants for an event
     * 
     * @param   integer  $event_id  The id of the event.
     *
     * @return  integer  Number of subscribed participants.
     *
     */
    public function getSubscribedParticipants($event_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('sum('.$db->quoteName('registered').')')
                ->from($db->quoteName('#__tkdclub_event_participants'))
                ->where('event_id = '.$event_id);

        $db->setQuery($query);
        $db->execute();

        return $db->loadResult();
    }

    /**
     * Send the emails to organizers and confirmation email to participant
     * 
     * @param   array   $event_data     data for the event
     * @param   object  $data           form data
     * @param   array   $groups         array of group numbers to send notification email to
     * 
     * @return  void
     * 
     */
    public function send($event_data, $data, $groups)
	{   
        $mail = new stdClass;

        // Prepare the data for the email
        $mail->title      = $event_data['title'];
        $mail->date       = HTMLHelper::_('date', $event_data['date'], Text::_('DATE_FORMAT_LC4'));
        $mail->fields     = $this->prepareDataforEmail($data);
        $mail->subscribed = $this->getSubscribedParticipants($event_data['event_id']);
        $mail->free       = $event_data['max'] - $mail->subscribed;
        $mail->name       = $data['firstname'] . ' ' . $data['lastname'];
        
        $this->sendConformationMail($mail, $data['email']);                 
        
        $this->sendUsergroupMail($mail, $this->getOrganizerEmails($groups));
    }

    /**
     * Prepare the data from fields for the email
     * 
     * @param   array   $data   the preprocessed data from the form
     * 
     * @return  string  the data as string, ready to use in email body
     * 
     */
    public function prepareDataforEmail($data = array())
    {   
        $params = Factory::getApplication()->getUserState('com_tkdclub.participant.itemparams', '');
        $store_data = $data['store_data'] === 1 ? $store_data = Text::_('JYES') : $store_data = Text::_('JNO');
        $field_text = '';

        $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_FIRSTNAME') . ': ' . $data['firstname'] . "\r\n";
        $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_LASTNAME') . ': ' . $data['lastname'] . "\r\n";
        $data['email'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_EMAIL') . ': ' . $data['email'] . "\r\n" : null;
        $data['clubname'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_CLUB') . ': ' . $data['clubname'] . "\r\n" : null;
        $data['grade'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_GRADE_EMAIL') . ': ' . $data['grade'] . "\r\n" : null;
        $data['age'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_AGE') . ': ' . $data['age'] . "\r\n" : null;
        $data['registered'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_SUM') . ': ' . $data['registered'] . "\r\n" : null;
        $data['user1'] ? $field_text .= $params->user1 . ': ' . $data['user1'] . "\r\n" : null;
        $data['user2'] ? $field_text .= $params->user2 . ': ' . $data['user2'] . "\r\n" : null;
        $data['user3'] ? $field_text .= $params->user3 . ': ' . $data['user3'] . "\r\n" : null;
        $data['user4'] ? $field_text .= $params->user4 . ': ' . $data['user4'] . "\r\n" : null;
        $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_PRIVACY_ACCEPTED_EMAIL') . ': ' . Text::_('JYES') . "\r\n";
        $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_STOREDATA_ACCEPTED_EMAIL') . ': ' . $store_data . "\r\n";
        $data['notes'] ? $field_text .= Text::_('COM_TKDCLUB_PARTICIPANT_NOTES') . ': ' . $data['notes'] . "\r\n" : null;
        $field_text .= "\r\n";

        return $field_text;
    }      

    /**
     * Get all the email adresses for the given user group
     * 
     * @param   array   $groups   the group ids
     * 
     * @return  array   indexed array with email adresses for the group
     * 
     */
    public function getOrganizerEmails($groups)
    {   
        // If no group is given return
        if (empty($groups))
        {
            return false;
        }

        $emails = array();

        foreach ($groups as $group)
        {
            // Get all email adesses for the users
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                    ->select($db->qn('a.email'))
                    ->from($db->qn('#__users', 'a'))
                    ->join('LEFT', $db->qn('#__user_usergroup_map', 'b') . ' ON a.id = b.user_id')
		            ->where($db->qn('group_id') . ' = ' . (int) $group);
        
            $emails = array_merge($emails, $db->setQuery($query)->loadColumn());
        }

	    return array_unique($emails);
    }

    /**
     * Send a confirmation email to the subscriber
     * 
     * @param   object  $mail   The data to form the email
     * @param   mixed   $recipient  string or array of strings
     * 
     * @return  bool    true if succeded, false otherwise
     */ 
    public function sendConformationMail($mail, $recipient)
    {   
        $subject = Text::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS_CONFIRMATION')
                   . "\"" . $mail->title . "\""
                   . Text::_('COM_TKDCLUB_EVENT_ON')
                   . $mail->date;

        $message = Text::_('COM_TKDCLUB_HELLO') . ' ' . $mail->name . ','
                   . "\r\n\r\n"
                   . Text::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_CONFIRMATION')
                   . "\r\n\r\n"
                   . Text::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_DATA_WHERE')
                   . "\r\n"
                   . $mail->fields
                   . Text::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_THANK_YOU');
        
        return $this->mail($subject, $message, $recipient);           
    }

    /**
     * Send an email to the users in the selected user group
     * 
     * @param   object  $mail   The data to form the email
     * @param   mixed   $recipient  string or array of strings
     * 
     * @return  bool    true if succeded, false otherwise
     */ 
    public function sendUsergroupMail($mail, $recipients)
    {   
        $subject = Text::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS')
                   . "\"" . $mail->title . "\""
                   . Text::_('COM_TKDCLUB_EVENT_ON')
                   . $mail->date . " "
                   . Text::_('COM_TKDCLUB_PARTICIPANT_INCOME');

        $message = Text::_('COM_TKDCLUB_HELLO') . ","
                   . "\r\n\r\n"
                   . Text::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_ADMIN')
                   . "\r\n\r\n"
                   . Text::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_DATA_WHERE')
                   . "\r\n"
                   . $mail->fields
                   . Text::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS')
                   . $mail->subscribed
                   . "\r\n"
                   . Text::_('COM_TKDCLUB_EVENT_PLACES_FREE')
                   . $mail->free;
        
        return $this->mail($subject, $message, $recipients);           
    }

    /**
     * Mail the E-Mail with JMailer
     * 
     * @param   string  $subject    the subject string
     * @param   string  $message    the message sring
     * @param   mixed   $recipients string or array of strings
     */
    public function mail($subject, $message, $recipients)
    {
        $app = Factory::getApplication();
		$mailer = Factory::getMailer();

		// Build email message format.
		$mailer->setSender(array($app->get('mailfrom'), $app->get('fromname')));
		$mailer->setSubject(stripslashes($subject));
		$mailer->setBody($message);
		$mailer->IsHtml(false);
        $mailer->addRecipient($recipients);

		// Send the Mail
		$rs = $mailer->Send();

		// Check for an error
		if ($rs instanceof Exception)
		{
			$this->setError($rs->getError());

			return false;
		}
		elseif (empty($rs))
		{
			
			$this->setError(Text::_('COM_USERS_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));

			return false;
        }

        return true;
    }
}