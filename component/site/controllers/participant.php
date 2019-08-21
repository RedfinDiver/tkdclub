<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class TkdClubControllerParticipant extends JControllerForm
{
    
    public function getTable($type = 'Participants', $prefix = 'TkdClubTable', $config = array())    
    {
        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Subscription to an event
     * 
     * This method is triggered by the "subscribe" - Button in the
     * participant site view
     * 
     */
    public function subscribe()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));       

        // Getting some variables
        $app = JFactory::getApplication();
        $model = $this->getModel('participant');

        $this->menu_params = JFactory::getApplication()->getUserState('com_tkdclub.participant.itemparams');
        $test = JComponentHelper::getParams('com_tkdclub')->get('captcha');
        $this->item_id = $app->getMenu()->getActive()->id;
        $event_data = $model->getEventData();
        
        // Get the input and preprocess it
        $this->data = $this->preprocessData($app->input);

        // Captcha Check
        if (!$this->checkCaptcha())
        {
            return false;
        }
        
        // Everything is fine, saving to the database
        try
        {
            $table = $this->getTable();
            
            if ($table->save($this->data))
            {
                $this->message = JText::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS') 
                . '"' .$event_data['title'] . '" ' .  JText::_('COM_TKDCLUB_AT') . ' '
                . JHtml::_('date', $event_data['date'], JText::_('DATE_FORMAT_LC4')) 
                . JText::_('COM_TKDCLUB_SUBSCRIBE_THANK_YOU');

                if ($this->data['email'])
                {
                    $this->message .= JText::_('COM_TKDCLUB_EMAIL_CONFIRMATION');
                }
        
                // Send the mail to organizer
                if ($this->menu_params->send_email)
                {
                    $model->send($event_data, $this->data, $this->menu_params->email_user_group);   
                }
                
                // Deleting the entered data, except value for clubname  
                foreach ($this->data as $key => &$value)
                {
                    if ($key != "clubname" )
                    {
                        $value = '';
                    }      
                }
            
                // Setting the cleaned state after successful saving   
                $app->setUserState('com_tkdclub.participant.data', $this->data);  
          
            }
            else
            {
                $this->message = JText::_($table->getError());
            }
            
        } 
        catch (Exception $ex)
        {
            $this->message = $ex->getMessage();
        }
        
        $this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=participant&Itemid='. $this->item_id, false));
        
        return true;
    }

    /** Preprocess the data before saving to the database
     * 
     * The data is cleaned and the data for age and grade is preprocessed
     * 
     * @param   object  $input  Joomla input object
     * 
     * @return  array   Preprocessed data, ready to save
     * 
     */
    public function preprocessData($input)
    {
        $uncleaned_data = $input->getArray(array(
            'jform' => array(
                'group' => 'int',
                'lastname' => 'string',
                'firstname' => 'string',
                'age' => 'string',
                'age_dist' => 'string',
                'clubname' => 'string',
                'registered' => 'int',
                'grade' => 'string',
                'grade_dist' => 'string',
                'kupgradesachieve' => 'string',
                'email' => 'string',
                'notes' => 'string',
                'user1' => 'string',
                'user2' => 'string',
                'user3' => 'string',
                'user4' => 'string',
                'store_data' => 'int',
                'privacy_agreed' => 'int',
            )
        ));

        $data = array();
     
        foreach ($uncleaned_data['jform'] as $key => $value)
        {
            $data[$key] = $value;
        }
    
        // Appending event_id and publishing        
        $data['event_id'] = $this->menu_params->event_id;
        $data['published'] = (int) 1;

        if ($data['group'] == 0)
        {
            $data['registered'] = 1;
        }

        // Set the age data for the table
        $data['age'] = $data['age_dist'] ? $data['age'] = $data['age_dist'] : $data['age'] = $data['age'];

        // Set the grade-data for the table
        if ($data['grade'])
        {
            $data['grade'] = $data['grade'];
        }
        elseif ($data['grade_dist'])
        {
            $data['grade'] = $data['grade_dist'];
        }
        elseif ($data['kupgradesachieve'])
        {
            $data['grade'] = $data['kupgradesachieve'];
        }

        return $data;
    }
    
    protected function checkCaptcha()
    {   
        $app = JFactory::getApplication();

        // No captcha in configuration demanded
        if (JComponentHelper::getParams('com_tkdclub')->get('captcha') == '0')
        {
            return true;
        }

        // Get the plugin
        JPluginHelper::importPlugin('captcha');
        $dispatcher = JEventDispatcher::getInstance();
        
        // Get the response
        $response = $dispatcher->trigger('onCheckAnswer', '');
        //$response = array(false);

        // If empty, return false
        if($response[0] === false)
        {   
            // set the fail message
            $this->setMessage(JText::_('COM_TKDCLUB_PARTICIPANT_CAPTCHA_ERROR'), 'error');

            // save form data in session
            $app->setUserState('com_tkdclub.participant.data', $this->data);

            // Redirect back to the contact form.
            $this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=participant&Itemid='. $this->item_id, false));
            
            return false;
        }
        
        return true;
    }
}