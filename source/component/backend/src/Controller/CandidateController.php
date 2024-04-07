<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

class CandidateController extends FormController
{
    protected $text_prefix = 'COM_TKDCLUB_CANDIDATE';

    /**
     * Data fetched for a candidate
     * 
     * @var array
     */
    protected $candidate_data = array();

    /**
     * The grades with a certain value, makes it easier to calculate
     * 
     * @var array
     */
    protected $grades = array(
        '10. Kup' => 1, '9. Kup' => 2, '8. Kup' => 3, '7. Kup' => 4, '6. Kup' => 5, '5. Kup' => 6, '4. Kup' => 7,
        '3. Kup' => 8, '2. Kup' => 9, '1. Kup' => 10, '1. Poom' => 11, '2. Poom' => 12, '3. Poom' => 13, '4. Poom' => 14,
        '1. Dan' => 11, '2. Dan' => 12, '3. Dan' => 13, '4. Dan' => 14, '5. Dan' => 15, '6. Dan' => 16, '7. Dan' => 17,
    );

    /**
     * The waiting time in days for a certain grade
     * 
     * @var integer
     */
    protected $waiting_time = array(
        '10. Kup' => 56, '9. Kup' => 56, '8. Kup' => 84, '7. Kup' => 84, '6. Kup' => 112, '5. Kup' => 112, '4. Kup' => 140,
        '3. Kup' => 140, '2. Kup' => 168, '1. Kup' => 168, '1. Poom' => 252, '2. Poom' => 728, '3. Poom' => 1092, '4. Poom' => 1092,
        '1. Dan' => 182, '2. Dan' => 364, '3. Dan' => 728, '4. Dan' => 1092, '5. Dan' => 1456, '6. Dan' => 1820, '7. Dan' => 2184
    );

    /**
     * The minimum ages for a certain grade in years
     * 
     * @var array
     */
    protected $minimum_age = array(
        '1. Poom' => 6, '2. Poom' => 8, '3. Poom' => 11, '4. Poom' => 14,
        '1. Dan' => 15, '2. Dan' => 16, '3. Dan' => 18, '4. Dan' => 21,
        '5. Dan' => 25, '6. Dan' => 30, '7. Dan' => 36
    );
    

