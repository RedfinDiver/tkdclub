<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

class MedalController extends FormController
{   
    /**
     * Cancel the form
     * 
     * When the cancel- button in the form ist clicked,
     * the user is redirected to the homepage.
     */
    public function cancel($key = null)
    {        
        // Clean the session state and go to homepage
        $context = "$this->option.edit.$this->context";
        $app = Factory::getApplication();
        $app->setUserState($context . '.data', null);

        $this->setRedirect(Route::_(Uri::base(), false));
    }

    /**
     * Save the form
     * 
     * Saving the entered form data to the medals-table
     * Based on the save() method in the FormController class
     * 
     * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
     */
   public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries
        $this->checkToken();

        $app = Factory::getApplication(); 
		$input = $app->input;
        $model = $this->getModel('medal');

        // Prepare redirect
        $returnId = $input->getInt('return');
        $menuItem = $app->getMenu()->getItem($returnId);
        $redirect = Route::_('index.php?Itemid=' . $menuItem->id);

        // Check that this user is allowed to add a new record
		if (!Factory::getUser()->authorise("core.create", "com_tkdclub"))
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return;
        }
        
        // get the data from the HTTP POST request
        $data  = $input->post->get('jform', array(), 'array');

        // We have to change the input data, because we using an array to
		// fill 3 fields in the database
		$winner_ids = $data['winner_ids'];
		
		$i = 1;
		foreach ($winner_ids as $id)
		{
			if ($id)
			{
				$data['winner_' . $i] = $id;
				$i++;
			}

			// Only allow up to 3 winner fields
			if ($i == 4) break;
		}

		// Check if the required fields are set
		!isset($data['winner_2']) ? $data['winner_2'] = 0 : "";
		!isset($data['winner_3']) ? $data['winner_3'] = 0 : "";

        // set up context for saving form data
        $context = "$this->option.edit.$this->context";
        
        // Validate the posted data.
		// First we need to set up an instance of the form
        $form = $model->getForm($data, false);
        
        if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
			return false;
        }
        
        $validData = $model->validate($form, $data);

        // Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof \Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the same screen.
			$this->setRedirect($redirect);

            return false;
        }

        // add the 'created by' and 'created' date fields
		$validData['created_by'] = Factory::getUser()->get('id', 0);
        $validData['created'] = date('Y-m-d h:i:s');
        
        // Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect($redirect);

			return false;
        }

        // send the info mail
        $this->sendInfoMail($validData);

        // Clear the data in the form, keep date, championship and type
        // Maybe the user wants to add more medals from the same tournament
        $validData['class'] = '';
        $validData['placing'] = '';
        $validData['winner_ids'] = '';
        $validData['notes'] = '';

        $app->setUserState($context . '.data', $validData);

        // redirect to the form
        $this->setRedirect($redirect, Text::_('COM_TKDCLUB_MEDAL_ADD_SUCCESS'));
        
        return true;
    }

    /**
     * Send the information mail to selected usergroups
     * 
     * @return  boolean  True if successful, false otherwise.
     */
    public function sendInfoMail($enteredData)
    {
        $params = ComponentHelper::getParams('com_tkdclub');
        if (!$params->get('medal_email'))
        {
            // no mail information specified in configuration
            return true;
        }

        $groups = $params->get('medal_info_to', array(8));
        $recipients = TkdclubHelper::getEmailfromUsergroups($groups);
        if (empty($recipients))
        {
            return false;
        }
        $current_user = Factory::getUser()->get('name');
        
        $subject = $params->get('club_name', 'TKD Club') . " - ". Text::_('COM_TKDCLUB_MEDAL_ADDED_MEDAL_FRONTEND') . $current_user;
        $nl = "\r\n";

        $body  = Text::_('COM_TKDCLUB_MEDAL_ADDED_MEDAL_FRONTEND_BODY') . $nl;
        $body .= Text::_('COM_TKDCLUB_DATE') . " : " . HTMLHelper::_('date', $enteredData['date'], Text::_('DATE_FORMAT_LC4')) . $nl;
        $body .= Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP') . " : " . $enteredData['championship'] . $nl;
        $body .= Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP_TYPE') . " : " . $enteredData['type'] . $nl;
        $body .= Text::_('COM_TKDCLUB_MEDAL_CLASS') . " : " . $enteredData['class'] . $nl;
        $body .= Text::_('COM_TKDCLUB_MEDAL_PLACING') . " : " . $enteredData['placing'] . $nl;
    
        $memberlist = TkdclubHelper::getMemberlist();
        $winners = TkdclubHelper::getMembersNames($enteredData['winner_ids'], $memberlist);

        $body .= Text::_('COM_TKDCLUB_ATHLETS') . " : " . $winners . $nl;
        $body .= Text::_('COM_TKDCLUB_NOTES') . " : " . $enteredData['notes']. $nl;
        
        $mailer = Factory::getMailer();
        $mailer->addRecipient($recipients);
        $mailer->addRecipient($recipients);
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->send();

        return true;
    }
    
}