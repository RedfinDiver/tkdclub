<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

/**
 * Model-class for list view 'medals'
 */
class MedalsModel extends ListModel
{       
    /**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
     * 
	 */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = 
            array('medal_id', 'date', 'championship', 'type', 'class', 'placing', 'winner_ids', 'winner', 'medalyear', 'state');
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
	 * @since   1.0
	 */
    protected function populateState($ordering = 'date', $direction = 'DESC')
    {
        /* Suchbegriff aus vorheriger Eingabe ermitteln */
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $placing = $this->getUserStateFromRequest($this->context.'.filter.placing', 'filter_placing', '', 'integer');
        $this->setState('filter.placing', $placing);

        $winner = $this->getUserStateFromRequest($this->context.'.filter.winner', 'filter_winner', '', 'integer');
        $this->setState('filter.winner', $winner);

        $medalyear = $this->getUserStateFromRequest($this->context.'.filter.medalyear', 'filter_medalyear', '', 'integer');
        $this->setState('filter.medalyear', $medalyear);

        $type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string');
        $this->setState('filter.type', $type);

        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'integer');
        $this->setState('filter.state', $state);

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
	 */
    protected function getStoreId($id = '')
    {
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.placing');
        $id	.= ':'.$this->getState('filter.winner');
        $id	.= ':'.$this->getState('filter.medalyear');
        $id	.= ':'.$this->getState('filter.type');
        $id	.= ':'.$this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_medals');

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where($db->quoteName('state') . ' = :state')
                ->bind(':state', $state, ParameterType::INTEGER);
        }

        // Filter by placing
        $placing = $this->getState('filter.placing');
        if ($placing >= 1)
        {
            $query->where($db->quoteName('placing')  . ' = :placing')
                ->bind(':placing', $placing, ParameterType::INTEGER);
        }

        // Filter by athlet
        $winner = $this->getState('filter.winner');
        if (is_numeric($winner))
        {
            $regex = "[[:<:]]" . (int)$winner . "[[:>:]]";
            $query->where($db->quoteName('winner_ids') . ' REGEXP ' . ':winner')
                ->bind(':winner', $regex);
        }

        // Filter by year
        $medalyear = $this->getState('filter.medalyear');
        if (!empty($medalyear))
        {   
            $query->where('YEAR(date) = :medalyear')
                ->bind(':medalyear', $medalyear, ParameterType::INTEGER);
        }

        // Filter by type of championship
        $championshiptype = $this->getState('filter.type');
        if (!empty($championshiptype))
        {
            $query->where($db->quoteName('type') . ' = :type')
                ->bind(':type', $championshiptype);
        }

        // Filter by search in different fields
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0) 
            {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('medal_id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            }
            else
            {   
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->where(
                    '(' . $db->quoteName('medal_id') . ' LIKE :search1'
                        . ' OR ' . $db->quoteName('date') . ' LIKE :search2'
                        . ' OR ' . $db->quoteName('championship') . ' LIKE :search3'
                        . ' OR ' . $db->quoteName('class') . ' LIKE :search4' .

                    ')'
                )
                    ->bind([':search1', ':search2', ':search3', ':search4'], $search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=checked_out');

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;
    }

    /**
     * Method to get the number of all entries in the medals-table
     * 
     * @return type integer
     */
    public function getAllRows()
    {
        return $this->getMedaldata($count = true);
    }
         
    /**
     * 
     * @param type integer $placing, 1 for first-place....
     * @return type integer number of medals in selected place
     * 
     * @since   1.0
     */
    public static function getMedals ($placing)
    {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                ->from('#__tkdclub_medals');
            $query->where('placing =' .$placing);

            $db->setQuery($query);
            $medals = $db->loadResult();

            return (int) $medals;

    }

    /**
    * Method for getting inormation about medals
    *
    * @return  mixed  an array on success, false on failure
    */
    public function getMedaldata($count = false)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_medals');

        $db->setQuery($query);
        $data = $db->loadObjectList();

        if ($count == true)
        {
            return (int) count($data);
        }

        $statistics = array();
        $statistics['sum'] = count($data);

        $statistics['placings'] = array_count_values(array_column($data, 'placing'));

        return $statistics;
    }

    /**
	 * Method to get the data that should be exported.
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
		$pklist = implode(',', $pks);

		$db	= Factory::getDBO();
		$query	= $db->getQuery(true);
		$query-> select('medal_id, date, championship, class, placing, winner_ids')
			  -> from($db->quoteName('#__tkdclub_medals'))
			  -> where('medal_id IN ('.$pklist.')')
              ->order('date DESC');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        $headers = array(
            Text::_('COM_TKDCLUB_MEDAL_ID'),               // id
            Text::_('COM_TKDCLUB_DATE'),                   // date_win
            Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'),    // championship
            Text::_('COM_TKDCLUB_MEDAL_CLASS'),            // class
            Text::_('COM_TKDCLUB_MEDAL_PLACING'),          // placing
            Text::_('COM_TKDCLUB_ATHLETS'),                // winner_ids       
            Text::_('COM_TKDCLUB_ATHLETS')                 // id_win  
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
    
}