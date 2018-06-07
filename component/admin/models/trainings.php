<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperTrainer', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/trainer.php');

/**
 * Model-class for list view 'trainings'
 *
 * @since  1.0
 */
class TkdClubModelTrainings extends JModelList
{   
    public $trainer_names; // all trainers in database
    public $training_years; // all years in which a training was held
    public $training_salary;
    public $training_types;
    public $assist_salary;
    public $distance_rate;

    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = 
            array('training_id', 'date', 'trainer', 'type', 'payment_state', 'year');
        }

        // getting all trainer names from database
        $this->trainer_names = TkdclubHelperTrainer::getTrainer($fromTrainingsTable = true);

        // getting all trainingyears from database
        $this->training_years = $this->get_all_training_years_from_database();

        // getting all trainingtypes from database
        $this->training_types = $this->get_all_training_types_from_database();

        // get values from configuration
        $this->training_salary = JComponentHelper::getParams('com_tkdclub')->get('training_salary', 0);
        $this->assist_salary = JComponentHelper::getParams('com_tkdclub')->get('assistent_salary', 0);
        $this->distance_rate = JComponentHelper::getParams('com_tkdclub')->get('distance_salary', 0);
        
        parent::__construct($config);
    }
    /**
     * checks if the parameters for calculating the trainer/assistent salary are properly set
     * 
     * @return bool true if all parameters are set, false if one ore more are not set
     * 
     */
    public function getSalaryparams()
    {
        if ($this->training_salary && $this->assist_salary && $this->distance_rate)
        {
            return true;
        }
        else
        {
            return false;
        }
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

        $stateselect = $this->getState('filter.payment_state');
        if (is_numeric($stateselect))
        {   
            // All unpaid trainings
            if ($stateselect == 0)
            {
                // Remember: standard glue is AND, so every statement is joined to the query with 'and'!
                $query->where('trainer_paid=0')->where('assist1_paid=0')->where('assist2_paid=0')->where('assist3_paid=0');
            }
            elseif ($stateselect == 1)
            {
                $condition1 = '(trainer_paid=1)';
                $condition2 = '((`assist1`> 0 AND `assist1_paid`= 1) OR `assist1`= 0 )';
                $condition3 = '((`assist2`> 0 AND `assist2_paid`= 1) OR `assist2`= 0 )';
                $condition4 = '((`assist3`> 0 AND `assist3_paid`= 1) OR `assist3`= 0 )';

                $query->where($condition1)->where($condition2)->where($condition3)->where($condition4);
  
            }
            elseif ($stateselect == 2)
            {
                $condition1 = '(trainer_paid=1 AND
                 ((assist1>0 AND assist1_paid=0) OR
                  (assist2>0 AND assist2_paid=0) OR
                  (assist3>0 AND assist3_paid=0)
                ))';

                $condition2 = '(assist1_paid=1 AND 
                 ((trainer_paid=0) OR
                  (assist2>0 AND assist2_paid=0) OR
                  (assist3>0 AND assist3_paid=0)
                ))';

                $condition3 = '(assist2_paid=1 AND
                 ((trainer_paid=0) OR
                  (assist1>0 AND assist1_paid=0) OR
                  (assist3>0 AND assist3_paid=0)
                ))';

                $condition4 = '(assist3_paid=1 AND
                 ((trainer_paid=0) OR
                  (assist1>0 AND assist1_paid=0) OR
                  (assist2>0 AND assist2_paid=0)
                ))';

                $query->where($condition1, 'OR')->where($condition2, 'OR')->where($condition3, 'OR')->where($condition4, 'OR');
            }
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('trainings_id = '.(int) substr($search, 3));
            }
            else
            {
                $search = $db->Quote('%'. $db->escape($search).'%');
                $query->where('training_id LIKE' .$search
                            .'OR date LIKE' .$search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=a.checked_out');

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;

    }

    /**
     * Get all the statistic data for a specific trainer
     *
     * This function gets the data for every trainer in the database
     *
     * @return  mixed   The data for the trainers
     *
     **/
    public function getTrainerData()
    {   
        // Return if there are no trainers, which basically means there are no datasets
        if (!$this->trainer_names)
        {
            return null;
        }

        // Loop through the trainer names and get their data
        foreach ($this->trainer_names as $container)
        {
            $trainer_id = $container->member_id;
            $trainer_name = $container->firstname . ' ' . $container->lastname;

            // Initialise the container and some variables
            $trainer = new stdClass;
            $trainer->trainer_id   = $trainer_id;
            $trainer->trainer_name = $trainer_name;
            $trainer->sex = $container->sex;
            $year_data = array();
            $sum_data = array('trainings' => 0,
                              'trainer' => 0,
                              'assistent' => 0,
                              'unpaid' => 0,
                              'unpaid_sum' => 0, 
                              'types' => array());

            // Get trainingsdata for every year from database and analyse it
            foreach ($this->training_years as $year)
            {
                $trainings_in_year = $this->get_trainings_from_db($trainer_id, $year);
                $trainer->$year = $this->analyse_trainerdata($trainings_in_year, $trainer_id, $this->training_salary, $this->assist_salary, $this->distance_rate);
                $trainer->sums = $this->sum_up_trainerdata($trainer->$year, $sum_data);
            }
            
            $trainerdata[] = $trainer;  // send the collected data to variable
        }

        usort($trainerdata, array($this, 'sortTrainers')); // sort by most trainings

        return array_reverse($trainerdata); // reverse order from most trainings to less trainings
    }

    /**
     * Get all the statitics data for all trainings
     *
     * This function gets the statistic data for all trainings in the database
     *
     * @return  mixed   The statistic data of all trainings
     *
     **/
     public function getTrainingsData()
     {  
        // Return if there are no training years, which basically means there are no datasets
        if (!$this->training_years)
        {
            return null;
        }

        // Initialise the container and some variables
        $trainingsdata = new stdClass;
        $sum_data = array('trainings' => 0, 'average' => 0, 'types' => array(), 'parts' => array());
        $sum_parts = 0; // collecting sum of participants for every year

        // Get trainingsdata for every year from database
        foreach ($this->training_years as $year)
        {
            $i = 0; // count variable to make sure the overall participants are not count twice or more each year
            $data = $this->get_trainings_from_db(null, $year);

            $year_data = array();
            $sum_parts_year = 0;
            $year_data['trainings'] = count($data);
            $sum_data['trainings'] += $year_data['trainings'];

            $year_data['types'] = array_count_values(array_column($data, 'type'));
            $sum_data['types'] = $this->sum_up($year_data['types'], $sum_data['types']);

            // loop through the training types and calculate the average participants
            foreach ($this->training_types as $type)
            {   
                $parts = 0;
                !isset($sum_parts_overall[$type]) ? $sum_parts_overall[$type] = 0 : null; // collecting sum of participants for all types overall all traininigs

                foreach ($data as $item)
                {
                    $item['type'] == $type ? $parts += $item['participants'] : null;
                    $i == 0 ? $sum_parts += $item['participants'] : null; // check if it is the first run for the type
                    $i == 0 ? $sum_parts_year += $item['participants'] : null; // check if it is the first run for the type
                }

                $parts > 0 ? $year_data['parts'][$type] = round($parts / $year_data['types'][$type], 1) : null;
                $sum_parts_overall[$type] += $parts;
                $i++;
            }

            $year_data['average'] = round($sum_parts_year / $year_data['trainings'], 1);
            $trainingsdata->$year = $year_data;
        }

        // calculating the average participation for all trainings
        foreach ($sum_data['types'] as $key => $value) 
        {
            $sum_parts_overall[$key] = round($sum_parts_overall[$key] / $value, 1);
        }
        
        $sum_data['parts'] = $sum_parts_overall;
        $sum_data['average'] = round($sum_parts / $sum_data['trainings'], 1);
        $trainingsdata->sums = $sum_data;

        $data = $this->prepareTrainingYearsChart($trainingsdata);
        $data = $this->prepareParticipantsYearChart($data);
        $data->currency = JComponentHelper::getParams('com_tkdclub')->get('currency', '€');

        return $data;
    }

    /**
     * Prepare data for trainings per year chart
     */
    public function prepareTrainingYearsChart(&$data)
    {
        $chartdata = array();
        $traintypes = array();

        $chartdata[0][] = JText::_('COM_TKDCLUB_YEAR');

        // First find all training - types in dataset
        foreach ($data->sums['types'] as $type => $value)
        {
            $chartdata[0][] = $type;
            $traintypes[] = $type;
        }

        foreach ($data as $year => $value)
        {
            $i = count($chartdata);
            if (is_numeric($year))
            {
                $chartdata[$i][] = $year;
                foreach ($traintypes as $traintype)
                {
                    if (array_key_exists($traintype, $value['types']))
                    {
                        $chartdata[$i][] = $value['types'][$traintype];
                    }
                    else
                    {
                        $chartdata[$i][] = 0;  // When there is no type set it to 0
                    }
                }
            }
        }
        $data->TrainingYearsChart = $chartdata;
        return $data;
    }

    /**
     * Prepare data for average participants per year and training type chart
     */
    public function prepareParticipantsYearChart(&$data)
    {
        $chartdata = array();
        $chartdata[0] = $data->TrainingYearsChart[0]; // The same as in the TrainingsYearsChart
        
        foreach ($data as $year => $value)
        {
            $j = count($chartdata);
            if (is_numeric($year))
            {
                $chartdata[$j][] = $year;
                foreach ($this->training_types as $type)
                {
                    if (array_key_exists($type, $data->{$year}['parts']))
                    {
                        $chartdata[$j][] = $data->{$year}['parts'][$type];
                    }
                    else
                    {
                        $chartdata[$j][] = 0;
                    }
                }

            }
        }

        $data->ParticipantsYearChart = $chartdata;
        return $data;
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
     * Get all trainining types from database
     *
     * This method is used to get every type of training from the database
     *
     * @return array a list of all trainings from the database
     **/
    public function get_all_training_types_from_database()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT type');
            $query->from($db->quoteName('#__tkdclub_trainings'));
            $db->setQuery($query);

        return $db->loadColumn();
    }

    /**
     * Load training datasets
     *
     * Retreive data for trainings from the database.
     * Given the 1. paramter data ist for 1. specific trainer
     * Given the 2. parameter the data is loaded for a specfic year
     *
     * @param  $trainer_id  int    member_id of the trainer
     * @param  $year        int    year of training data 4 digits e.g. '2017'
     * @return              array  the datasets as array
     **/
    public function get_trainings_from_db($trainer_id = null, $year = null)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__tkdclub_trainings'));

        if ($trainer_id)
        {
              $query->where('('.$db->quoteName('trainer') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist1') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist2') . ' = ' .  $trainer_id
             . ' OR ' . $db->quoteName('assist3') . ' = ' .  $trainer_id . ')');
        }
             
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
     * analyses the trainer data  
     *
     * @return array statistics of the trainings as trainer or assistent
     **/
    public function analyse_trainerdata($trainings_data, $trainer_id, $training_salary = 0, $assist_salary = 0, $distance_rate = 0)
    {
        $trainer = 0;
        $assist = 0;
        $unpaid_trainer = 0;
        $unpaid_assist = 0;
        $unpaid_km = 0;
        $types = array();
        $container = array();

        $trainings = count($trainings_data);

        foreach ($trainings_data as $key => $value)
        {
            if ($value['trainer'] == $trainer_id )
            {
                $trainer++;
                isset($types[$value['type']]['trainer']) ? $types[$value['type']]['trainer']++ : $types[$value['type']]['trainer'] = 1;
                if ($value['trainer_paid'] == 0)
                {  
                    $unpaid_trainer++;
                    $unpaid_km += $value['km_trainer'];
                }
            }
            else
            {
                $assist++;
                isset($types[$value['type']]['assistent']) ? $types[$value['type']]['assistent']++ : $types[$value['type']]['assistent'] = 1;
                
                if($value['assist1'] == $trainer_id && $value['assist1_paid'] == 0)
                {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist1'];
                }
                elseif($value['assist2'] == $trainer_id && $value['assist2_paid'] == 0)
                {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist2'];
                }
                elseif($value['assist3'] == $trainer_id && $value['assist3_paid'] == 0)
                {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist3'];
                }
            }
        }

        $container = array('trainings' => $trainings, 'trainer' => $trainer,'assistent' => $assist,
                            'unpaid' => $unpaid_trainer + $unpaid_assist,
                            'unpaid_sum' => $unpaid_km * $distance_rate + 
                                            $unpaid_trainer * $training_salary +
                                            $unpaid_assist * $assist_salary);


        foreach ($types as &$type)
        {
           if (array_key_exists('trainer', $type) && !array_key_exists('assistent', $type))
           {
                $type['trainings'] = $type['trainer'];
                $type['assistent'] = 0;
           }

           if (!array_key_exists('trainer', $type) && array_key_exists('assistent', $type))
           {
                $type['trainings'] = $type['assistent'];
                $type['trainer'] = 0;
           }

           if (array_key_exists('trainer', $type) && array_key_exists('assistent', $type))
           {
                $type['trainings'] = $type['assistent'] + $type['trainer'];
           }
        }

        $container['types'] = $types;                                                        
        return $container;
    }

    /**
     * Method to sum all types in an array
     *
     */
     public function sum_up($a, &$b)
     {
         foreach ($a as $key => $value)
         {
             if (array_key_exists($key, $b))
             {
                $b[$key] = $value + $b[$key];
             }
             else
             {
                $b[$key] = $value;
             }
         }

         return $b;
     }

     /**
     * Method to sum all trainerdata
     *
     */
     public function sum_up_trainerdata($year_data, &$sum_data)
     {
        $sum_data['trainings'] += $year_data['trainings'];
        $sum_data['trainer'] += $year_data['trainer'];
        $sum_data['assistent'] += $year_data['assistent'];
        $sum_data['unpaid'] += $year_data['unpaid'];
        $sum_data['unpaid_sum'] += $year_data['unpaid_sum'];

        foreach($year_data['types'] as $type => $value)
        {
            if (isset($sum_data['types'][$type]))
            {
                $sum_data['types'][$type]['trainings'] += $value['trainings'];
                $sum_data['types'][$type]['trainer'] += $value['trainer'];
                $sum_data['types'][$type]['assistent'] += $value['assistent'];
            }
            else
            {
                $sum_data['types'][$type]['trainings'] = $value['trainings'];
                $sum_data['types'][$type]['trainer'] = $value['trainer'];
                $sum_data['types'][$type]['assistent'] = $value['assistent'];
            }
        }

        return $sum_data;
     }

    /**
    * Method to get the number of all entries in the trainings-table
    * 
    * @return type integer
    * 
    * @since   1.0
    */
    public function getAllRows()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
                ->from($db->quoteName('#__tkdclub_trainings'));

        $db->setQuery($query);
        
        return $db->loadResult();
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
                        'training_id', 'date',
                        'trainer', 'km_trainer', 'trainer_paid',
                        'assist1', 'km_assist1', 'assist1_paid',
                        'assist2', 'km_assist2', 'assist2_paid',
                        'assist3', 'km_assist3', 'assist3_paid',
                        'type', 'participants');
                        
		$query-> select($db->quoteName($fields))
			  -> from($db->quoteName('#__tkdclub_trainings'))
			  -> where('training_id IN ('.$pklist.')')
              ->order('training_id DESC');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        $headers = array(
            JText::_('COM_TKDCLUB_TRAINING_ID'),                // training_id
            JText::_('COM_TKDCLUB_DATE'),                       // date
            JText::_('COM_TKDCLUB_TRAINING_TRAINER'),           // trainer
            JText::_('COM_TKDCLUB_TRAINING_TRAINER_KM'),        // km_trainer
            JText::_('COM_TKDCLUB_TRAINING_TRAINER_PAID'),      // trainer_paid
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1'),        // assist1
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1_KM'),     // km_assist1
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1_PAID'),   // assist1_paid      
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2'),        // assist2
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2_KM'),     // km_assist2
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2_PAID'),   // assist2_paid    
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3'),        // assist3
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3_KM'),     // km_assist2
            JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3_PAID'),   // assist3_paid 
            JText::_('COM_TKDCLUB_TRAINING_TYPE'),              // type
            JText::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'),      // participants
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
        
}