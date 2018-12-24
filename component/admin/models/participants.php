<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Model-class for list view 'participants'
 */
class TkdClubModelParticipants extends JModelList
{       
    /**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   2.0
	 */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                    'b.date', 'b.title',
                    'a.clubname', 'a.firstname',
                    'a.lastname', 'a.event_id',
                    'event_id', 'published', 'a.published'
                );
        }
        
        parent::__construct($config);
    }
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
    protected function populateState($ordering = 'b.date', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $event_id = $this->getUserStateFromRequest($this->context.'.filter.event_id', 'filter_event_id');
        $this->setState('filter.event_id', $event_id);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
        $this->setState('filter.published', $published);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_tkdclub');
        $this->setState('params', $params);

        parent::populateState($ordering, $direction);
    }

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.0
	 */
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.event_id');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }
        
    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   2.0
	 */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        //Select all fields of table event_participants
        $query->select('a.*')
                ->from($db->quoteName('#__tkdclub_event_participants', 'a'));

        // Join over the fields from the event-table
        $query->select('b.date,b.title');
        $query->join('LEFT', $db->quoteName('#__tkdclub_events', 'b') . ' ON a.event_id = b.event_id');

        // Search-box filter
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
                $search = $db->quote('%'. $db->escape($search, true).'%');
                $query->where('a.id LIKE' .$search
                            .' OR a.firstname LIKE' .$search
                            .' OR a.lastname LIKE' .$search
                            .' OR a.clubname LIKE' .$search
                            .' OR b.title LIKE' .$search);
        }

        // Event-list filter
        $event_id = $this->getState('filter.event_id');
        if (is_numeric($event_id))
        {
            $query->where('a.event_id = ' . $event_id);
        }

        // state filter
        $published = $this->getState('filter.published');
        if (is_numeric($published))
        {
            $query->where('a.published = ' . $published);
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=a.checked_out');

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;
        }

    /**
    * Method to get the number of all entries in the eventparts-table
    * 
    * @return type integer 
    */
    public function getAllRows()

    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_event_participants');

        $db->setQuery($query);
        $sets = $db->loadObjectList();

        $allrows = count($sets);

        return $allrows;

    }

    /**
	 * Method to get the data that should be exported.
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
		$pklist = implode(',', $pks);

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);

        // select fields from events table
        $query->select('a.title,a.date');
        $query->from($db->quoteName('#__tkdclub_events', 'a'));  

        // select fields from eventparts table
        $query->select('b.firstname,b.lastname,b.clubname,b.email,b.participants,b.grade,b.age,b.user1,b.user2,b.user3,b.user4');
        $query->join('LEFT', $db->quoteName('#__tkdclub_event_participants', 'b') . ' ON a.id = b.event_id');

        // only selected items in the list
        $query->where('b.id IN ('.$pklist.')');

        // ordering
        $query->order('a.date DESC');

		$db->setQuery((string)$query);
		$rows = $db->loadRowList();

        $headers = array(
            JText::_('COM_TKDCLUB_EVENT_TITLE'),                // a.title
            JText::_('COM_TKDCLUB_EVENT_DATE'),                 // a.date
            JText::_('COM_TKDCLUB_EVENTPARTS_FIRSTNAME'),       // b.firstname
            JText::_('COM_TKDCLUB_EVENTPARTS_LASTNAME'),        // b.lastname
            JText::_('COM_TKDCLUB_EVENTPARTS_CLUB'),            // b.clubname
            JText::_('COM_TKDCLUB_EVENTPARTS_EMAIL'),           // b.email
            JText::_('COM_TKDCLUB_EVENTPARTS_PARTICIPANTS'),    // b.participants       
            JText::_('COM_TKDCLUB_EVENTPARTS_GRADE'),           // b.grade  
            JText::_('COM_TKDCLUB_EVENTPARTS_AGE'),             // b.age   
            JText::_('COM_TKDCLUB_FIELD_USER1'),                // b.user1
            JText::_('COM_TKDCLUB_FIELD_USER2'),                // b.user2 
            JText::_('COM_TKDCLUB_FIELD_USER3'),                // b.user3 
            JText::_('COM_TKDCLUB_FIELD_USER4')                 // b.user4
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
    }
    
 }