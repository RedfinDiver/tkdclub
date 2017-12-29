<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for list view 'events'
 */
class TkdClubModelEvents extends JModelList
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
            $config['filter_fields'] = array('event_id', 'title', 'date', 'published',);
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
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
        $this->setState('filter.state', $state);

        parent::populateState('date', 'DESC');
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
        $id .= ':' . $this->getState('filter.state');

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
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
                ->from($db->quoteName('#__tkdclub_events'));

        // Search filter      
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            
            $search = $db->quote('%'. $db->escape($search, true).'%');
            $query->where('event_id LIKE' .$search
                        .' OR title LIKE' .$search
                        .' OR date LIKE' .$search);
            
        }

        // State filter
        $stateselect = $this->getState('filter.state');
        if (is_numeric($stateselect))
        {
            $query->where('published = ' . $stateselect);
        }

        $sort  = $this->getState('list.ordering', 'date');
        $order = $this->getState('list.direction', 'desc');

        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;
    }

    /**
    * Method to get the number of all entries in the events-table
    * 
    * @return type integer 
    */
    public function getAllRows()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_events');

        $db->setQuery($query);
        $sets = $db->loadObjectList();

        $allrows = count($sets);

        return $allrows;
    }
 
 }