<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Model-class for edit view 'candidate'
 */
class CandidateModel extends AdminModel
{   
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @throws  Exception
     */
    public function getTable($type = 'candidates', $prefix = 'administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
    * Method for getting the form from the model.
    *
    * @param   array    $data      Data for the form.
    * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
    *
    * @return  mixed  A JForm object on success, false on failure
    *
    * @since   1.0
    */
    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'candidate',  $options);

        if (empty($form))
        {
            return false;
        }

        return $form;
    }
        
    /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   1.0
	 */
    protected function loadFormData()
    {
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.candidate.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }
        
    /**
     * Overriding getItem method, gets lastpromotion and grade from members-table
     * 
     * @param   type    integer $pk, the id of the item
     * @return  type    object - expanded with properties of members table
     * 
     * @since   1.0
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk = null);

        if ($item->id_candidate >= (int)1)
        {      
            $db = Factory::getDbo();
            $query = $db->getQuery(TRUE);

            $query
                ->select('a.grade, a.lastpromotion')
                ->from($db->quoteName('#__tkdclub_members', 'a'))
                ->join('inner', $db->quoteName('#__tkdclub_candidates', 'b') .' ON (' . $db->quoteName('b.id_candidate') . ' = ' . $db->quoteName('a.id') . ')')
                ->where('b.id_candidate ='. (int) $item->id_candidate);
            
            // Get the data and save to array
            $item2 = $db->setQuery($query)->loadAssoc();
            
            if ($item2['lastpromotion'] == '0000-00-00')
            {
                $item2['lastpromotion'] = '';
            }
            else
            {
                $item2['lastpromotion'] = HTMLHelper::_('date', $item2['lastpromotion'], Text::_('DATE_FORMAT_LC4'));
            }
            
            // Append the array
            $item->setProperties($item2);
        }

        if (!isset($item->id_promotion)) 
        {
            // Prefill the promotion field with prior selected value for convinience
            $session = Factory::getSession();
            $preselected_promotion = $session->get('preselected_promotion', '', 'tkdclub');
            $item->id_promotion = $preselected_promotion;
        }
        
        return $item;
    }

    public function save($data)
    {
        // Store the selected promotion field in the session
        $session = Factory::getSession();
        $session->set('preselected_promotion', $data['id_promotion'], 'tkdclub');

        return parent::save($data);
    }
  
    /**
     * Search the database for publihed promotions
     * Controller TkdClubControllerExampart uses this for a message
     * 
     * @return  boolean     true if exams are published, false otherwise
     * @see TkdClubControllerCandidate
     */
    public function checkPromotionsPublished()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(*)');
        $query->from($db->quoteName('#__tkdclub_promotions'))
                ->where('promotion_state = 1');           
        $published = (int) $db->setQuery($query)->loadResult();

        return ($published > 0) ? true : false;
    }

    /**
     * Get the memberdata from database for AJAX call
     */
    public function getCandidateData($candidate_id, $promotion_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query
            ->select('a.id, a.firstname, a.lastname, a.grade, a.lastpromotion, a.birthdate, a.entry')
            ->from($db->quoteName('#__tkdclub_members', 'a'))
            ->select($db->quoteName('b.date', 'promotion_date'))
            ->select($db->quoteName('b.type', 'promotion_type'))
            ->join('LEFT', $db->quoteName('#__tkdclub_promotions', 'b') . ' ON  (' . $db->quoteName('b.id') . ' = ' . $promotion_id . ')')
            ->where('a.id = '. (int) $candidate_id);
        
        $db->setQuery($query);
        $result = $db->loadAssoc();

        // Checking for already existing candidates, sometimes double promotions are allowed
        $allowedDoubleGrades = array('0' => 0, '10. Kup' => 1, '9. Kup' => 2);
        $doublePossible = array_key_exists($result['grade'], $allowedDoubleGrades);
        $alreadySubscribed = $this->getAlreadySubscribed($candidate_id, $promotion_id);        

        if ($doublePossible) // double possible
        {
            $result['double'] = $alreadySubscribed;
            return $result;
        }

        if (!$doublePossible) // no double possible, just return the info
        {
            $result['second_promotion'] = $alreadySubscribed;
            return $result;
        }
    }

    /**
     * Checks for already subscribed candidates for a promotion
     * This is for allowed double promotions
     * 
     * @param   int     $candidate_id 
     * @param   int     $promotion_id
     * @return  mixed   boolean false if no other subscription
     *                  string with Kup when there is a subscription
     *                  string with error-message when a double promotion is already found
     */
    private function getAlreadySubscribed($candidate_id, $promotion_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query
            ->select('a.grade_achieve')
            ->from($db->quoteName('#__tkdclub_candidates', 'a'))
            ->where('a.id_candidate = '. (int) $candidate_id)
            ->where('a.id_promotion = '. (int) $promotion_id);

        $db->setQuery($query);
        $grade_achieve = $db->loadColumn();
        $count = count($grade_achieve);

        if ($count == 0)
        {
            return false;
        }
        elseif ($count == 1)
        {
            return $grade_achieve[0];
        }
        elseif ($count >= 2)
        {
            return 'error_already_double';
        }
    }
}