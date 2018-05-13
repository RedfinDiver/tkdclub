<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
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
    
    public function send($event_data, $data, $grp)
	{       
        require_once  JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php';
        $app = JFactory::getApplication();
        $access = new JAccess;
        $db     = $this->getDbo();
        $parts  = TkdClubHelper::getParticipants($event_data['id']);
        $params = JFactory::getApplication()->getUserState('com_tkdclub.participant.itemparams', '');

		$title = $event_data['title'];
		$subject = JText::_('COM_TKDCLUB_SUBSCRIPTION') . $title 
                               . JText::_('COM_TKDCLUB_SUBSCRIPTION_AT') . JHtml::_('date', $event_data['date'], JText::_('DATE_FORMAT_LC4'))
                               . JText::_('COM_TKDCLUB_SUBSCRIPTION_NEW');
                
        // Preparing the Fields for email-message
        $params->show_grade  ? $field_grade =  JText::_('COM_TKDCLUB_EVENTPARTS_GRADE') . ': ' .  $data['grade']    .  "\r\n" : $field_grade = '';
        $params->show_age    ? $field_age   =  JText::_('COM_TKDCLUB_EVENTPARTS_AGE')   . ': ' .  $data['age']      .  "\r\n" : $field_age   = '';
        $params->show_club   ? $field_club  =  JText::_('COM_TKDCLUB_EVENTPARTS_CLUB')  . ': ' .  $data['clubname'] .  "\r\n" : $field_club  = '';
        $params->show_email  ? $field_email =  JText::_('COM_TKDCLUB_EVENTPARTS_EMAIL') . ': ' .  $data['email']    .  "\r\n" : $field_email = '';
        $data['notes'] != '' ? $field_notes =  JText::_('COM_TKDCLUB_EVENTPARTS_NOTES') . ': ' .  $data['notes']    .  "\r\n" : $field_notes = '';
        $params->show_user1  ? $field_user1 = $params->user1 . ': ' . $data['user1'] . "\r\n" : $field_user1 = '';
        $params->show_user2  ? $field_user2 = $params->user2 . ': ' . $data['user2'] . "\r\n" : $field_user2 = '';
        $params->show_user3  ? $field_user3 = $params->user3 . ': ' . $data['user3'] . "\r\n" : $field_user3 = '';
        $params->show_user4  ? $field_user4 = $params->user4 . ': ' . $data['user4'] . "\r\n" : $field_user4 = '';
                
        // Preparing email-message
		$message_body = $data['firstname'] . ' ' . $data['lastname'] . JText::_('COM_TKDCLUB_EVENT_HAS_SUBSCRIBED') . "\r\n\r\n"
                                . $field_grade
                                . $field_age
                                . $field_club
                                . $field_email
                                . $field_notes
                                . $field_user1 . $field_user2 . $field_user3 . $field_user4 
                                . "\r\n"
                                . JText::_('COM_TKDCLUB_EVENT_SUBSCRIBED') . $parts . "\r\n"
                                . JText::_('COM_TKDCLUB_EVENT_PLACES_FREE'). ($event_data['max_parts'] - $parts);
                                
		// Get users in the group out of the ACL
		$to = $access->getUsersByGroup($grp);

		// Get all users email and group except for senders
		$query = $db->getQuery(true)
			->select('email')
			->from('#__users');

		if ($grp !== 0)
		{
			if (empty($to))
			{
				$query->where('0');
			}
			else
			{
				$query->where('id IN (' . implode(',', $to) . ')');
			}
		}

		$db->setQuery($query);
		$rows = $db->loadColumn();

		// Get the Mailer
		$mailer = JFactory::getMailer();

		// Build email message format.
		$mailer->setSender(array($app->get('mailfrom'), $app->get('fromname')));
		$mailer->setSubject(stripslashes($subject));
		$mailer->setBody($message_body);
		$mailer->IsHtml(false);
        $mailer->addRecipient($rows);

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
		
	}
        
    public function setAttributes($names, $atts, $values)
    {
        $form->setFieldAttribute();
    }
}