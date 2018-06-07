<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdClubHelperAge', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/agecalc.php');

class TkdClubControllerCandidate extends JControllerForm
{
    protected $text_prefix = 'COM_TKDCLUB_CANDIDATE';

    /**
     * The grades with a certain value, makes it easier to calculate
     * 
     * @var array
     */
    protected $grades;
    
    /**
     * The waiting time in days for a certain grade
     * 
     * @var integer
     */
    protected $waiting_time;

    /**
     * The minimum age for a certain grade in years
     * 
     * @var integer
     */
    protected $minimum_age;

    /**
     * Overriding constructor
     */
    public function __construct($config = array())
    {
        $this->grades = array(
            '10. Kup' => 1, '9. Kup' => 2, '8. Kup' => 3, '7. Kup' => 4, '6. Kup' => 5, '5. Kup' => 6, '4. Kup' => 7,
            '3. Kup' => 8, '2. Kup' => 9, '1. Kup' => 10, '1. Poom' => 11, '2. Poom' => 12, '3. Poom' => 13, '4. Poom' => 14,
            '1. Dan' => 11, '2. Dan' => 12, '3. Dan' => 13, '4. Dan' => 14, '5. Dan' => 15, '6. Dan' => 16, '7. Dan' => 17,
        );

        $this->waiting_time = array(
            '10. Kup' => 56, '9. Kup' => 56, '8. Kup' => 84, '7. Kup' => 84, '6. Kup' => 112, '5. Kup' => 112, '4. Kup' => 140,
            '3. Kup' => 140, '2. Kup' => 168, '1. Kup' => 168, '1. Poom' => 252, '2. Poom' => 728, '3. Poom' => 1092, '4. Poom' => 1092,
            '1. Dan' => 182, '2. Dan' => 364, '3. Dan' => 728, '4. Dan' => 1092, '5. Dan' => 1456, '6. Dan' => 1820, '7. Dan' => 2184
        );

        $this->minimum_age = array(
            '1. Poom' => 6, '2. Poom' => 8, '3. Poom' => 11, '4. Poom' => 14,
            '1. Dan' => 15, '2. Dan' => 16, '3. Dan' => 18, '4. Dan' => 21,
            '5. Dan' => 25, '6. Dan' => 30, '7. Dan' => 36
        );

        parent::__construct($config = array());
    }

