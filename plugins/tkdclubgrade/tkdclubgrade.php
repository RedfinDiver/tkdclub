<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Plugin that changes the entries of grade and lastpromotion in the members-table after
 * publishing (means passing) the promotion-participation in the candidates-view.
 * 
 * @todo error handling, message if updating fails
 */
class plgContentTkdclubgrade extends JPlugin
{
    public function onContentChangeState ($context, $pks, $value)
    {
       //first check if its the right context, otherwise exit the plugin
        if ($context != 'com_tkdclub.candidate' || $value == 0)
        {
            return;
        }
        
        foreach ($pks as $i => $pk)
        {
            $memberData = $this->getMemberData($pk);
            $this->setMemberData($memberData->id_candidate, $memberData->grade_achieve, $memberData->date);
        }
        
        return true;
    }
    
    /**
     * Gets the Data for the passed promotion: member_id, grade_achieve and date
     * 
     * @param   type  integer $id_candidate
     * @return  type  object
     */
    protected function getMemberData($id_candidate)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id_candidate, a.grade_achieve, b.date')
              ->from($db->quoteName('#__tkdclub_candidates','a'))
              ->join('inner', '#__tkdclub_promotions AS b ON b.promotion_id = a.id_promotion')
              ->where('a.id =' . (int)$id_candidate);
        $memberData = $db->setQuery($query)->loadObject();
       
        return $memberData;
    }
    
    /**
     * Sets the new data in the members-table
     * 
     * @param type integer $member_id
     * @param type string  $grade_achieve
     * @param type string  $date_exam
     * @return boolean
     */
    protected function setMemberData($member_id, $grade_achieve, $date_exam)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__tkdclub_members')
              ->set(array('grade = '. $db->quote($grade_achieve), 'lastpromotion = '.$db->quote($date_exam)))
              ->where('member_id = ' . (int) $member_id);
        $db->setQuery($query);
        
        if(!$db->query())
        {
            return false;
        }
        
        return true;
     }
}