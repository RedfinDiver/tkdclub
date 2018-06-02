<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Participants list view controller
 */
class TkdClubControllerParticipants extends JControllerAdmin
{
    protected $text_prefix = 'COM_TKDCLUB_PARTICIPANT';

    public function getModel($name = 'participant', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Toggle on/off the stats
     * 
     * With this method the statistics are switched on or off
     * in the participants list view
     * 
     * @return void
     */ 
    public function togglestats()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $session = JFactory::getSession();

        if (!$session->get('togglestats_participants', null, 'tkdclub'))
        {
            $session->set('togglestats_participants', 'ON', 'tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_ON';  
        }
        else
        {
            $session->clear('togglestats_participants','tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_OFF';  
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=participants', JText::_($msg));
    }

    /**
     * Delete participants for gdpr compliance
     * 
     * This method is triggered by the "gdpr" button in admin paricipants list view
     */
    public function delete_gdpr()
    {   
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel();

        // First get ids of participants and corresponding dates from the database
        $ids_to_delete = $model->getIdsToDelete();

        if (empty($ids_to_delete))
        {
            $this->setRedirect('index.php?option=com_tkdclub&view=participants');
            $this->setMessage('COM_TKDKLUB_PARTICIPANT_GDPR_NO_DATA', 'notice');

            return;
        }

        // Make sure the item ids are integers
        $cid = ArrayHelper::toInteger($ids_to_delete);

        // Remove the items.
        if ($model->delete($cid))
        {
            JFactory::getApplication()->enqueueMessage('COM_TKDKLUB_PARTICIPANT_GDPR_DATA_DELETED', 'notice');
            $this->setMessage(\JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
        }
        else
        {
            $this->setMessage($model->getError(), 'error');
        }

        // Invoke the postDelete method to allow for the child class to access the model.
        $this->postDeleteHook($model, $cid);

        $this->setRedirect('index.php?option=com_tkdclub&view=participants');
    }
}   