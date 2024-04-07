<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * Participants list view controller
 */
class ParticipantsController extends AdminController
{
    protected $text_prefix = 'COM_TKDCLUB_PARTICIPANT';

    public function getModel($name = 'participant', $prefix = 'Administrator', $config = array())
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
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $session = Factory::getSession();

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
        
        $this->setRedirect('index.php?option=com_tkdclub&view=participants', Text::_($msg));
    }

    /**
     * Delete participants for gdpr compliance
     * 
     * This method is triggered by the "gdpr" button in admin participants list view
     */
    public function delete_gdpr()
    {   
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        $model = $this->getModel();

        // First lets have a look if there is somthing to delete
        if(!$ids_to_delete = $model->getIdsToDelete())
        {
            $this->setMessage(Text::_('COM_TKDKLUB_PARTICIPANT_GDPR_NO_DATA'), 'notice');
            $this->setRedirect('index.php?option=com_tkdclub&view=participants');
            
            return;
        }
        
        // Now look for data that is allowed to store
        if (!$data_to_store = $model->getDataToStore())
        {   
            // nothing to store, just remove the data from the database
            $this->removeData($ids_to_delete);
            
            return;
        }
        
        // Well, we have some data to store, let' s do it
        $stored = $model->storeAllowedData($data_to_store);
        Factory::getApplication()->enqueueMessage(Text::plural($this->text_prefix . '_N_ITEMS_STORED', $stored), 'notice');

        // And now finally remove the data
        $this->removeData($ids_to_delete);

    }

    /**
     * Remove data sets from the database
     * 
     * @param   array   $ids    array with ids to delete
     * 
     * @return  void
     */
    public function removeData($ids)
    {
        // Make sure the item ids are integers
        $cid = ArrayHelper::toInteger($ids);
        $model = $this->getModel();

        // Remove the items.
        if ($model->delete($cid))
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_TKDKLUB_PARTICIPANT_GDPR_DATA_DELETED', 'notice'));
            $this->setMessage(Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
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