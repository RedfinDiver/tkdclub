<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

class ParticipantController extends FormController
{
    /**
     * Subscription to an event
     * 
     * This method is triggered by the "subscribe" - Button in the
     * participant site view
     * 
     */
    public function subscribe()
    {
        // Check for request forgeries.
        Session::checkToken();       

        $app   = Factory::getApplication();
        $model = $this->getModel('participant');
        $table = $model->getTable();
        $item_id = $this->input->getInt('Itemid');
        
        // Get the data from POST
        $data = $this->input->post->get('jform', [], 'array');
        
        // Get the event data from the event_id
        $event_id = $this->input->getInt('event_id');
        $event = $model->getEvent($event_id);
        
        // Get the current uri for redirect
        $currentUri = Uri::getInstance();

        // Get the menu-parameters for the current menu item
        $params = $app->getMenu()->getActive()->getParams();
        
        // Get the form
        $form = $model->getForm();

        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }

        // Prepare the form before data validation
        $form = $this->prepareForm($form, $params);

        if (!$model->validate($form, $data)) {
            $errors = $model->getErrors();

            foreach ($errors as $error) {
                $errorMessage = $error;

                if ($error instanceof \Exception) {
                    $errorMessage = $error->getMessage();
                }

                $app->enqueueMessage($errorMessage, 'error');
            }

            // save form data in session
            $app->setUserState('com_tkdclub.participant.data', $data);

            // Redirect back to the contact form.
            $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=participant&Itemid='. $item_id, false));

            return false;
        }

        // Preprocess the data
        $data = $this->preprocessData($data);

        // Everything is fine, saving to the database
        try
        {            
            if ($table->save($data))
            {
                $this->message = Text::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS') 
                . '"' .$event['title'] . '" ' .  Text::_('COM_TKDCLUB_AT') . ' '
                . HTMLHelper::_('date', $event['date'], Text::_('DATE_FORMAT_LC4')) 
                . Text::_('COM_TKDCLUB_SUBSCRIBE_THANK_YOU');

                if ($data['email'])
                {
                    $this->message .= Text::_('COM_TKDCLUB_EMAIL_CONFIRMATION');
                }
        
                // Send email to organizer and participant
                if ($params->get('send_email'))
                {
                    $model->send($event, $data, $params);   
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
                $this->message = Text::_($table->getError());
            }
            
        } 
        catch (\Exception $ex)
        {
            $this->message = $ex->getMessage();
        }
        
        $this->setRedirect($currentUri);
        
        return true;
    }

    /** Prepare the form
     * 
     * Not necassary fields and groups are removed from the form object
     * 
     * @param   object  $form  Joomla form object
     * 
     * @param   object  $params  Joomla registry object with menu item parameters
     * 
     * @return  object  prepared form, ready to validate
     * 
     */
    public function prepareForm(&$form, $params)
    {
        // Change the form according to menu-parameters        
        // Checking for single or multiple subscription
        if ($params->get('allow_multi') == '0') {
            $form->removeField('registered');
            $form->removeField('age_dist');
            $form->removeField('grade_dist');
        } elseif ($params->get('allow_multi') == '1') {
            $form->removeField('age');
            $form->removeField('grade');
            $form->removeField('kupgradesachieve');
        };

        // Checking for age fields
        if ($params->get('show_age') == '0') {
            $form->removeField('age');
            $form->removeField('age_dist');
        }

        // Checking for grade fields (when not removed previously, doesn't harm)
        if ($params->get('show_grade') == '0') {
            $form->removeField('grade');
            $form->removeField('grade_dist');
        }

        // Checking for the other fields
        $params->get('show_club')  == '0' ? $form->removeField('clubname') : null;
        $params->get('show_email') == '0' ? $form->removeField('email') : null;
        $params->get('show_user1') == '0' ? $form->removeField('user1') : null;
        $params->get('show_user2') == '0' ? $form->removeField('user2') : null;
        $params->get('show_user3') == '0' ? $form->removeField('user3') : null;
        $params->get('show_user4') == '0' ? $form->removeField('user4') : null;
        $params->get('show_kupgradeachieve') == '0' ? $form->removeField('kupgradesachieve') : null;

        // The 'group' field is never necessary in that case
        $form->removeField('group');

        return $form;
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
    public function preprocessData(&$data)
    {     
        // Appending event_id and publishing        
        $data['event_id'] = $this->input->getInt('event_id');
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
}