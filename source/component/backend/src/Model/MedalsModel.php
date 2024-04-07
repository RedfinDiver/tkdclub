<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Model-class for list view medals.
 */
class MedalsModel extends ListModel
{       
    /**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = 
            array('id', 'date', 'championship', 'type', 'class', 'placing', 'winner', 'medalyear', 'state');
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

        $winner = $this->getUserStateFromRequest($this->context.'.filter.winner', 'filter_winner', '', 'array');
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
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('m.*')->from('#__tkdclub_medals as m');

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where($db->quoteName('m.state') . ' = :state')
                ->bind(':state', $state, ParameterType::INTEGER);
        }

        // Filter by placing
        $placing = $this->getState('filter.placing');
        if ($placing >= 1)
        {
            $query->where($db->quoteName('m.placing')  . ' = :placing')
                ->bind(':placing', $placing, ParameterType::INTEGER);
        }

        // Filter by athlet
        $winner = $this->getState('filter.winner');
        if (is_numeric($winner))
        {
            $query->where($db->quoteName('m.winner_1') . ' = :winner1', 'OR')
                    ->where($db->quoteName('m.winner_2') . ' = :winner2', 'OR')
                    ->where($db->quoteName('m.winner_3') . ' = :winner3', 'OR')
                    ->bind([':winner1', ':winner2', ':winner3'], $winner, ParameterType::INTEGER);
        }

        // Filter by year
        $medalyear = $this->getState('filter.medalyear');
        if (!empty($medalyear))
        {   
            $query->where('YEAR(m.date) = :medalyear')
                ->bind(':medalyear', $medalyear, ParameterType::INTEGER);
        }

        // Filter by type of championship
        $championshiptype = $this->getState('filter.type');
        if (!empty($championshiptype))
        {
            $query->where($db->quoteName('m.type') . ' = :type')
                ->bind(':type', $championshiptype);
        }

        // Filter by search in different fields
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0) 
            {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('m.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            }
            else
            {   
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->where(
                    '(' . $db->quoteName('m.id') . ' LIKE :search1'
                        . ' OR ' . $db->quoteName('m.date') . ' LIKE :search2'
                        . ' OR ' . $db->quoteName('m.championship') . ' LIKE :search3'
                        . ' OR ' . $db->quoteName('m.class') . ' LIKE :search4' .

                    ')'
                )
                    ->bind([':search1', ':search2', ':search3', ':search4'], $search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=checked_out');

        $sort = $this->getState('list.ordering', 'm.date');
        $order = $this->getState('list.direction', 'DESC');
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
     * Method to get the number of Medals for a place
     * 
     * @param   type integer $placing, 1 for first-place....
     * 
     * @return  type integer number of medals in selected place
     * 
     * @since   1.0
     */
    public static function getMedals ($placing)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__tkdclub_medals'))
            ->where($db->quoteName('placing') . ' = :placing')
            ->bind(':placing', $placing, ParameterType::INTEGER);

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
        $query->select('*')->from($db->quoteName('#__tkdclub_medals'));

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
		$db	= Factory::getDBO();
		$query	= $db->getQuery(true);
        $fields = array(
            'id',           // 0
            'date',         // 1    
            'championship', // 2
            'class',        // 3
            'placing',      // 4
            'winner_1',     // 5
            'winner_2',     // 6
            'winner_3'      // 7
        );

        $pks = ArrayHelper::toInteger($pks);
		$query->select($db->quoteName($fields))->from($db->quoteName('#__tkdclub_medals'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }
			  
        $query->order('date DESC');

		$db->setQuery($query);
		$rows = $db->loadRowList();

        $headers = array(
            Text::_('COM_TKDCLUB_MEDAL_ID'),               // id
            Text::_('COM_TKDCLUB_DATE'),                   // date_win
            Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'),    // championship
            Text::_('COM_TKDCLUB_MEDAL_CLASS'),            // class
            Text::_('COM_TKDCLUB_MEDAL_PLACING'),          // placing
            Text::_('COM_TKDCLUB_ATHLETS'),                // winner_1
            Text::_('COM_TKDCLUB_ATHLETS'),                // winner_2
            Text::_('COM_TKDCLUB_ATHLETS')                 // winner_3
        );

        $memberlist = TkdclubHelper::getMemberlist();

        foreach ($rows as &$row)
        {
            $row[] = TkdclubHelper::getMembersNames($row[5], $memberlist);
            unset($row[5]);

            $row[] = TkdclubHelper::getMembersNames($row[6], $memberlist);
            unset($row[6]);

            $row[] = TkdclubHelper::getMembersNames($row[7], $memberlist);
            unset($row[7]);
        }

		// Return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	// Header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
}