    /**
     * Add a new candidate
     * 
     * Overriding the add-method for checking if a promotion is published
     * and message to the user if not
     */
    public function add()
    {
        if (!$this->getModel()->checkPromotionsPublished())
        {
            $this->setMessage(Text::_('COM_TKDCLUB_CANDIDATE_NO_PUPLISHED_PROMOTIONS'), 'error');

            $this->setRedirect(
                    Route::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_list
                            . $this->getRedirectToListAppend(),
                        false)
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
        $candidate_id = Factory::getApplication()->input->get('candidate_id', '', 'int');
        $promotion_id = Factory::getApplication()->input->get('promotion_id', '', 'int');
        $this->app = Factory::getApplication();

        // check if there are Ids in the request
        $checkIDs = $this->checkIDs($candidate_id, $promotion_id);
        !empty($checkIDs) ? $this->response($checkIDs) : null;

        // IDs ok, get Model and fetch data
        $model = $this->getModel($name = 'candidate', $prefix = 'Administrator', $config = array());
        $this->candidate_data = $model->getCandidateData($candidate_id, $promotion_id);
        $this->candidate_data['name'] = $this->candidate_data['firstname'] . ' ' . $this->candidate_data['lastname'];

        // Check for some missing candidates-data for finding proper grade to achieve
        $this->checkData($this->candidate_data);
 
        // Check for double promotions and already assigned promotions
        $checkForDouble = $this->checkForDouble();
        !empty($checkForDouble) ? $this->response($checkForDouble) : null;

        // check for minimum age and waiting time
        $this->candidate_data['age'] = (int) TkdclubHelper::getAgetoDate($this->candidate_data['promotion_date'], $this->candidate_data['birthdate']);
        $this->changeGrades();

        // calculate grade value
        if (!$this->candidate_data['double']) {
            $this->candidate_data['grade'] == '0' ? $grade_achieve_value = 1 : $grade_achieve_value = $this->grades[$this->candidate_data['grade']] + 1;
        } else {
            $grade_achieve_value = $this->grades[$this->candidate_data['double']] + 1;
        }

        // Find the correct grade for the grade value
        $this->candidate_data['grade_achieve'] = array_search($grade_achieve_value, $this->grades);

        // Check if the correct promotion type is selected
        $checkType = $this->checkType($grade_achieve_value);
        !empty($checkType) ? $this->response($checkType) : null;

        $checkAgeAndWaitingtime = $this->checkAgeAndWaitingtime($grade_achieve_value, $this->candidate_data['grade_achieve']);
        !empty($checkAgeAndWaitingtime) ? $this->response($checkAgeAndWaitingtime) : null;

        // everything is fine, give back the grade achieve and Information about double promotion
        $candidate_data['grade_achieve'] = array_search($grade_achieve_value, $this->grades);

        if ($this->candidate_data['lastpromotion'] == false) {
            $this->candidate_data['lastpromotion'] = Text::_('COM_TKDCLUB_CANDIDATE_NOPROMOTION');
            $this->candidate_data['grade'] = Text::_('COM_TKDCLUB_NO_GRADE_LISTVIEW');
        } else {
            $this->candidate_data['lastpromotion'] = HTMLHelper::_('date', $this->candidate_data['lastpromotion'], Text::_('DATE_FORMAT_LC4'));
        }

        if ($this->candidate_data['double']) {
            $this->candidate_data['notes'] = Text::_('COM_TKDCLUB_CANDIDATE_DOUBLE_PROMOTION');
        }

        // bring in the amount of trainings since last promotion
        $member_model = $this->getModel($name = 'member', $prefix = 'Administrator', $config = array());
        $trainings = $member_model->getTrainings($candidate_id);
        $this->candidate_data['sLastPromotion'] = $trainings['sLastPromotion'];

        $this->candidate_data['no_error'] = true;

        $this->candidate_data['second_promotion'] = false;

        $this->response($this->candidate_data);
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

        $candidate_id == '' ? $errors['error_candidate_id'] = Text::_('COM_TKDCLUB_CANDIDATE_MISSING_CANDIDATE_ID') : null;
        $promotion_id == '' ? $errors['error_promotion_id'] = Text::_('COM_TKDCLUB_CANDIDATE_MISSING_PROMOTION_ID') : null;

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

        // checking for double
        $this->candidate_data['double'] = isset($candidate_data['double']) ? $candidate_data['double'] : null;
        $this->candidate_data['second_promotion'] = isset($candidate_data['second_promotion']) ? $candidate_data['second_promotion'] : null;

        // missing birthdate
        $this->candidate_data['birthdate'] == '0000-00-00' ? $errors['error_birthdate'] = '- ' . Text::_('COM_TKDCLUB_MEMBER_BIRTHDATE') : null;

        // missing last promotion date when grade is set
        if ($this->candidate_data['lastpromotion'] == '0000-00-00' && $this->candidate_data['grade'] != '0') {
            $errors['error_lastpromotion'] = '- ' . Text::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION');
        }

        // missing grade when last promotion date is set
        if ($this->candidate_data['lastpromotion'] != '0000-00-00' && $this->candidate_data['grade'] == '0') {
            $errors['error_grade'] = '- ' . Text::_('COM_TKDCLUB_MEMBER_GRADE');
        }

        // missing entry date when no grade is set
        if ($this->candidate_data['entry'] == '0000-00-00' && $this->candidate_data['grade'] == '0') {
            $errors['error_entry'] = '- ' . Text::_('COM_TKDCLUB_MEMBER_ENTRY');
        }

        // when there are errors, create a link for editing the missing data in member view
        if (!empty($errors)) {
            $errors['error'] = '<b>' . Text::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->name . '</b>'
                . '<br>'
                . Text::_('COM_TKDCLUB_CANDIDATE_MISSING_DATA');
            $edit_link = Route::_("index.php?option=com_tkdclub&task=member.edit&id=" . $candidate_data['member_id']);
            $errors['edit_link'] = '<span class="icon-edit"> </span>'
                . '<a href="' . $edit_link . '">'
                . $this->name . Text::_('COM_TKDCLUB_CANDIDATE_EDITING_IN_DATABASE')
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

        if ($this->candidate_data['double'] == 'error_already_double') {
            $errors[$this->candidate_data['double']] = '- ' . Text::_('COM_TKDCLUB_CANDIDATE_ALREADY_DOUBLE_PROMOTION');
        }

        if ($this->candidate_data['second_promotion']) {
            $errors['second_promotion'] = '- ' . Text::_('COM_TKDCLUB_CANDIDATE_ALREADY_PROMOTION_ASSIGNED');
        }

        if (!empty($errors)) {
            $errors['error'] = '<b>' . Text::_('COM_TKDCLUB_CANDIDATE_ERROR') .  $this->name . '</b>';
        }

        return $errors;
    }

    /**
     * Change grades for candidates < 15 years
     */
    protected function changeGrades()
    {
        if ($this->candidate_data['age'] < 15) {
            unset($this->grades['1. Dan']);
            unset($this->grades['2. Dan']);
            unset($this->grades['3. Dan']);
            unset($this->grades['4. Dan']);
            unset($this->grades['5. Dan']);
            unset($this->grades['6. Dan']);
            unset($this->grades['7. Dan']);
        } else {
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
            $this->candidate_data['age'] < $this->minimum_age[$grade_achieve] ? $errors['error_minage'] = '- ' . Text::_('COM_TKDCLUB_CANDIDATE_MINIMUM_AGE_NOT_ACHIEVED') : null;
        }

        // Now check the waiting time, for double promotion calculate as necessary
        $promotion = date_create($this->candidate_data['promotion_date']);

        if (!$this->candidate_data['double']) // No double promotion
        {
            $this->candidate_data['lastpromotion'] == false ? $last = date_create($this->candidate_data['entry']) : $last = date_create($this->candidate_data['lastpromotion']);
            $time = $this->waiting_time[$grade_achieve];
        }

        if ($this->candidate_data['double']) // double promotion, figure out date to use and waiting time
        {
            $this->candidate_data['lastpromotion'] == false ? $last = date_create($this->candidate_data['entry']) : $last = date_create($this->candidate_data['lastpromotion']);

            $time2 = $this->waiting_time[$grade_achieve]; // for 2. grade
            $grade1_val = $this->grades[$grade_achieve] - 1;
            $grade1 = array_search($grade1_val, $this->grades);
            $time1 = $this->waiting_time[$grade1];

            $time = $time1 + $time2;
        }

        $diff = date_diff($promotion, $last, true);

        if ($diff->days < $time) {
            $errors['error_waitingtime'] = '- ' . Text::_('COM_TKDCLUB_CANDIDATE_WAITING_TIME_NOT_ACHIEVED');
        }

        if (!empty($errors)) {
            $errors['error'] = '<b>' . Text::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->candidate_data['name'] . '</b>';
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

        if ($grade_achieve_value > 10 && $this->candidate_data['promotion_type'] == 'kup') {
            $errors['error_type'] = Text::_('COM_TKDCLUB_CANDIDATE_SUBSCRIBE_TO_DAN');
        }

        if ($grade_achieve_value < 11 && $this->candidate_data['promotion_type'] == 'dan') {
            $errors['error_type'] = Text::_('COM_TKDCLUB_CANDIDATE_SUBSCRIBE_TO_KUP');
        }

        if (!empty($errors)) {
            $errors['error'] = '<b>' . Text::_('COM_TKDCLUB_CANDIDATE_ERROR') . $this->candidate_data['name'] . '</b>';
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
        $json = json_encode($data);
        echo $json;
        //echo new JsonResponse($data);
        $this->app->close();
    }
}
