<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
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
    *   JS method for inserting multiple field markup
    */
    public function getMultipleFieldset()
    {
        $app = JFactory::getApplication();
        $html = '';
        $toRender = $app->input->get('selection', '', 'int');
        $model = $this->getModel($name = 'participant', $prefix = 'TkdClubModel', $config = array());
        $form = $model->getForm($data = array(), $loadData = true);
        
        if ($toRender == 1)
        {
            foreach($form->getFieldset('participant_data_multiple') as $field)
            {
                $html .= !$field->hidden ? $form->renderField($field->fieldname) : null;
            }
        }
        elseif ($toRender == 0)
        {
            foreach($form->getFieldset('participant_data_single') as $field)
            {
                $html .= !$field->hidden ? $form->renderField($field->fieldname) : null;
            }
        }

        $result = json_encode(array('response' => $html));

        echo $result;
        $app->close();
    }
    
    public function subscribe()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));       

        // Getting the application
        $app = JFactory::getApplication();
        
        // Get the menu item-id an the event-data via JMenuSite from the params of the menu item
        $item_id = $app->getMenu()->getActive()->id;
        $event_id = $app->getMenu()->getActive()->params->get('event_id');
        $grp = $app->getMenu()->getActive()->params->get('email_user');
        $send_mail = $app->getMenu()->getActive()->params->get('send_email');
        $menu_params = $app->getMenu()->getActive()->params;
        
        $model = $this->getModel('participant');
        $event_data = $model->getEventData();

        // Get the params for the component
        $params = JComponentHelper::getParams('com_tkdclub');
        
        // Get Form-data from the JInput Object and cleaning and append event_id and publishing 
        $input = $app->input;
        $uncleaned_data = $input->getArray(array(
                'jform' => array(
                    'group' => 'int',
                    'lastname' => 'word',
                    'firstname' => 'word',
                    'age' => 'string',
                    'age_dist' => 'string',
                    'clubname' => 'string',
                    'participants' => 'int',
                    'grade' => 'string',
                    'grade_dist' => 'string',
                    'kupgradesachieve' => 'string',
                    'email' => 'string',
                    'notes' => 'string',
                    'user1' => 'string',
                    'user2' => 'string',
                    'user3' => 'string',
                    'user4' => 'string',
                )
            ));

        $data = array();
         
        foreach ($uncleaned_data['jform'] as $key => $value)
        {
            $data[$key] = $value;
        }
        
        // Appending event_id and publishing        
        $data['event_id'] = (int) $event_id;
        $data['published'] = (int) 1;

        if ($data['group'] == 0)
        {
            $data['participants'] = 1;
        }

        // Get the age data for the table
        $data['age'] = $data['age_dist'] ? $data['age'] = $data['age_dist'] : $data['age'] = $data['age'];

        // get the grade-data for the table
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

        // Captcha Check
        if (!$this->checkCaptcha() && $params->get('captcha') != '0' )
        {
            // set the fail message
            $this->setMessage(JEventDispatcher::getInstance()->getError());
            // save form data in session
            $app->setUserState('com_tkdclub.participant.data', $data);
            // Redirect back to the contact form.
            $this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=participant&Itemid='. $item_id, false));
            return false;
        }
        
        // Everything is fine, saving to the database
        try
        {
            $table = $this->getTable();
            
            if ($table->save($data))
            {
                $this->message = JText::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS') 
                . '"' .$event_data['title'] . '" am ' 
                . JHtml::_('date', $event_data['date'], JText::_('DATE_FORMAT_LC4')) 
                . JText::_('COM_TKDCLUB_SUBSCRIBE_THANK_YOU');
        
                // Send the mail
                if ($send_mail)
                {
                    $model->send($event_data, $data, $grp);   
                }
                
                // Deleting the entered data, except value for clubname  
                foreach ($data as $key => &$value)
                {
                    if ($key != "clubname" )
                    {
                        $value = '';
                    }      
                }
            
                // Setting the cleaned state after successful saving   
                $app->setUserState('com_tkdclub.participant.data', $data);  
          
                
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
        
        $this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=participant&Itemid='. $item_id, false));
        
        return true;
    }
    
    
    protected function checkCaptcha()
    {
        // Get the plugin
        JPluginHelper::importPlugin('captcha');
        $dispatcher = JEventDispatcher::getInstance();
        
        // Get the response
        $res = $dispatcher->trigger('onCheckAnswer', '');

        // If empty, return false
        if(!$res[0])
        {
            return false;
        }
        
        return true;
    }
}