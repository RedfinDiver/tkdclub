<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* helper for getting trainer names from database
*/
class TkdclubHelperTrainer
{
    public static function getTrainer($fromTrainingsTable = true)
    {
        if ($fromTrainingsTable == true)
        {
            $db = JFactory::getDBO();
            
            $q1 = $db->getQuery(true)
                    ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
                    ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.trainer') . ')' )
                    ->where('trainer>0');
            
            $q2 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist1') . ')' )
            ->where('assist1>0');
            
            $q3 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist2') . ')' )
            ->where('assist2>0');

            $q4 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist3') . ')' )
            ->where('assist3>0');
            
            $query = $q1->union($q2)->union($q3)->union($q4);
            $db->setQuery($query);

            return TkdclubHelperTrainer::prepareArray($db->loadObjectList());
        }

        if ($fromTrainingsTable == false)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
    
            $query->select($db->quoteName(array('member_id', 'firstname', 'lastname')));
            $query->from($db->quoteName('#__tkdclub_members'));
            $query->order('member_id ASC');
            $query->where($db->quoteName('functions') . ' LIKE '. $db->quote('%Trainer%'));
    
            $db->setQuery($query);
    
            return TkdclubHelperTrainer::prepareArray($db->loadObjectList());
        }
        
    }

    public static function prepareArray($result)
    {
        $trainers = array();
        
        foreach ($result as $trainer)
        {
            $trainers[$trainer->member_id] = $trainer->firstname . ' ' . $trainer->lastname;
        }

        return $trainers;
    }
}