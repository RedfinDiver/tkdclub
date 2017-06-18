<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for list view 'trainings'
 *
 * @since  1.0
 */
class TkdClubModelTrainings extends JModelList
{      
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = 
            array('training_id', 'date', 'trainer', 'type', 'payment_state', 'year');
        }
        
        parent::__construct($config);
    }
      
    protected function populateState($ordering = 'date', $direction = 'DESC')
    {
        //Suchbegriff aus vorheriger Eingabe ermitteln
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $year = $this->getUserStateFromRequest($this->context.'.filter.year', 'filter_year', '');
        $this->setState('filter.year', $year);

        $trainer = $this->getUserStateFromRequest($this->context.'.filter.trainer', 'filter_trainer', '');
        $this->setState('filter.trainer', $trainer);

        $type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $state = $this->getUserStateFromRequest($this->context.'.filter.payment_state', 'filter_payment_state', '');
        $this->setState('filter.payment_state', $state);

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.year');
        $id	.= ':'.$this->getState('filter.trainer');
        $id	.= ':'.$this->getState('filter.type');
        $id	.= ':'.$this->getState('filter.payment_state');

        return parent::getStoreId($id);
    }
      
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*')
                ->select($db->quoteName('b.firstname') . ' AS ' . $db->quoteName('trainer_firstname'))
                ->select($db->quoteName('b.lastname') . ' AS ' . $db->quoteName('trainer_lastname'))
                ->select($db->quoteName('assist1.firstname') . ' AS ' . $db->quoteName('assist1_firstname'))
                ->select($db->quoteName('assist1.lastname') . ' AS ' . $db->quoteName('assist1_lastname'))
                ->select($db->quoteName('assist2.firstname') . ' AS ' . $db->quoteName('assist2_firstname'))
                ->select($db->quoteName('assist2.lastname') . ' AS ' . $db->quoteName('assist2_lastname'))
                ->select($db->quoteName('assist3.firstname') . ' AS ' . $db->quoteName('assist3_firstname'))
                ->select($db->quoteName('assist3.lastname') . ' AS ' . $db->quoteName('assist3_lastname'))
                ->from($db->quoteName('#__tkdclub_trainings', 'a'))
                ->join('LEFT', $db->quoteName('#__tkdclub_members', 'b') . ' ON (' . $db->quoteName('a.trainer') . ' = ' . $db->quoteName('b.member_id') . ')')
                ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist1') . ' ON (' . $db->quoteName('a.assist1') . ' = ' . $db->quoteName('assist1.member_id') . ')')
                ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist2') . ' ON (' . $db->quoteName('a.assist2') . ' = ' . $db->quoteName('assist2.member_id') . ')')
                ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist3') . ' ON (' . $db->quoteName('a.assist3') . ' = ' . $db->quoteName('assist3.member_id') . ')');

        $yearselect = $this->getState('filter.year');
        if (!empty($yearselect)) {
            $ys = $db->quote('%' .$db->escape($yearselect).'%');
            $query->where('date LIKE ' .$ys);
        } 

        $trainerselect = $this->getState('filter.trainer');
        if (!empty($trainerselect))
        {
            $ts = $db->quote($db->escape($trainerselect, true));
            $query->where(' (trainer = ' .$ts
                            .' OR assist1 = '.$ts
                            .' OR assist2 = '.$ts
                            .' OR assist3 = '.$ts. ' )');
        }

        $typeselect = $this->getState('filter.type');
        if (!empty($typeselect))
        {   
            $type = $db->quote($db->escape($typeselect, true));
            $query->where('type = ' .$type);
        }

        $stateselect = $this->getState('filter.payment_state'); // TODO
        if (is_numeric($stateselect))
        {   
            $query->where('payment_state = ' . (int) $stateselect);
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('id = '.(int) substr($search, 3));
            }
            else
            {
                $search = $db->Quote('%'. $db->escape($search).'%');
                $query->where('training_id LIKE' .$search
                            .'OR date LIKE' .$search
                            .'OR trainer LIKE' .$search
                            .'OR assist1 LIKE' .$search
                            .'OR type LIKE' .$search);
            }
        }

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;

    }

    /**
    * Method to get the number of all entries in the trainings-table
    * 
    * @return type integer
    * 
    */
    public function getAllRows()
    {

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
                ->from($db->quoteName('#__tkdclub_trainings'));

        $db->setQuery($query);
        $allrows = $db->loadResult();

        return $allrows;

    }

    /**
        * Method to get sum of all participants of all entries in the trainings-table
        * 
        * @return type integer number of all entries in the trainings-table
        * 
        */
    public function getTrainingspart()
    {

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('SUM(participants)')
                ->from($db->quoteName('#__tkdclub_trainings'));

        $db->setQuery($query);
        $trainingspart = $db->loadResult();

        return $trainingspart;

    }

    /**
        * Method to calculate trainer salary
        * The given parameters are used to calculate, so please define them to get
        * the right calculation
        * 
        * @param  type float $km distance made by car for training
        * @return type float The calculated trainer salary
        * 
        */
    public static function getTrainerSalary($km)
    {
        $training = JComponentHelper::getParams('com_tkdclub')->get('training_salary', 0);
        $distance = JComponentHelper::getParams('com_tkdclub')->get('distance_salary', 0);

        $salary = $training + ($distance * $km);

        return floatval($salary);
    }

    /**
        * Method to calculate assistent salary.
        * The given parameters are used to calculate, so please define them in config to get
        * the right calculation
        * 
        * @param type float $km distance made by car for training
        * @return type float The calculated assistent salary
        * 
        * @since   2.0
        */
    public static function getAssistentSalary($km)
    {

        $training = JComponentHelper::getParams('com_tkdclub')->get('assistent_salary', 0);          
        $distance = JComponentHelper::getParams('com_tkdclub')->get('distance_salary', 0);

        $salary = $training + ($distance * $km);

        return floatval($salary);
    }

    /** 
        * Get training numbers from database
        * 
        * @return type string The calculated assistent salary
        * 
        */
    public function getTrainings()
    {   
        if ($this->getState('filter.type') != '')
        {

            $filter_type = 'AND type = '.'"'.$this->getState('filter.type'). '"';
        }
        else
        {
            $filter_type = NULL;
        }

        if ($this->getState('filter.year') != '')
        {

            $filter_year = 'AND date LIKE '.'"%'.$this->getState('filter.year'). '%"';

        }
        else
        {
            $filter_year = NULL;
        }

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
                ->from($db->quoteName('#__tkdclub_trainings'))
                ->where('trainer = '.'"' .$this->getState('filter.trainer'). '"'
                        .$filter_type
                        .$filter_year

                        );

        $db->setQuery($query);
        $trainings = $db->loadResult();

        return $trainings;
    }

    /** 
        * Get trainer name from database
        * 
        * @return type string trainer name in 'firstname lastname' format
        * 
        */
    public static function getTrainerName ($member_id)
    {
        if ($member_id)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select(array('firstname, lastname'))
                ->from($db->quoteName('#__tkdclub_members'))
                ->where("member_id = " . (int) $member_id);

            $db->setQuery($query);
            $result = $db->loadObject();

            return $result->firstname . ' ' . $result->lastname;
        }
        else
        {
            return '';
        }

    }

    /**
     * Get all the data for a specific trainer
     *
     * With this function one gets the data for every trainer in the database
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getTrainerData()
    {   
        // get values from configuration
        $training_salary = JComponentHelper::getParams('com_tkdclub')->get('training_salary', 0);
        $assist_salary = JComponentHelper::getParams('com_tkdclub')->get('assistent_salary', 0);
        $distance_rate = JComponentHelper::getParams('com_tkdclub')->get('distance_salary', 0);

        // getting all trainer names from database
        $names = $this->get_all_trainer_from_database();

        // loop through the trainer names an get their data
        foreach ($names as $trainer_id => $trainer_name)
        {
            $trainer = new stdClass; // initialise the container
            $trainer->trainer_id   = $trainer_id;
            $trainer->trainer_name = $trainer_name;

            // get overall trainingsdata from the database for the trainer
            $all_trainings = $this->get_all_trainings_for_one_trainer($trainer_id);

            $overall_to_transfer = array();
            $overall_to_transfer['trainings'] = count($all_trainings);
            $overall_to_transfer['types'] = array_count_values(array_column($all_trainings, 'type'));
            $overall_to_transfer['payment_state'] = array_count_values(array_column($all_trainings, 'payment_state'));

            $trainer->sums = $overall_to_transfer; 
            
            // get trainingsdata for every year from the database
            $training_years = $this->get_all_training_years_from_database();

            foreach ($training_years as $year)
            {
                $trainings_in_year             = $this->get_all_trainings_for_one_trainer($trainer_id, $year);
                
                $year_to_transfer = array();
                $year_to_transfer['trainings'] = count($trainings_in_year);
                $year_to_transfer['types'] = array_count_values(array_column($trainings_in_year, 'type'));
                $year_to_transfer['payment_state'] = array_count_values(array_column($trainings_in_year, 'payment_state'));

                $trainer->$year        = $year_to_transfer;
            }

            $trainerdata[] = $trainer;
        }

        usort($trainerdata, array($this, 'sortTrainers')); // sort by most trainings

        return array_reverse($trainerdata); // reverse order from most trainings to less trainings
    }

    /**
     * sort function for trainings
     **/
    function sortTrainers($a, $b)
    {
        if ($a->sums['trainings'] == $b->sums['trainings']) {
            return 0;
        }
        return ($a->sums['trainings'] < $b->sums['trainings']) ? -1 : 1;
    }

    /**
     * Get all trainers from database
     *
     * This method is used to get the id and the name of every single
     * trainer in the database
     *
     * @return array in the form [ int trainer_id => strg 'firstname lastname']
     **/
    public function get_all_trainer_from_database()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('member_id AS trainer_id')
              ->select('CONCAT(firstname, " ", lastname) AS trainer_name' )
              ->from($db->quoteName('#__tkdclub_members'))
              ->where($db->quoteName('functions') . ' LIKE ' . $db->quote('%trainer%'))
              ->where($db->quoteName('member_state') . ' = ' .  $db->quote('active'));
        $db->setQuery($query);
        
        return $db->loadAssocList('trainer_id', 'trainer_name');
    }

    /**
     * Load all datasets for 1 specific trainer (per year)
     *
     * With this function all datasets for 1 specific trainer are loaded
     * Given the 2. parameter the data is loaded for a specfic year
     *
     * @param  $trainer_id  int    member_id of the trainer
     * @param  $year        int    year of training data 4 digits e.g. '2017'
     * @return              array  the datasets as array
     **/
    public function get_all_trainings_for_one_trainer($trainer_id, $year = null)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
              ->from($db->quoteName('#__tkdclub_trainings'))
              ->where('('.$db->quoteName('trainer') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist1') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist2') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist3') . ' = ' .  $trainer_id . ')');
        
        if($year)
        {
            $query->where($db->quoteName('date') . ' LIKE ' . $db->quote('%'. $year .'%'));
        }

        $db->setQuery($query);

        return $db->loadAssoclist();
    }

    /**
     * get all the years in which a training was held
     *
     * This function gets all years in 4 digit form (2017) from database
     * It serves as a list for a database-query
     *
     * @return array
     **/
    public function get_all_training_years_from_database()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT substring(date,1,4)');
            $query->from($db->quoteName('#__tkdclub_trainings'));
            $query->order($db->quoteName('date') . 'ASC');

            $db->setQuery($query);

        return $db->loadColumn();
    }

    /**
     * get the sum of distance for not paid trainings for a specfic trainer
     *
     * @return int
     **/
    public function get_sum_distance_of_unpaid_trainings($trainer_id)
    {

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
        $fields = array(
                        'training_id', 'date', 'trainer', 'km_trainer', 'assist1', 'km_assist1', 'assist2', 'km_assist2', 'assist3', 'km_assist3',
                        'type', 'participants', 'payment_state');
                        
		$query-> select($db->quoteName($fields))
			  -> from($db->quoteName('#__tkdclub_trainings'))
			  -> where('id IN ('.$pklist.')')
              ->order('id DESC');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        $headers = array(
            JText::_('COM_TKDCLUB_TRAINING_ID'),            // training_id
            JText::_('COM_TKDCLUB_TRAINING_DATE'),          // date
            JText::_('COM_TKDCLUB_TRAINING_TRAINER'),       // trainer
            JText::_('COM_TKDCLUB_TRAINING_KM'),            // km_trainer
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1'),    // assist1
            JText::_('COM_TKDCLUB_ASSSIT1_KM'),             // km_assist1       
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2'),    // assist2
            JText::_('COM_TKDCLUB_ASSSIT2_KM'),             // km_assist2
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3'),    // assist3
            JText::_('COM_TKDCLUB_ASSSIT3_KM'),             // km_assist2
            JText::_('COM_TKDCLUB_TRAINING_TYPE'),          // type
            JText::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'),  // participants
            JText::_('COM_TKDCLUB_TRAINING_PAID'),          // payment_state
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
        
}