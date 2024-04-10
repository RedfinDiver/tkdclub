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
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

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
        $item_id = $this->input->getInt('Itemid', 0);
        $event_id = $this->input->getInt('event_id', 0);
        
        // Get the data from POST
        // Get the data from POST
        $data = $this->input->post->get('jform', [], 'array');
        
        // Get the event data from the event_id
        $event_id = $this->input->getInt('event_id');
        $event = $model->getEvent($event_id);
        
        // Get the current uri for redirect
        $currentUri = Uri::getInstance();

        // Get the menu-parameters for the current menu item
        if (!$item_id)
        {
            $params = $app->getMenu()->getActive()->getParams();
        }
        else
        {
            $params = $app->getMenu()->getItem($item_id)->getParams();
        }
        
        
        // Get the form
        $form = $model->getForm();

        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }

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
            $item_id = $this->input->getInt('Itemid');
        }
        
        $this->setRedirect($currentUri);
        
        return true;
    }

    public function reloadform()
    {
        // Check for request forgeries.
        Session::checkToken();
        $app = Factory::getApplication();      
        $event_id = $app->input->getInt('event_id');
        $item_id = $app->input->getInt('item_id');
        $todo = $app->input->getString('todo');

        if ($todo = 'reset')
        {
            // empty session data
            $app->setUserState('com_tkdclub.participant.data', '');
            $app->enqueueMessage( Text::_('COM_TKDCLUB_FORM_RESET'), 'message');
        }

        // Redirect back to the contact form.
        $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=participant&event_id='. $event_id . '&Itemid=' . $item_id));
        

        return;
        
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

    /** Create the html markup for single/multiple subscriptions
     * 
     * @return  json   html markup
     * 
     */
    public function reloadfields()
    {
        try
        {
            $app = Factory::getApplication();
            $model = $this->getModel($name = 'participant', $prefix = 'Site', $config = array());
            $form = $model->getForm($data = array(), $loadData = true);
            $html = '';

            foreach($form->getFieldset('participant_data') as $field)
            {
                $html .= !$field->hidden ? $form->renderField($field->fieldname) : null;
            }

            $result = array('response' => $html);
            
            //@TODO Make JsonResponse work
            //echo new JsonResponse($result);

            echo $html;
            $app->close();
            
        }
        catch(\Exception $e)
        {
            echo new JsonResponse($e);
        }
    }
}