    /**
     * Add a new candidate
     * 
     * Overriding the add-method for checking if a promotion is published
     * and message to the user if not
     */
    public function add() 
    {
        // check for published promotions
        $model =  $this->getModel(); 
        $exams =  $model->checkPromotionsPublished();
                
        if (!$exams)   
        {
            $this->setError(JText::_('COM_TKDCLUB_CANDIDATE_NO_PUPLISHED_PROMOTIONS'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
        }
        
        parent::add();
    }
    
    /**
     * Gets the right input data for adding a new candidate
     * 
     * This method is triggered when a new candidate is selected in the form
     * 
     * @return  json    the data for input or error-data
     * @todo   checks for age if candidate has poom and goes for dan
     */
    public function getAjaxData()
    {
        $input = JFactory::getApplication()->input;
        $candidate_id = JFactory::getApplication()->input->get('candidate_id', '', 'int');
        $promotion_id = JFactory::getApplication()->input->get('promotion_id', '', 'int');
        $this->app = JFactory::getApplication();
        
        // check if there are Ids in the request
        $checkIDs = $this->checkIDs($candidate_id, $promotion_id);
        !empty($checkIDs) ? $this->response($checkIDs) : null;
        
        // IDs ok, get Model and fetch data
        $model = $this->getModel($name = 'candidate', $prefix = 'TkdClubModel', $config = array());
        $candidate_data = $model->getCandidateData($candidate_id, $promotion_id);
        
        // Check for some missing candidates-data for finding proper grade to achieve
        $checkData = $this->checkData($candidate_data);
        !empty($checkData) ? $this->response($checkData) : null;

        // Check for double promotions and already assigned promotions
        $checkForDouble = $this->checkForDouble();
        !empty($checkForDouble) ? $this->response($checkForDouble) : null;

        // check for minimum age and waiting time
        $this->age = (int) TkdClubHelperAge::getAgetoDate($this->promotion_date, $this->birthdate);
        $this->changeGrades();

        // calculate grade value
        if (!$this->double)
        {
            $this->grade == '0' ? $grade_achieve_value = 1 : $grade_achieve_value = $this->grades[$candidate_data['grade']] + 1;
        }
        else
        {
            $grade_achieve_value = $this->grades[$this->double] + 1;
        }

        // Find the correct grade for the grade value
        $grade_achieve = array_search($grade_achieve_value, $this->grades);

        // Check if the correct promotion type is selected
        $checkType = $this->checkType($grade_achieve_value);
        !empty($checkType) ? $this->response($checkType) : null;
        
        $checkAgeAndWaitingtime = $this->checkAgeAndWaitingtime($grade_achieve_value, $grade_achieve);
        !empty($checkAgeAndWaitingtime) ? $this->response($checkAgeAndWaitingtime) : null;

        // everything is fine, give back the grade achieve and Information about double promotion
        $candidate_data['grade_achieve'] = array_search($grade_achieve_value, $this->grades);

        if ($candidate_data['lastpromotion'] == '0000-00-00')
        {
            $candidate_data['lastpromotion'] = '';
            $candidate_data['grade'] = JText::_('COM_TKDCLUB_NO_GRADE_LISTVIEW');
        } else
        {
            $candidate_data['lastpromotion'] = JHtml::_('date', $candidate_data['lastpromotion'], JText::_('DATE_FORMAT_LC4'));
        }

        if ($this->double)
        {
            $candidate_data['notes'] = JText::_('COM_TKDCLUB_CANDIDATE_DOUBLE_PROMOTION');
        }
        
        $candidate_data['no_error'] = true;
        $this->response($candidate_data);
    }

    /**
     * Checks if there are ids in the request
     * 
     * This method checks if there are ids in the request.
     * If not, a array with error messages is created
     * 
     * @param   int     $candidate_id   integer value of member_id
     * @param   int     $promotion_id   integer value of promotion_id
     * 
     * @return  array   an array wirh messages when there are no ids, otherwise empty array
     */
    protected function checkIDs($candidate_id, $promotion_id)
    {   
        $errors = array();
        
        $candidate_id == '' ? $errors['error_candidate_id'] = JText::_('COM_TKDCLUB_CANDIDATE_MISSING_CANDIDATE_ID') : null;
        $promotion_id == '' ? $errors['error_promotion_id'] = JText::_('COM_TKDCLUB_CANDIDATE_MISSING_PROMOTION_ID') : null;

        return $errors;
    }

    /**
     * Checks the data in the database of a candidate
     * 
     * This method checks the data in the database of a candidate for necassary data
     * If some data is not present, appropriate error messages are created
     * 
     * @param   array   $candidate_data     array with the candidate data from database
     * 
     * @return  mixed   nothing if all data is there
     *                  array of messages and edit link in member edit view
     */
    protected function checkData($candidate_data)
    {
        $errors = array();

        // writing data to class variables
        $this->birthdate = $candidate_data['birthdate'];
        $this->lastpromotion = $candidate_data['lastpromotion'];
        $this->grade = $candidate_data['grade'];
        $this->entry = $candidate_data['entry'];
        $this->member_id = $candidate_data['member_id'];
        $this->name = $candidate_data['firstname'] . ' ' . $candidate_data['lastname'];
        $this->promotion_date = $candidate_data['promotion_date'];
        $this->promotion_type = $candidate_data['promotion_type'];
        $this->double = isset($candidate_data['double']) ? $candidate_data['double'] : null;
        $this->second_promotion = isset($candidate_data['second_promotion']) ? $candidate_data['second_promotion'] : null;

        // missing birthdate
        $this->birthdate == '0000-00-00' ? $errors['error_birthdate'] = '- ' . JText::_('COM_TKDCLUB_MEMBER_BIRTHDATE') : null;

        // missing last promotion date when grade is set
        if ($this->lastpromotion == '0000-00-00' && $this->grade != '0')
        {
            $errors['error_lastpromotion'] = '- ' . JText::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION');
        }

        // missing grade when last promotion date is set
        if ($this->lastpromotion != '0000-00-00' && $this->grade == '0')
        {
            $errors['error_grade'] = '- ' . JText::_('COM_TKDCLUB_MEMBER_GRADE');
        }

        // missing entry date when no grade is set
        if ($this->entry == '0000-00-00' && $this->grade == '0')
        {
            $errors['error_entry'] = '- ' . JText::_('COM_TKDCLUB_MEMBER_ENTRY');
        }

        // when there are errors, create a link for editing the missing data in member view
        if(!empty($errors))
        {
            $errors['error'] = '<b>' . JText::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->name . '</b>'
                                . '<br>'
                                . JText::_('COM_TKDCLUB_CANDIDATE_MISSING_DATA');
            $edit_link = JRoute::_("index.php?option=com_tkdclub&task=member.edit&member_id=" . $this->member_id);
            $errors['edit_link'] = '<span class="icon-edit"> </span>'
                                    . '<a href="'.$edit_link.'">' 
                                    . $this->name . JText::_('COM_TKDCLUB_CANDIDATE_EDITING_IN_DATABASE') 
                                    . '</a>';
        } 

        return $errors;
    }

    /**
     * Checks for errors for double promotions or if another promotion is already set
     */
    protected function checkForDouble()
    {
        $errors = array();

        if ($this->double == 'error_already_double')
        {
            $errors[$this->double] = '- ' . JText::_('COM_TKDCLUB_CANDIDATE_ALREADY_DOUBLE_PROMOTION');
        }

        if ($this->second_promotion)
        {
            $errors['second_promotion'] = '- ' . JText::_('COM_TKDCLUB_CANDIDATE_ALREADY_PROMOTION_ASSIGNED');
        }

        if (!empty($errors))
        {  
            $errors['error'] = '<b>' . JText::_('COM_TKDCLUB_CANDIDATE_ERROR') .  $this->name . '</b>';
        }

        return $errors;
    }

    /**
     * Change grades for candidates < 15 years
     */
    protected function changeGrades()
    {
        if ($this->age < 15)
        {
            unset($this->grades['1. Dan']);
            unset($this->grades['2. Dan']);
            unset($this->grades['3. Dan']);
            unset($this->grades['4. Dan']);
            unset($this->grades['5. Dan']);
            unset($this->grades['6. Dan']);
            unset($this->grades['7. Dan']);
        }
        else
        {
            unset($this->grades['1. Poom']);
            unset($this->grades['2. Poom']);
            unset($this->grades['3. Poom']);
            unset($this->grades['4. Poom']);
        }
    }

    /**
     * Check if minimum age and waiting time for a grade is reached
     * 
     * @param   int     $grade_achieve_value    integer value representing the grade to achieve
     * @param   string  $grade_achieve          name of the grade to achieve
     * 
     * @return  mixed   nothing if minimum age and waiting time is ok
     *                  array with error messages
     */
    protected function checkAgeAndWaitingtime($grade_achieve_value, $grade_achieve)
    {
        $errors = array();

        // First check minimum age
        if (array_key_exists($grade_achieve, $this->minimum_age))
        {
            $this->age < $this->minimum_age[$grade_achieve] ? $errors['error_minage'] = '- ' . JText::_('COM_TKDCLUB_CANDIDATE_MINIMUM_AGE_NOT_ACHIEVED') : null;
        }

        // Now check the waiting time, for double promotion calculate as necessary
        $promotion = date_create($this->promotion_date);
        
        if (!$this->double) // No double promotion
        {
            $last = date_create($this->lastpromotion);
            $time = $this->waiting_time[$grade_achieve];
        }

        if ($this->double) // double promotion, figure out date to use and waiting time
        {
            $this->lastpromotion == '0000-00-00' ? $last = date_create($this->entry) : $last = date_create($this->lastpromotion);
            
            $time2 = $this->waiting_time[$grade_achieve]; // for 2. grade
            $grade1_val = $this->grades[$grade_achieve] - 1;
            $grade1 = array_search($grade1_val, $this->grades);
            $time1 = $this->waiting_time[$grade1];

            $time = $time1 + $time2;
        }

        $diff = date_diff($promotion, $last, true);

        if ($diff->days < $time)
        {
            $errors['error_waitingtime'] = '- ' . JText::_('COM_TKDCLUB_CANDIDATE_WAITING_TIME_NOT_ACHIEVED');
        }

        if (!empty($errors))
        {  
            $errors['error'] = '<b>' . JText::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->name . '</b>';
        }

        return $errors;
    }

    /**
     * Check for the right promotion
     * 
     * This method checks if the subscription to dan or kup promotion test is ok
     * 
     * @param   int     $grade_achieve_value    integer value representing the grade
     * 
     * @return  mixed   nothing if grade and promtion type are matching
     *                  array with error messages if not
     */
    protected function checkType($grade_achieve_value)
    {
        $errors = array();

        if ($grade_achieve_value > 10 && $this->promotion_type == 'kup' )
        {
            $errors['error_type'] = JText::_('COM_TKDCLUB_CANDIDATE_SUBSCRIBE_TO_DAN');
        }

        if ($grade_achieve_value < 11 && $this->promotion_type == 'dan' )
        {
            $errors['error_type'] = JText::_('COM_TKDCLUB_CANDIDATE_SUBSCRIBE_TO_KUP');
        }

        if (!empty($errors))
        {  
            $errors['error'] = '<b>' . JText::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->name . '</b>';
        }

        return $errors;
    }

    /**
     * Echo out data to the browser
     * 
     * @param   array   array with data of the candidate
     * @return  json    echo out data in json format
     */
    protected function response($data)
    {
        echo json_encode($data);
        $this->app->close();
    }
}