<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Model-class for list view members.
 */
class MembersModel extends ListModel
{
    /**
     * Helper var for grade filter
     */
    public  $student_grades = array(
        '10. Kup', '9. Kup', '8. Kup', '7. Kup', '6. Kup', '5. Kup', '4. Kup', '3. Kup', '2. Kup', '1. Kup'
    );

    /**
     * Helper var for grade filter
     */
    public $master_grades = array(
        '1. Poom', '2. Poom', '3. Poom',
        '1. Dan', '2. Dan', '3. Dan', '4. Dan', '5. Dan', '6. Dan', '7. Dan', '8. Dan', '9. Dan', '10. Dan'
    );

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
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] =
                array(
                    'id', 'lastname', 'firstname', 'citizenship', 'street',
                    'zip', 'city', 'memberpass', 'grade', 'member_state'
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
     * @since   1.0
     */
    protected function populateState($ordering = 'm.id', $direction = 'ASC')
    {
        /* inputs for search bar filter */
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        /* inputs for grade filter */
        $grade = $this->getUserStateFromRequest($this->context . '.filter.grade', 'filter_grade', '');
        $this->setState('filter.grade', $grade);

        /* input for member_state filter, standard is active members */
        $state = $this->getUserStateFromRequest($this->context . '.filter.member_state', 'filter_member_state');
        if (!is_string($state)) {
            $this->setState('filter.member_state', 'active');
        } elseif ($state == 'all') {
            $this->setState('filter.member_state', '');
        } else {
            $this->setState('filter.member_state', $state);
        }

        // Load the parameters.
        $params = ComponentHelper::getParams('com_tkdclub');
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
     * @since   1.0
     */
    protected function getStoreId($id = '')
    {
        $id    .= ':' . $this->getState('filter.search');
        $id    .= ':' . $this->getState('filter.grade');
        $id    .= ':' . $this->getState('filter.member_state');

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
        $query->select(
            [   
                $db->quoteName('m.id'),
                $db->quoteName('m.firstname'),
                $db->quoteName('m.lastname'),
                $db->quoteName('m.sex'),
                $db->quoteName('m.phone'),
                $db->quoteName('m.email'),
                $db->quoteName('m.memberpass'),
                $db->quoteName('m.grade'),
                $db->quoteName('m.lastpromotion'),
                $db->quoteName('m.iban'),
                $db->quoteName('m.member_state'),
                $db->quoteName('m.functions'),
                $db->quoteName('m.attachments'),
                $db->quoteName('m.image'),
                $db->quoteName('m.notes_personel'),
                $db->quoteName('m.notes_taekwondo'),
                $db->quoteName('m.notes_clubdata'),
                $db->quoteName('m.created'),
                $db->quoteName('m.created_by'),
                $db->quoteName('m.modified'),
                $db->quoteName('m.modified_by'),
                $db->quoteName('m.checked_out'),
                $db->quoteName('m.checked_out_time')
            ]
        )->from($db->quoteName('#__tkdclub_members', 'm'));
        
        // Filter by grade.
        $gradeselect = $this->getState('filter.grade');

        switch ($gradeselect) {
            case '':
                break; // show all members

            case '0':
                // Empty or null field
                $query->where($db->quoteName('m.grade') . ' = :gradeselect') 
                    ->bind(':gradeselect', $gradeselect, ParameterType::INTEGER); 
                break;

            case 'students':
                // Show only students
                $query->whereIn($db->quoteName('m.grade'), $this->student_grades, ParameterType::STRING);
                break;

            case 'masters':
                // Show only masters
                $query->whereIn($db->quoteName('m.grade'), $this->master_grades, ParameterType::STRING);
                break;

            default:
                // Default behavier for grades
                $query->where($db->quoteName('m.grade')  . ' = :gradeselect')
                    ->bind(':gradeselect', $gradeselect);
        }

        // Filter by state
        $stateselect = $this->getState('filter.member_state');

        if ($stateselect == 'active' || $stateselect == 'inactive' || $stateselect == 'support') {
            $query->where($db->quoteName('m.member_state') . ' = :stateselect')
                ->bind(':stateselect', $stateselect);
        }

        // Filter by search in different fields.
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
                        . ' OR ' . $db->quoteName('m.lastname') . ' LIKE :search2'
                        . ' OR ' . $db->quoteName('m.firstname') . ' LIKE :search3'
                        . ' OR ' . $db->quoteName('m.street') . ' LIKE :search4'
                        . ' OR ' . $db->quoteName('m.zip') . ' LIKE :search5'
                        . ' OR ' . $db->quoteName('m.city') . ' LIKE :search6'
                        . ' OR ' . $db->quoteName('m.memberpass') . ' LIKE :search7' .
                    ')'
                )
                    ->bind([':search1', ':search2', ':search3', ':search4', ':search5', ':search6', ':search7'], $search);
            }
        }

        // Join over the users for the checked out user.
        $query->select($db->quoteName('u.name') . ' AS  editor')
                ->join('LEFT', $db->quoteName('#__users') . 'AS u ON u.id = m.checked_out');

        // Add the list ordering clause.
        $sort = $this->getState('list.ordering', 'id');
        $order = $this->getState('list.direction', 'ASC');
        
        $query->order($db->escape($sort) . ' ' . $db->escape($order));
        
        return $query;
    }

    /**
     * Method to get the number of all entries in the members-table
     * 
     * @return type integer 
     */
    public function getAllRows()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__tkdclub_members'));

        $db->setQuery($query);
        $allrows = (int) $db->loadResult();

        return $allrows;
    }

    /**
     * Method for getting basic statistic information about members
     *
     * @param   string   $type      What to return.
     *
     * @return  mixed  an array on success, false on failure
     *
     * @since   2.2.0
     */
    public function getMemberdata($json = false)
    {
        // Getting all data from memberdatabase
        $data = $this->getData('#__tkdclub_members', 'id');

        if (empty($data)) {
            return false;
        }

        // Initilise some variables
        $memberdata = new \stdClass;
        $active = 0;
        $inactive = 0;
        $support = 0;
        $agedistribution = array(
            '0-9' => 0, '10-19' => 0, '20-29' => 0, '30-39' => 0,
            '40-49' => 0, '50-59' => 0, '60-69' => 0, '+70' => 0,
        );
        $agesum = 0;
        $genderdist = array('female' => 0, 'male' => 0);
        $now = date('Y-m-d');
        $oldest = array('name' => '', 'age_y' => 0, 'age_d' => 0);
        $youngest = array('name' => '', 'age_y' => 100, 'age_d' => 36500);

        // All rows in members table
        $allrows = count($data);

        // Getting Memberstates, age and gender distribution
        foreach ($data as $member) {
            $member->member_state == 'active' ? $active++ : '';
            $member->member_state == 'inactive' ? $inactive++ : '';
            $member->member_state == 'support' ? $support++ : '';

            if ($member->member_state == 'active') {
                $age = (int) TkdclubHelper::getAgetoDate($now, $member->birthdate);
                $age_in_days = TkdclubHelper::getAge($member->birthdate, 'days'); //important for youngest/oldest calculation
                $age <= 9 ? $agedistribution['0-9'] += 1 : ''; //0-9 years
                $age >= 10 && $age <= 19 ? $agedistribution['10-19'] += 1 : ''; //10-19 years
                $age >= 20 && $age <= 29 ? $agedistribution['20-29'] += 1 : ''; //20-29 years
                $age >= 30 && $age <= 39 ? $agedistribution['30-39'] += 1 : ''; //30-39 years
                $age >= 40 && $age <= 49 ? $agedistribution['40-49'] += 1 : ''; //40-49 years
                $age >= 50 && $age <= 59 ? $agedistribution['50-59'] += 1 : ''; //50-59 years
                $age >= 60 && $age <= 69 ? $agedistribution['60-69'] += 1 : ''; //60-69 years
                $age >= 70 ? $agedistribution['+70'] += 1 : ''; //more than 70 years

                $agesum += $age; //cumulation age for average calculation

                //oldest and youngest
                if ($age_in_days < $youngest['age_d'])
                {
                    $youngest = array('age_y' => $age, 'name' => $member->firstname . ' ' . $member->lastname, 'age_d' => $age_in_days);
                }

                if ($age_in_days > $oldest['age_d'])
                {
                    $oldest = array('age_y' => $age, 'name' => $member->firstname . ' ' . $member->lastname, 'age_d' => $age_in_days);
                }

                //gender
                $member->sex == 'female' ? $genderdist['female']++ : $genderdist['male']++;
            }
        }

        $memberdata->allrows     = $allrows;
        $memberdata->active      = $active;
        $memberdata->inactive    = $inactive;
        $memberdata->support     = $support;
        $memberdata->agedist     = $agedistribution;
        $memberdata->average_age = round($agesum / $active, 1);
        $memberdata->genderdist  = $genderdist;
        $memberdata->oldest      = $oldest;
        $memberdata->youngest    = $youngest;

        return $json ? json_encode($memberdata) : $memberdata;
    }

    /**
     * Method for getting all data from database table
     *
     * @param   string   $database      the database.
     * @param   string   $ordering      the ordering column.
     *
     * @return  mixed  A Jobject on success, false on failure
     *
     * @since   2.2.0
     */
    protected function getData($database, $ordering)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName($database))
            ->order($db->quoteName($ordering) . ' ASC');;
        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }

    /**
     * Method to get the data that should be exported.
     * @return  mixed  The data.
     */
    public function getExportData($pks)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            'id',     // 0
            'firstname',     // 1
            'lastname',      // 2
            'birthdate',     // 3
            'sex',           // 4
            'citizenship',   // 5
            'street',        // 6
            'zip',           // 7
            'city',          // 8
            'country',       // 9
            'phone',         // 10
            'email',         // 11
            'iban',          // 12
            'memberpass',    // 13
            'grade',         // 14
            'lastpromotion', // 15
            'member_state',  // 16
            'entry',         // 17
            'leave',         // 18
            'created',       // 19
            'created_by',    // 20
            'modified',      // 21
            'modified_by'    // 22
        );

        $pks = ArrayHelper::toInteger($pks);
        $query->select($db->quoteName($fields))->from($db->quoteName('#__tkdclub_members'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }
            
        $query->order('id ASC');

        $db->setQuery($query);
        $rows = $db->loadRowList();

        foreach ($rows as &$row)
        {
            $row[4]  == 'female' ? $row[4] = Text::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') : $row[4] = Text::_('COM_TKDCLUB_MEMBER_SEX_MALE');

            switch ($row[16]) {
                case 'active':
                    $row[16] = Text::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE');
                    break;

                case 'support':
                    $row[16] = Text::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER');
                    break;

                case 'inactive':
                    $row[16] = Text::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE');
                    break;

                default:
                    $row[16] = '';
            }

            $row[20] ? $row[20] = Factory::getUser($row[20])->name : '';
            $row[22] ? $row[22] = Factory::getUser($row[22])->name : '';
        }

        $headers = array(
            Text::_('COM_TKDCLUB_MEMBER_ID'),              // id
            Text::_('COM_TKDCLUB_MEMBER_FIRSTNAME'),       // firstname
            Text::_('COM_TKDCLUB_MEMBER_LASTNAME'),        // lastname
            Text::_('COM_TKDCLUB_MEMBER_BIRTHDATE'),       // birthdate
            Text::_('COM_TKDCLUB_MEMBER_SEX'),             // sex
            Text::_('COM_TKDCLUB_MEMBER_CITIZENSHIP'),     // citizenship       
            Text::_('COM_TKDCLUB_MEMBER_STREET'),          // street
            Text::_('COM_TKDCLUB_MEMBER_ZIP'),             // zip
            Text::_('COM_TKDCLUB_MEMBER_CITY'),            // city
            Text::_('COM_TKDCLUB_MEMBER_COUNTRY'),         // country
            Text::_('COM_TKDCLUB_MEMBER_PHONE'),           // phone
            Text::_('COM_TKDCLUB_MEMBER_EMAIL'),           // email
            Text::_('COM_TKDCLUB_MEMBER_IBAN'),            // iban
            Text::_('COM_TKDCLUB_MEMBER_PASS'),            // memberpass
            Text::_('COM_TKDCLUB_MEMBER_GRADE'),           // grade
            Text::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION'),  // lastexam
            Text::_('COM_TKDCLUB_MEMBER_STATE'),           // member_state
            Text::_('COM_TKDCLUB_MEMBER_ENTRY'),           // entry
            Text::_('COM_TKDCLUB_MEMBER_LEAVE'),           // leave
            Text::_('COM_TKDCLUB_FIELD_CREATED'),          // created
            Text::_('COM_TKDCLUB_FIELD_CREATED_BY'),       // created_by
            Text::_('COM_TKDCLUB_FIELD_MODIFIED'),         // modified
            Text::_('COM_TKDCLUB_FIELD_MODIFIED_BY')       // modified_by

        );

        // Return the results as an array of items, each consisting of an array of fields
        $content    = array($headers);
        $content    = array_merge($content,  $rows);
        
        return $content;
    }
}
