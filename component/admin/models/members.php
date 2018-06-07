<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdClubHelperAge', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/agecalc.php');

/**
 * Model-class for list view 'members'
 */
class TkdClubModelMembers extends JModelList
{
    /**
     * helper vars for grade filter
     */
    public  $students_grade = array(
    '10. Kup', '9. Kup', '8. Kup', '7. Kup', '6. Kup','5. Kup', '4. Kup', '3. Kup', '2. Kup', '1. Kup');


    public $masters_grade = array(
    '1. Poom', '2. Poom', '3. Poom',
    '1. Dan', '2. Dan', '3. Dan', '4. Dan', '5. Dan', '6. Dan', '7. Dan', '8. Dan', '9. Dan', '10. Dan');

    /**
	 * Constructor
	 */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = 
            array('member_id', 'lastname', 'firstname', 'citizenship', 'street', 
                    'zip', 'city', 'memberpass', 'grade', 'member_state');
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
    protected function populateState($ordering = 'm.member_id', $direction = 'ASC')
    {
        /* inputs for search bar filter */
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        /* inputs for grade filter */
        $grade = $this->getUserStateFromRequest($this->context.'.filter.grade', 'filter_grade', '');
        $this->setState('filter.grade', $grade);

        /* input for member_state filter, standard is active members */
        $state = $this->getUserStateFromRequest($this->context.'.filter.member_state', 'filter_member_state');
        if (!is_string($state))
        {
            $this->setState('filter.member_state', 'active');
            
        }
        elseif ($state == 'all')   
        {  
            $this->setState('filter.member_state', '');
            
        }
        else 
        {
            $this->setState('filter.member_state', $state);
        }

        // Load the parameters.
        $params = JComponentHelper::getParams('com_tkdclub');
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
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.grade');
        $id	.= ':'.$this->getState('filter.member_state');

        return parent::getStoreId($id);
    }

    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.0
	 */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('m.*')->from($db->quoteName('#__tkdclub_members') . ' as m');

        $gradeselect = $this->getState('filter.grade');
        switch ($gradeselect)
        {
            case '':
                break; // show all members

            case '0':
                $query->where('m.grade = ' . (int) $gradeselect); // empty or null field
                break;

            case 'students': // show only students
                $students_list = "'" . implode("','", $this->students_grade) . "'";
                $query->where('m.grade IN ('.$students_list.')');
                break;
            
            case 'masters': // show only masters
                $masters_list = "'" . implode("','", $this->masters_grade) . "'";
                $query->where('m.grade IN ('.$masters_list.')');
                break;

            default: // default behavier for grades
                $gs = $db->quote($db->escape($gradeselect, true));
                $query->where('m.grade = ' . $gs);
        }

