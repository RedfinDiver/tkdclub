<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for list view 'medals'
 */
class TkdClubModelMedals extends JModelList
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
            array('medal_id', 'date', 'championship', 'type', 'class', 'placing', 'winner_ids', 'winner', 'medalyear');
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

        $search = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string');
        $this->setState('filter.type', $search);

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

        $placing = $this->getState('filter.placing');
        if ($placing >= 1)
        {
            $query->where('placing = '. (int) $placing );
        }

        $winner = $this->getState('filter.winner');
        if (!empty($winner))
        {
            $query->where('winner_ids REGEXP \'' . '[[:<:]]' . (int)$winner . '[[:>:]]\'');
        }

        $medalyear = $this->getState('filter.medalyear');
        if (!empty($medalyear))
        {   
            $medalyear = $db->Quote('%'. $db->escape($medalyear).'%');
            $query->where('date LIKE ' . $medalyear);
        }

        $championshiptype = $this->getState('filter.type');
        if (!empty($championshiptype))
        {
            $championshiptype = $db->quote($championshiptype);
            $query->where('type=' . $championshiptype);
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = $db->Quote('%'. $db->escape($search).'%');
            $query->where('medal_id LIKE' .$search
                        .'OR date LIKE' .$search
                        .'OR championship LIKE' .$search
                        .'OR type LIKE' .$search
                        .'OR class LIKE' .$search
                        .'OR placing LIKE' .$search
                        .'OR winner_ids LIKE' .$search);

        }

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
            $db = JFactory::getDbo();
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
        $db = JFactory::getDbo();
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

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query-> select('medal_id, date, championship, class, placing, winner_ids')
			  -> from($db->quoteName('#__tkdclub_medals'))
			  -> where('medal_id IN ('.$pklist.')')
              ->order('date DESC');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        $headers = array(
            JText::_('COM_TKDCLUB_MEDAL_ID'),               // id
            JText::_('COM_TKDCLUB_DATE'),                   // date_win
            JText::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'),    // championship
            JText::_('COM_TKDCLUB_MEDAL_CLASS'),            // class
            JText::_('COM_TKDCLUB_MEDAL_PLACING'),          // placing
            JText::_('COM_TKDCLUB_ATHLETS'),                // winner_ids       
            JText::_('COM_TKDCLUB_ATHLETS')                 // id_win  
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
    
}