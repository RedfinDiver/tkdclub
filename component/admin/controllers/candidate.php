<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdClubHelperAge', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/agecalc.php');

class TkdClubControllerCandidate extends JControllerForm
{
    /**
     * the grades with a certain value, makes it easier to calculate
     * 
     * @var array
     */
    protected $grades;
    
    /**
     * the waiting time in days for a certain grade
     * 
     * @var integer
     */
    protected $waiting_time;

    /**
     * the minimum age for a certain grade in years
     * 
     * @var integer
     */
    protected $minimum_age;

    /**
     * overriding constructor
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

    public function add() 
    {
        // check for published promotions
        $model =  $this->getModel(); 
        $exams =  $model->checkExamsPublished();
                
        if (!$exams)   
        {
            $this->setError(JText::_('COM_TKDCLUB_EXAMS_ERROR_NOEXAM_PUBLISHED'));
			$this->setMessage($this->getError(), 'message');

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
    
    public function getAjaxData ()
    {
        $input = JFactory::getApplication()->input;
        $candidate_id = JFactory::getApplication()->input->get('candidate_id', '', 'int');
        $promotion_id = JFactory::getApplication()->input->get('promotion_id', '', 'int');
        $this->app = JFactory::getApplication();
        
        // check if there are Ids in the request
        $checkIDs = $this->checkIDs($candidate_id, $promotion_id);
        !empty($checkIDs) ? $this->response($checkIDs) : null;
        
        // take present ids, get Model and fetch data
        $model = $this->getModel($name = 'candidate', $prefix = 'TkdClubModel', $config = array());
        $candidate_data = $model->getCandidateData($candidate_id);
        
        // check for some missing candidates-data for finding proper grade to achieve
        $checkData = $this->checkData($candidate_data);
        !empty($checkData) ? $this->response($checkData) : null;

        // check for minimum age and waiting time
        $this->promotion_date = $model->getPromotionDate($promotion_id);
        $this->age = (int) TkdClubHelperAge::getAgetoDate($this->promotion_date, $this->birthdate);
        $this->changeGrades();

        // calculate grade value
        $this->grade == '0' ? $grade_achieve_value = 1 : $grade_achieve_value = $this->grades[$candidate_data['grade']] + 1;
        $grade_achieve = array_search($grade_achieve_value, $this->grades);
        
        $checkAgeAndWaitingtime = $this->checkAgeAndWaitingtime($grade_achieve_value, $grade_achieve);
        !empty($checkAgeAndWaitingtime) ? $this->response($checkAgeAndWaitingtime) : null;

        // everything is fine, give back the grade achieve
        $candidate_data['grade_achieve'] = array_search($grade_achieve_value, $this->grades);

        if ($candidate_data['lastpromotion'] == '0000-00-00')
        {
            $candidate_data['lastpromotion'] = '';
            $candidate_data['grade'] = JText::_('COM_TKDCLUB_NO_GRADE_LISTVIEW');
        } else
        {
            $candidate_data['lastpromotion'] = JHtml::_('date', $candidate_data['lastpromotion'], JText::_('DATE_FORMAT_LC4'));
        }
        
        $candidate_data['no_error'] = true;
        $this->response($candidate_data);
    }

    public function checkIDs ($candidate_id, $promotion_id)
    {   
        $errors = array();
        
        $candidate_id == '' ? $errors['error_candidate_id'] = JText::_('COM_TKDCLUB_CANDIDATE_MISSING_CANDIDATE_ID') : null;
        $promotion_id == '' ? $errors['error_promotion_id'] = JText::_('COM_TKDCLUB_CANDIDATE_MISSING_PROMOTION_ID') : null;

        return $errors;
    }

    public function checkData ($candidate_data)
    {
        $errors = array();
        $this->birthdate = $candidate_data['birthdate'];
        $this->lastpromotion = $candidate_data['lastpromotion'];
        $this->grade = $candidate_data['grade'];
        $this->entry = $candidate_data['entry'];
        $this->member_id = $candidate_data['member_id'];
        $this->name = $candidate_data['firstname'] . ' ' . $candidate_data['lastname'];

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
            $errors['error'] = JText::_('COM_TKDCLUB_CANDIDATE_MISSING_DATA');
            $edit_link = JRoute::_("index.php?option=com_tkdclub&task=member.edit&member_id=" . $this->member_id);
            $errors['edit_link'] = '<span class="icon-edit"> </span>'
                                    . '<a href="'.$edit_link.'">' 
                                    . $this->name . JText::_('COM_TKDCLUB_CANDIDATE_EDITING_IN_DATABASE') 
                                    . '</a>';
        } 

        return $errors;
    }

    /**
     * change grades for candidates < 15 years
     */
    public function changeGrades()
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

    public function checkAgeAndWaitingtime($grade_achieve_value, $grade_achieve)
    {
        $errors = array();

        // first check minimum age
        if (array_key_exists($grade_achieve, $this->minimum_age))
        {
            $this->age < $this->minimum_age[$grade_achieve] ? $errors['error_minage'] = JText::_('COM_TKDCLUB_CANDIDATE_MINIMUM_AGE_NOT_ACHIEVED') : null;
        }

        // now check the waiting time
        $promotion = date_create($this->promotion_date);
        $last = date_create($this->lastpromotion);
        $diff = date_diff($promotion, $last, true);

        if ($diff->days < $this->waiting_time[$grade_achieve])
        {
            $errors['error_waitingtime'] = JText::_('COM_TKDCLUB_CANDIDATE_WAITING_TIME_NOT_ACHIEVED');
        }

        return $errors;
    }

    public function response($data)
    {
        echo json_encode($data);
        $this->app->close();
    }
}