        $stateselect = $this->getState('filter.member_state');
        if ($stateselect == 'active' || $stateselect == 'inactive' || $stateselect == 'support')
        {
            $ss = $db->quote($db->escape($stateselect, true));
            $query->where('m.member_state = ' .$ss);
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
                $search = $db->quote('%'. $db->escape($search, true).'%');
                $query->where('m.member_id LIKE' .$search
                            .'OR m.lastname LIKE' .$search
                            .'OR m.firstname LIKE' .$search
                            .'OR m.street LIKE' .$search
                            .'OR m.zip LIKE' .$search
                            .'OR m.city LIKE' .$search
                            .'OR m.memberpass LIKE' .$search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')
			->join('LEFT', '#__users AS u ON u.id=m.checked_out');

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

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
    * Method for getting inormation about members
    *
    * @param   string   $type      What to return.
    *
    * @return  mixed  an array on success, false on failure
    *
    * @since   2.2.0
    */
    public function getMemberdata($json = false)
    {   
        //initilise some variables
        $memberdata = new stdClass;
        $active = 0;
        $inactive = 0;
        $support = 0;
        $agedistribution = array
        (
            '0-9' => 0, '10-19' => 0, '20-29' => 0, '30-39' => 0,
            '40-49' => 0, '50-59' => 0, '60-69' => 0, '+70' => 0, 
        );
        $agesum = 0;
        $genderdist = array('female' => 0, 'male' => 0);
        $now = date('Y-m-d');
        $oldest = array('name' => '', 'age_y' => 0, 'age_d' => 0);
        $youngest = array('name' => '', 'age_y' => 100, 'age_d' => 36500);

        //getting all data from memberdatabase
        $data = $this->getData('#__tkdclub_members', 'member_id');
        
        //all rows in members table
        $allrows = count($data);
        
        //getting Memberstates, age and gender distribution
        foreach ($data as $member)
        {
            $member->member_state == 'active' ? $active++ : '';          
            $member->member_state == 'inactive' ? $inactive++ : '';
            $member->member_state == 'support' ? $support++ : '';
            
            if ($member->member_state == 'active')
            {
                $age = TkdClubHelperAge::getAgetoDate($now, $member->birthdate);
                $age_in_days = TkdClubHelperAge::getAge($member->birthdate, 'days'); //important for youngest/oldest calculation
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
                    $youngest = array('age_y' => $age, 'name' => $member->firstname .' ' . $member->lastname, 'age_d' => $age_in_days);
                }
                elseif ($age_in_days > $oldest['age_d'])
                {
                    $oldest = array('age_y' => $age, 'name' => $member->firstname .' ' . $member->lastname, 'age_d' => $age_in_days);
                }
                
                //gender
                $member->sex == 'female' ? $genderdist['female']++ : $genderdist['male']++;

            }
        }

        $memberdata->allrows = $allrows;
        $memberdata->active = $active;
        $memberdata->inactive= $inactive;
        $memberdata->support = $support;
        $memberdata->agedist = $agedistribution;
        $memberdata->average_age = round($agesum/$active, 1);
        $memberdata->genderdist = $genderdist;
        $memberdata->oldest = $oldest;
        $memberdata->youngest = $youngest;
        
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
		$pklist = implode(',', $pks);

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
        $fields = array(
                        'member_id', 'firstname', 'lastname', 'birthdate', 'sex', 'citizenship', 'street', 'zip', 'city', 'country', 'phone', 'email',
                        'memberpass', 'grade', 'lastpromotion', 'member_state', 'entry', 'leave', 'created', 'created_by', 'modified', 'modified_by');
                        
		$query-> select($db->quoteName($fields))
			  -> from($db->quoteName('#__tkdclub_members'))
			  -> where('member_id IN ('.$pklist.')')
              ->order('member_id ASC');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        foreach ($rows as &$row)
        {
            $row[4] == 'female' ? $row[4] = JText::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') : $row[4] = JText::_('COM_TKDCLUB_MEMBER_SEX_MALE');
            $row[12] == 0 ? $row[12] = JText::_('COM_TKDCLUB_MEMBER_NO_PASS') : null;
            $row[13] == 0 ? $row[13] = JText::_('COM_TKDCLUB_NO_GRADE_LISTVIEW') : null;
            switch ($row[15])
            {
                case 'active':
                    $row[15] = JText::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE');
                    break;
                
                case 'support':
                    $row[15] = JText::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER');
                    break;
                
                case 'inactive':
                    $row[15] = JText::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE');
                    break;

                default:
                    $row[15] = $row[15];

            }

            $row[19] ? $row[19] = JFactory::getUser($row[19])->name : null;
            $row[21] ? $row[21] = JFactory::getUser($row[21])->name : null;

        }

        $headers = array(
            JText::_('COM_TKDCLUB_MEMBER_ID'),              // member_id
            JText::_('COM_TKDCLUB_MEMBER_FIRSTNAME'),       // firstname
            JText::_('COM_TKDCLUB_MEMBER_LASTNAME'),        // lastname
            JText::_('COM_TKDCLUB_MEMBER_BIRTHDATE'),       // birthdate
            JText::_('COM_TKDCLUB_MEMBER_SEX'),             // sex
            JText::_('COM_TKDCLUB_MEMBER_CITIZENSHIP'),     // citizenship       
            JText::_('COM_TKDCLUB_MEMBER_STREET'),          // street
            JText::_('COM_TKDCLUB_MEMBER_ZIP'),             // zip
            JText::_('COM_TKDCLUB_MEMBER_CITY'),            // city
            JText::_('COM_TKDCLUB_MEMBER_COUNTRY'),         // country
            JText::_('COM_TKDCLUB_MEMBER_PHONE'),           // phone
            JText::_('COM_TKDCLUB_MEMBER_EMAIL'),           // email
            JText::_('COM_TKDCLUB_MEMBER_PASS'),            // memberpass
            JText::_('COM_TKDCLUB_MEMBER_GRADE'),           // grade
            JText::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION'),  // lastexam
            JText::_('COM_TKDCLUB_MEMBER_STATE'),           // member_state
            JText::_('COM_TKDCLUB_MEMBER_ENTRY'),           // entry
            JText::_('COM_TKDCLUB_MEMBER_LEAVE'),           // leave
            JText::_('COM_TKDCLUB_FIELD_CREATED'),          // created
            JText::_('COM_TKDCLUB_FIELD_CREATED_BY'),       // created_by
            JText::_('COM_TKDCLUB_FIELD_MODIFIED'),         // modified
            JText::_('COM_TKDCLUB_FIELD_MODIFIED_BY')       // modified_by

        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
    
}