<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for edit view 'participant'
 */
class TkdClubModelParticipant extends JModelAdmin
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
    public function getTable($type = 'Participants', $prefix = 'TkdClubTable', $config = array())
    {   
        return JTable::getInstance($type, $prefix, $config);
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
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.participant.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Get all Ids and corresponding event-dates from the database
     * 
     * @return  array   array of fetched data
     */
    public function getIdsToDelete()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $days = JComponentHelper::getParams('com_tkdclub')->get('days', 365);

        // Select ids of table event_participants
        $query->select('a.id')
                ->from($db->quoteName('#__tkdclub_event_participants', 'a'));
        
        // Join over the event-table for the date difference
        $query->join('LEFT', $db->quoteName('#__tkdclub_events', 'b') . ' ON a.event_id = b.event_id');
        $query->where($db->quoteName('store_data') . '=0');
        $query->where('DATEDIFF(NOW(), b.date) >=' . $days);
        
        $db->setQuery($query);
        
        return $db->loadColumn();
    }
}