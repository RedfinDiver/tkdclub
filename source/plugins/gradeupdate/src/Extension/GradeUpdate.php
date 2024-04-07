<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Redfindiver\Plugin\Content\GradeUpdate\Extension;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;

defined('_JEXEC') or die;

/**
 * Grade update plugin for com_tkdclub
 * 
 */
class GradeUpdate extends CMSPlugin
{
    use DatabaseAwareTrait;

    /**
     * Changes the entries of grade and lastpromotion in the members-table after
     * publishing (means passing) the promotion-participation in the candidates-view.
     * 
     * @todo error handling, message if updating fails
     */
    public function onContentChangeState ($context, $pks, $value)
    {
       // First check if its the right context, otherwise exit the plugin
        if ($context != 'com_tkdclub.candidate' || $value == 0 || $value == 2)
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
     * @param   integer     $id_candidate
     * 
     * @return  object
     */
    protected function getMemberData($id_candidate)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('a.id_candidate, a.grade_achieve, b.date')
              ->from($db->quoteName('#__tkdclub_candidates','a'))
              ->join('inner', '#__tkdclub_promotions AS b ON b.id = a.id_promotion')
              ->where('a.id =' . (int)$id_candidate);
        $memberData = $db->setQuery($query)->loadObject();
       
        return $memberData;
    }
    
    /**
     * Sets the new data in the members-table
     * 
     * @param   integer     $member_id
     * @param   string      $grade_achieve
     * @param   string      $date_exam
     * 
     * @return  boolean     True on success
     */
    protected function setMemberData($member_id, $grade_achieve, $date_exam)
    {
        $table = Factory::getApplication()
                    ->bootComponent('com_tkdclub')
                    ->getMVCFactory()
                    ->createTable('Members', 'Administrator');
       
        $table->load($member_id);
        $table->lastpromotion = $date_exam;
        $table->grade = $grade_achieve;
        $table->store();
    }
}