<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
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
                ->from($db->quoteName('#__tkdclub_events', 'e'));

        // Search filter      
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = $db->quote('%'. $db->escape($search, true).'%');
            $query->where('e.event_id LIKE' .$search
                        .' OR e.title LIKE' .$search
                        .' OR e.date LIKE' .$search);
        }

        // State filter
        $stateselect = $this->getState('filter.state');
        if (is_numeric($stateselect))
        {
            $query->where('e.published = ' . $stateselect);
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')
        ->join('LEFT', '#__users AS u ON u.id=e.checked_out');

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

    /**
	 * Method to get the data that should be exported.
     * 
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
        JLoader::register('TkdclubHelperGetEventParts', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/geteventparts.php');
		$pklist = implode(',', $pks);

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query-> select($db->quoteName(array('event_id', 'title', 'date', 'deadline', 'min', 'max', 'published', 'notes')))
			  -> from($db->quoteName('#__tkdclub_events'))
			  -> where($db->quoteName('event_id') . ' IN ' . '(' . $pklist . ')')
              -> order($db->quoteName('date') . ' DESC');

		$db	-> setQuery((string)$query);
        $rows	= $db->loadRowList();
        
        foreach ($rows as $key => &$row)
        {
            $first = array_slice($row,0,6);
            $second = array_slice($row,6);
            $first[] = TkdclubHelperGetEventParts::geteventparts($row[0]);
            $row = array_merge($first, $second);
        }

        $headers = array(
            JText::_('COM_TKDCLUB_EVENT_ID'),                       // event_id
            JText::_('COM_TKDCLUB_EVENT_TITLE'),                    // title
            JText::_('COM_TKDCLUB_DATE'),                           // date
            JText::_('COM_TKDCLUB_EVENT_DEADLINE'),                 // deadline
            JText::_('COM_TKDCLUB_EVENT_MINIMUM_PARTICIPANTS'),     // min
            JText::_('COM_TKDCLUB_EVENT_MAXIMUM_PARTICIPANTS'),     // max
            JText::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS'),  // subscribed
            JText::_('JSTATUS'),                                    // published     
            JText::_('COM_TKDCLUB_NOTES')                           // notes
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	// header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
 
 }