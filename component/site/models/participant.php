<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class TkdClubModelParticipant extends JModelForm
{
    public function getTable($type = 'Participants', $prefix = 'TkdClubTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'participant',  $options);
        
        // Removing not displayed fields from the form and labeling of userfields
        $params = JFactory::getApplication()->getUserState('com_tkdclub.participant.itemparams');
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
        $app =  JFactory::getApplication();
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
        $event_id = JFactory::getApplication()->getParams()->toObject()->event_id;

        $db = JFactory::getDbo();
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
        $db = JFactory::getDbo();
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
     * @param   integer $group          number of group to send notification email
     * 
     * @return  void
     * 
     */
    public function send($event_data, $data, $group)
	{   
        $mail = new stdClass;

        // Prepare the data for the email
        $mail->title      = $event_data['title'];
        $mail->date       = JHtml::_('date', $event_data['date'], JText::_('DATE_FORMAT_LC4'));
        $mail->fields     = $this->prepareDataforEmail($data);
        $mail->subscribed = $this->getSubscribedParticipants($event_data['event_id']);
        $mail->free       = $event_data['max'] - $mail->subscribed;
        $mail->name       = $data['firstname'] . ' ' . $data['lastname'];
        
        $this->sendConformationMail($mail, $data['email']);                 
        
        $this->sendUsergroupMail($mail, $this->getOrganizerEmails($group));
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
        $params = JFactory::getApplication()->getUserState('com_tkdclub.participant.itemparams', '');
        $store_data = $data['store_data'] === 1 ? $store_data = JText::_('JYES') : $store_data = JText::_('JNO');
        $field_text = '';

        $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_FIRSTNAME') . ': ' . $data['firstname'] . "\r\n";
        $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_LASTNAME') . ': ' . $data['lastname'] . "\r\n";
        $data['email'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_EMAIL') . ': ' . $data['email'] . "\r\n" : null;
        $data['clubname'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_CLUB') . ': ' . $data['clubname'] . "\r\n" : null;
        $data['grade'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_GRADE_EMAIL') . ': ' . $data['grade'] . "\r\n" : null;
        $data['age'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_AGE') . ': ' . $data['age'] . "\r\n" : null;
        $data['registered'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_SUM') . ': ' . $data['registered'] . "\r\n" : null;
        $data['user1'] ? $field_text .= $params->user1 . ': ' . $data['user1'] . "\r\n" : null;
        $data['user2'] ? $field_text .= $params->user2 . ': ' . $data['user2'] . "\r\n" : null;
        $data['user3'] ? $field_text .= $params->user3 . ': ' . $data['user3'] . "\r\n" : null;
        $data['user4'] ? $field_text .= $params->user4 . ': ' . $data['user4'] . "\r\n" : null;
        $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_PRIVACY_ACCEPTED_EMAIL') . ': ' . JText::_('JYES') . "\r\n";
        $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_STOREDATA_ACCEPTED_EMAIL') . ': ' . $store_data . "\r\n";
        $data['notes'] ? $field_text .= JText::_('COM_TKDCLUB_PARTICIPANT_NOTES') . ': ' . $data['notes'] . "\r\n" : null;
        $field_text .= "\r\n";

        return $field_text;
    }      

    /**
     * Get all the email adresses for the given user group
     * 
     * @param   integer   $group   the group id
     * 
     * @return  array   indexed array with email adresses for the group
     * 
     */
    public function getOrganizerEmails($group)
    {   
        // If no group is given return
        if ($group === 0)
        {
            return false;
        }

        // Get users in the group out of the ACL
        $access = new JAccess;
		$users = $access->getUsersByGroup($group);

        // Get all email adesses for the user
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select('email')->from('#__users')
		            ->where('id IN (' . implode(',', $users) . ')');

        $db->setQuery($query);
        
		return $db->loadColumn();
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
        $subject = JText::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS_CONFIRMATION')
                   . "\"" . $mail->title . "\""
                   . JText::_('COM_TKDCLUB_EVENT_ON')
                   . $mail->date;

        $message = JText::_('COM_TKDCLUB_HELLO') . ' ' . $mail->name . ','
                   . "\r\n\r\n"
                   . JText::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_CONFIRMATION')
                   . "\r\n\r\n"
                   . JText::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_DATA_WHERE')
                   . "\r\n"
                   . $mail->fields
                   . JText::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_THANK_YOU');
        
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
    public function sendUsergroupMail($mail, $recipient)
    {   
        $subject = JText::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS')
                   . "\"" . $mail->title . "\""
                   . JText::_('COM_TKDCLUB_EVENT_ON')
                   . $mail->date . " "
                   . JText::_('COM_TKDCLUB_PARTICIPANT_INCOME');

        $message = JText::_('COM_TKDCLUB_HELLO') . ","
                   . "\r\n\r\n"
                   . JText::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_ADMIN')
                   . "\r\n\r\n"
                   . JText::_('COM_TKDCLUB_PARTICIPANT_MESSAGE_DATA_WHERE')
                   . "\r\n"
                   . $mail->fields
                   . JText::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS')
                   . $mail->subscribed
                   . "\r\n"
                   . JText::_('COM_TKDCLUB_EVENT_PLACES_FREE')
                   . $mail->free;
        
        return $this->mail($subject, $message, $recipient);           
    }

    /**
     * Mail the E-Mail with JMailer
     * 
     * @param   string  $subject    the subject string
     * @param   string  $message    the message sring
     * @param   mixed   $recipient  string or array of strings
     */
    public function mail($subject, $message, $recipient)
    {
        $app = JFactory::getApplication();
		$mailer = JFactory::getMailer();

		// Build email message format.
		$mailer->setSender(array($app->get('mailfrom'), $app->get('fromname')));
		$mailer->setSubject(stripslashes($subject));
		$mailer->setBody($message);
		$mailer->IsHtml(false);
        $mailer->addRecipient($recipient);

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
			
			$this->setError(JText::_('COM_USERS_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));

			return false;
        }

        return true;
    }

    public function setAttributes($names, $atts, $values)
    {
        $form->setFieldAttribute();
    }
}