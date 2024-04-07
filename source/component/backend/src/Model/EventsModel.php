<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;


/**
 * Model-class for list view 'events'
 */
class EventsModel extends ListModel
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
            $config['filter_fields'] = array('id', 'title', 'date', 'published',);
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
        $query->select('e.*')
                ->from($db->quoteName('#__tkdclub_events', 'e'));

        // Search filter      
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = $db->quote('%'. $db->escape($search, true).'%');
            $query->where('e.id LIKE' .$search
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
        $db	= Factory::getDBO();
        $query = $db->getQuery(true);

        $fields = array(
            'id',  // 0
            'title',     // 1    
            'date',      // 2
            'deadline',  // 3
            'min',       // 4
            'max',       // 5
            'published', // 6
            'notes'      // 7
        );

        $pks = ArrayHelper::toInteger($pks);
		$query->select($db->quoteName($fields))->from($db->quoteName('#__tkdclub_events'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }

        $query->order($db->quoteName('date') . ' DESC');

		$db->setQuery($query);
        $rows = $db->loadRowList();
        
        foreach ($rows as $key => &$row)
        {
            $first = array_slice($row,0,6);
            $second = array_slice($row,6);
            $first[] = TkdclubHelper::GetEventParts($row[0]);
            $row = array_merge($first, $second);
        }

        $headers = array(
            Text::_('COM_TKDCLUB_EVENT_ID'),                       // event_id
            Text::_('COM_TKDCLUB_EVENT_TITLE'),                    // title
            Text::_('COM_TKDCLUB_DATE'),                           // date
            Text::_('COM_TKDCLUB_EVENT_DEADLINE'),                 // deadline
            Text::_('COM_TKDCLUB_EVENT_MINIMUM_PARTICIPANTS'),     // min
            Text::_('COM_TKDCLUB_EVENT_MAXIMUM_PARTICIPANTS'),     // max
            Text::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS'),  // subscribed
            Text::_('JSTATUS'),                                    // published     
            Text::_('COM_TKDCLUB_NOTES')                           // notes
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	// header with column names
		$content	= array_merge( $content,  $rows);
        
        foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0)
			{
				$row[2] = HTMLHelper::_('date', $row[2], Text::_('DATE_FORMAT_LC4'));
				$row[3] = HTMLHelper::_('date', $row[3], Text::_('DATE_FORMAT_LC4'));
				$row[7] == 1 ? $row[7] = Text::_('JPUBLISHED') : $row[7] = Text::_('JUNPUBLISHED');
			}
		}

        return $content;
	}
 
 }