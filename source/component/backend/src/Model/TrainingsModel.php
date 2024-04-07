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
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Model-class for list view trainings.
 *
 * @since  1.0
 */
class TrainingsModel extends ListModel
{   
    /**
	 * An array of all trainers in database
	 *
	 * @var  array
	 */
    public $trainer_names;
    
    /**
	 * An array of all years in which a training was held
	 *
	 * @var  array
	 */
    public $training_years;

    /**
	 * An array of all training types in database
	 *
	 * @var  array
	 */
    public $training_types;

    /**
	 * An integer value of trainings salary
	 *
	 * @var  integer
	 */
    public $training_salary;

    /**
	 * An integer value of assistent salary
	 *
	 * @var  integer
	 */
    public $assist_salary;

    /**
	 * An integer value of the rate per km
	 *
	 * @var  integer
	 */
    public $distance_rate;

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
                array('id', 'date', 'trainer', 'type', 'payment_state', 'year');
        }

        $this->trainer_names   = TkdclubHelper::getTrainer($fromTrainingsTable = true);
        $this->training_years  = $this->get_all_training_years_from_database();
        $this->training_types  = $this->get_all_training_types_from_database();
        $this->training_salary = ComponentHelper::getParams('com_tkdclub')->get('training_salary', 0);
        $this->assist_salary   = ComponentHelper::getParams('com_tkdclub')->get('assistent_salary', 0);
        $this->distance_rate   = ComponentHelper::getParams('com_tkdclub')->get('distance_salary', 0);

        parent::__construct($config);
    }

    /**
     * Checks if the parameters for calculating the trainer/assistent salary are properly set
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
	 * @since   1.6
	 */
    protected function populateState($ordering = 'date', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $year = $this->getUserStateFromRequest($this->context . '.filter.year', 'filter_year', '');
        $this->setState('filter.year', $year);

        $trainer = $this->getUserStateFromRequest($this->context . '.filter.trainer', 'filter_trainer', '');
        $this->setState('filter.trainer', $trainer);

        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $state = $this->getUserStateFromRequest($this->context . '.filter.payment_state', 'filter_payment_state', '');
        $this->setState('filter.payment_state', $state);

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
	 * @since   1.6
	 */
    protected function getStoreId($id = '')
    {
        $id    .= ':' . $this->getState('filter.search');
        $id    .= ':' . $this->getState('filter.year');
        $id    .= ':' . $this->getState('filter.trainer');
        $id    .= ':' . $this->getState('filter.type');
        $id    .= ':' . $this->getState('filter.payment_state');

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
            ->join('LEFT', $db->quoteName('#__tkdclub_members', 'b') . ' ON (' . $db->quoteName('a.trainer') . ' = ' . $db->quoteName('b.id') . ')')
            ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist1') . ' ON (' . $db->quoteName('a.assist1') . ' = ' . $db->quoteName('assist1.id') . ')')
            ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist2') . ' ON (' . $db->quoteName('a.assist2') . ' = ' . $db->quoteName('assist2.id') . ')')
            ->join('LEFT', $db->quoteName('#__tkdclub_members', 'assist3') . ' ON (' . $db->quoteName('a.assist3') . ' = ' . $db->quoteName('assist3.id') . ')');

        // Filter by year
        $yearselect = $this->getState('filter.year');
        if (!empty($yearselect))
        {
            $query->where('YEAR(date) = :yearselect')
                ->bind(':yearselect', $yearselect, ParameterType::INTEGER);
        }

        // Filter by trainer
        $trainerselect = $this->getState('filter.trainer');
        if (!empty($trainerselect)) {
            $query
                ->where($db->quoteName('a.trainer') . ' = :trainer', 'OR')
                ->where($db->quoteName('a.assist1') . ' = :assist1', 'OR')  
                ->where($db->quoteName('a.assist2') . ' = :assist2', 'OR') 
                ->where($db->quoteName('a.assist3') . ' = :assist3', 'OR')
                ->bind([':trainer', ':assist1', ':assist2', ':assist3'], $trainerselect, ParameterType::INTEGER);
        }

        // Filter by type
        $typeselect = $this->getState('filter.type');
        if (!empty($typeselect))
        {
            $query->where($db->quoteName('type') . ' = :type')
                    ->bind(':type', $typeselect);
        }

        // Filter by payment state
        $stateselect = $this->getState('filter.payment_state');
        if (is_numeric($stateselect))
        {
            if ($stateselect == 0)
            {
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

        // Filter by search in different fields.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0) 
            {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            }
            else
            {   
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->where(
                    '(' . $db->quoteName('a.id') . ' LIKE :search1'
                        . ' OR ' . $db->quoteName('a.date') . ' LIKE :search2' .
                    ')'
                )
                    ->bind([':search1', ':search2'], $search);
            }
        }
        
        // Join over the users for the checked out user.
        $query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=a.checked_out');

        $sort = $this->getState('list.ordering', 'date');
        $order = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($sort) . ' ' . $db->escape($order));

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
        if (!$this->trainer_names) {
            return false;
        }

        // Loop through the trainer names and get their data
        foreach ($this->trainer_names as $container) {
            $trainer_id = $container->id;
            $trainer_name = $container->firstname . ' ' . $container->lastname;

            // Initialise the container and some variables
            $trainer = new \stdClass;
            $trainer->trainer_id   = $trainer_id;
            $trainer->trainer_name = $trainer_name;
            $trainer->sex = $container->sex;
            $year_data = array();
            $sum_data = array(
                'trainings' => 0,
                'trainer' => 0,
                'assistent' => 0,
                'unpaid' => 0,
                'unpaid_sum' => 0,
                'types' => array()
            );

            // Get trainingsdata for every year from database and analyse it
            foreach ($this->training_years as $year) {
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
        if (!$this->training_years) {
            return false;
        }

        // Initialise the container and some variables
        $trainingsdata = new \stdClass;
        $sum_data = array('trainings' => 0, 'average' => 0, 'types' => array(), 'parts' => array());
        $sum_parts = 0; // collecting sum of participants for every year

        // Get trainingsdata for every year from database
        foreach ($this->training_years as $year) {
            $i = 0; // count variable to make sure the overall participants are not count twice or more each year
            $data = $this->get_trainings_from_db(null, $year);

            $year_data = array();
            $sum_parts_year = 0;
            $year_data['trainings'] = count($data);
            $sum_data['trainings'] += $year_data['trainings'];

            $year_data['types'] = array_count_values(array_column($data, 'type'));
            $sum_data['types'] = $this->sum_up($year_data['types'], $sum_data['types']);

            // loop through the training types and calculate the average participants
            foreach ($this->training_types as $type) {
                $parts = 0;
                !isset($sum_parts_overall[$type]) ? $sum_parts_overall[$type] = 0 : null; // collecting sum of participants for all types overall all traininigs

                foreach ($data as $item) {
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
        foreach ($sum_data['types'] as $key => $value) {
            $sum_parts_overall[$key] = round($sum_parts_overall[$key] / $value, 1);
        }

        $sum_data['parts'] = $sum_parts_overall;
        $sum_data['average'] = round($sum_parts / $sum_data['trainings'], 1);
        $trainingsdata->sums = $sum_data;

        $data = $this->prepareTrainingYearsChart($trainingsdata);
        $data = $this->prepareParticipantsYearChart($data);
        $data->currency = ComponentHelper::getParams('com_tkdclub')->get('currency', '€');

        return $data;
    }

    /**
     * Prepare data for trainings per year chart
     */
    public function prepareTrainingYearsChart(&$data)
    {
        $chartdata = array();
        $traintypes = array();

        $chartdata[0][] = Text::_('COM_TKDCLUB_YEAR');

        // First find all training - types in dataset
        foreach ($data->sums['types'] as $type => $value) {
            $chartdata[0][] = $type;
            $traintypes[] = $type;
        }

        foreach ($data as $year => $value) {
            $i = count($chartdata);
            if (is_numeric($year)) {
                $chartdata[$i][] = $year;
                foreach ($traintypes as $traintype) {
                    if (array_key_exists($traintype, $value['types'])) {
                        $chartdata[$i][] = $value['types'][$traintype];
                    } else {
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

        foreach ($data as $year => $value) {
            $j = count($chartdata);
            if (is_numeric($year)) {
                $chartdata[$j][] = $year;
                foreach ($this->training_types as $type) {
                    if (array_key_exists($type, $data->{$year}['parts'])) {
                        $chartdata[$j][] = $data->{$year}['parts'][$type];
                    } else {
                        $chartdata[$j][] = 0;
                    }
                }
            }
        }

        $data->ParticipantsYearChart = $chartdata;
        return $data;
    }

    /**
     * Sort function for trainings
     **/
    function sortTrainers($a, $b)
    {
        if ($a->sums['trainings'] == $b->sums['trainings'])
        {
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
        $db = Factory::getDbo();
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
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__tkdclub_trainings'));

        if ($trainer_id) {
            $query->where('(' . $db->quoteName('trainer') . ' = ' .  $trainer_id
                . ' OR ' . $db->quoteName('assist1') . ' = ' .  $trainer_id
                . ' OR ' . $db->quoteName('assist2') . ' = ' .  $trainer_id
                . ' OR ' . $db->quoteName('assist3') . ' = ' .  $trainer_id . ')');
        }

        if ($year) {
            $query->where($db->quoteName('date') . ' LIKE ' . $db->quote('%' . $year . '%'));
        }

        $db->setQuery($query);

        return $db->loadAssoclist();
    }

    /**
     * Get all the years in which a training was held
     *
     * This function gets all years in 4 digit form (2017) from database
     * It serves as a list for a database-query
     *
     * @return array
     **/
    public function get_all_training_years_from_database()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT substring(date,1,4)');
        $query->from($db->quoteName('#__tkdclub_trainings'));
        $query->order($db->quoteName('date') . 'ASC');

        $db->setQuery($query);

        return $db->loadColumn();
    }

    /**
     * Analyses the trainer data  
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
            if ($value['trainer'] == $trainer_id)
            {
                $trainer++;
                isset($types[$value['type']]['trainer']) ? $types[$value['type']]['trainer']++ : $types[$value['type']]['trainer'] = 1;
                if ($value['trainer_paid'] == 0) {
                    $unpaid_trainer++;
                    $unpaid_km += $value['km_trainer'];
                }
            } 
            else
            {
                $assist++;
                isset($types[$value['type']]['assistent']) ? $types[$value['type']]['assistent']++ : $types[$value['type']]['assistent'] = 1;

                if ($value['assist1'] == $trainer_id && $value['assist1_paid'] == 0) {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist1'];
                } elseif ($value['assist2'] == $trainer_id && $value['assist2_paid'] == 0) {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist2'];
                } elseif ($value['assist3'] == $trainer_id && $value['assist3_paid'] == 0) {
                    $unpaid_assist++;
                    $unpaid_km += $value['km_assist3'];
                }
            }
        }

        $container = array(
            'trainings' => $trainings, 'trainer' => $trainer, 'assistent' => $assist,
            'unpaid' => $unpaid_trainer + $unpaid_assist,
            'unpaid_sum' => $unpaid_km * $distance_rate +
                $unpaid_trainer * $training_salary +
                $unpaid_assist * $assist_salary
        );


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

        foreach ($year_data['types'] as $type => $value)
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
        $memberlist = TkdclubHelper::getMemberlist();
        $db    = $this->getDbo();
        $query    = $db->getQuery(true);
        $fields = array(
            'id',           // 0
            'date',         // 1
            'trainer',      // 2
            'km_trainer',   // 3
            'trainer_paid', // 4
            'assist1',      // 5
            'km_assist1',   // 6
            'assist1_paid', // 7
            'assist2',      // 8
            'km_assist2',   // 9
            'assist2_paid', // 10
            'assist3',      // 11
            'km_assist3',   // 12
            'assist3_paid', // 13
            'type',         // 14
            'participants'  // 15
        );
        $pks = ArrayHelper::toInteger($pks);
        $query->select($db->quoteName($fields))
            ->from($db->quoteName('#__tkdclub_trainings'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }
       
        $query->order('id DESC');

        $db->setQuery($query);
        $rows = $db->loadRowList();

        $headers = array(
            Text::_('COM_TKDCLUB_TRAINING_ID'),                // id
            Text::_('COM_TKDCLUB_DATE'),                       // date
            Text::_('COM_TKDCLUB_TRAINING_TRAINER'),           // trainer
            Text::_('COM_TKDCLUB_TRAINING_TRAINER_KM'),        // km_trainer
            Text::_('COM_TKDCLUB_TRAINING_TRAINER_PAID'),      // trainer_paid
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT1'),        // assist1
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT1_KM'),     // km_assist1
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT1_PAID'),   // assist1_paid      
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT2'),        // assist2
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT2_KM'),     // km_assist2
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT2_PAID'),   // assist2_paid    
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT3'),        // assist3
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT3_KM'),     // km_assist2
            Text::_('COM_TKDCLUB_TRAINING_ASSISTENT3_PAID'),   // assist3_paid 
            Text::_('COM_TKDCLUB_TRAINING_TYPE'),              // type
            Text::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'),      // participants
        );

        foreach ($rows as &$row)
		{
            // Set all 0 and null fields to empty string
            foreach ($row as &$field)
            {
                !$field ? $field = '' : null; 
            }

            // Prepare trainer fields
            if ($row[2])
            {
                $row[2] = TkdclubHelper::getMembersNames($row[2], $memberlist);
                $row[4] == 1 ? $row[4] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[4] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
            }

            // Prepare assistent 1 fields
            if ($row[5] > 0)
            {
                $row[5] =  TkdclubHelper::getMembersNames($row[5], $memberlist);
                $row[7] == 1 ? $row[7] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[7] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
            }

            // Prepare assistent 2 fields
            if ($row[8] > 0)
            {
                $row[8] = TkdclubHelper::getMembersNames($row[8], $memberlist);
                $row[10] == 1 ? $row[10] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[10] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
            }

            // Prepare assistent 3 fields
            if ($row[11] > 0)
            {
                $row[11] = TkdclubHelper::getMembersNames($row[11], $memberlist);
                $row[13] == 1 ? $row[13] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[13] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
            }
        }

        // Return the results as an array of items, each consisting of an array of fields
        $content    = array($headers);    // Header with column names
        $content    = array_merge($content,  $rows);
        return $content;
    }
}
