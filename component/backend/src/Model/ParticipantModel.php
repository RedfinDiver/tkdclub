<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Table\SubscribersTable;

/**
 * Model-class for edit view 'participant'
 */
class ParticipantModel extends AdminModel
{       
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   2.0
     * @throws  Exception
     */
    public function getTable($type = 'Participants', $prefix = 'Administrator', $config = array())
    {   
        return parent::getTable($type, $prefix, $config);
    }
        
    /**
     * Method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   2.0
     */
    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'participant',  $options);

        if (empty($form))
        {
            return false;
        }
        return $form;
    }

    /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   2.0
	 */
    protected function loadFormData()
    {
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.participant.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Get all Ids where data is to delete
     * 
     * @return  array   array of ids
     */
    public function getIdsToDelete()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $days = ComponentHelper::getParams('com_tkdclub')->get('days', 365);

        // Select ids of table event_participants
        $query->select('a.id')
                ->from($db->quoteName('#__tkdclub_event_participants', 'a'));
        
        // Join over the event-table for the date difference
        $query->join('LEFT', $db->quoteName('#__tkdclub_events', 'b') . ' ON a.event_id = b.event_id');
        $query->where('DATEDIFF(NOW(), b.date) >=' . $days);
        
        $db->setQuery($query);
        
        return $db->loadColumn();
    }

    /**
     * Get data from entrys where data is allowed to store for newsletters
     * 
     * @return  array   array of objects with fetched data
     */
    public function getDataToStore()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $days = ComponentHelper::getParams('com_tkdclub')->get('days', 365);

        // Select ids of table event_participants
        $query->select('a.firstname,a.lastname,a.email')
                ->from($db->quoteName('#__tkdclub_event_participants', 'a'));
        
        // Join over the event-table for the date difference
        $query->join('LEFT', $db->quoteName('#__tkdclub_events', 'b') . ' ON a.event_id = b.event_id');
        $query->where($db->quoteName('store_data') . '=1');
        $query->where('DATEDIFF(NOW(), b.date) >=' . $days);
        
        $db->setQuery($query);
        
        return $db->loadObjectList();
    }

    /**
     * Store allowed data in the newsletter database
     * 
     * @param   array   $data   array of objects with the data to store
     * 
     * @return  integer number of saved datasets
     */
    public function storeAllowedData($data)
    {
        $all_emails = $this->getAllEmails();
        $stored = 0;

        foreach ($data as $row)
        {   
            // Check if email is there and already present
            if ($row->email != '' && !in_array($row->email, $all_emails))
            {
                $table = $this->getMVCFactory()->createTable('Subscribers', 'Administrator');
                $row->origin = 2; // set origin from form in database
                $table->save($row) ? $stored++ : null;
            }
        }

        return $stored;
    }

    /**
     * Get all unique email adresses in the component
     * 
     * @return  array   array with all emails in it
     */
    public function getAllEmails()
    {
        // First all emails from members table
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select('DISTINCT ' . $db->quoteName('email'))
              ->from($db->quoteName('#__tkdclub_members'));
        $db->setQuery($query);
        $emails = $db->loadColumn();

        // Now all from the subscribers table
        $query = $db->getQuery(true);
        $query->select('DISTINCT ' . $db->quoteName('email'))
              ->from($db->quoteName('#__tkdclub_newsletter_subscribers'));
        $db->setQuery($query);
        $emails += $db->loadColumn();

       return array_unique($emails);
    }
}