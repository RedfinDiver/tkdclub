<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for edit view 'candidate'
 */
class TkdClubModelCandidate extends JModelAdmin
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
    public function getTable($type = 'Candidates', $prefix = 'TkdClubTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
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
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.candidate.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }
        
    /**
     * Fügt aus der Mitgliederliste die Daten für aktuellen Grad und letzte Prüfung ein
     * 
     * @param   type    integer $pk, the id of the item
     * @return  type    object - expanded with properties of members table
     * 
     * @since   1.0
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item->id_candidate >= (int)1)
        {      
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE);

            $query->select('a.grade, a.lastpromotion');
            $query->from('#__tkdclub_members AS a');
            $query->join('inner', '#__tkdclub_candidates AS b ON b.id_candidate = a.member_id');
            $query->where('b.id_candidate ='. (int) $item->id_candidate);
            
            // die Daten aus der Mitgliederliste laden und in ein array speichern
            $item2 = $db->setQuery($query)->loadAssoc();
            $item2['lastpromotion'] = JHtml::_('date', $item2['lastpromotion'], JText::_('DATE_FORMAT_LC4'));
            
            //das erstellte array an das bestehende item anhängen
            $item->setProperties($item2);
        }

        return $item;
    }
         
    /**
     * Sucht in der Datenbank nach freigegebenen Prüfungen, verwendet
     * der Controller TkdClubControllerExampart zum Absetzen einer Meldung
     * 
     * @since   1.0
     * @return boolean true if exams are published, false otherwise
     * @see TkdClubControllerExampart
     */
    public function checkExamsPublished()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(*)');
        $query->from($db->quoteName('#__tkdclub_promotions'))
                ->where('promotion_state = 1');           
        $published = (int) $db->setQuery($query)->loadResult();

        return ($published > 0) ? true : false;
    }

    /**
     * get the memberdata from database for AJAX call
     */
    public function getCandidateData($candidate_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('member_id, firstname, lastname, grade, lastpromotion, birthdate, entry')
                ->from($db->quoteName('#__tkdclub_members'))
                ->where('member_id = '. (int) $candidate_id);
        $db->setQuery($query);
        
        $result = $db->loadAssoc();

        return $result;
    }

    /**
     * get the promotion data from database for AJAX call
     */
    public function getPromotionDate($promotion_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('date')
                ->from($db->quoteName('#__tkdclub_promotions'))
                ->where('promotion_id = '. (int) $promotion_id);
        $db->setQuery($query);

        return $db->loadResult();
    }
}