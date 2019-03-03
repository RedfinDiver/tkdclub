<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class TkdclubHelper
{
    /**
     * Get all the email adresses for the given user group
     * 
     * @param   array   $groups   the group ids
     * 
     * @return  array   indexed array with email adresses for the groups
     * 
     */
    public static function getEmailFromUserGroups($groups)
    {   
        // If no group is given return
        if (empty($groups))
        {
            return false;
        }

        $emails = array();

        foreach ($groups as $group)
        {
            // Get all email adesses for the users
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                    ->select($db->qn('a.email'))
                    ->from($db->qn('#__users', 'a'))
                    ->join('LEFT', $db->qn('#__user_usergroup_map', 'b') . ' ON a.id = b.user_id')
		            ->where($db->qn('group_id') . ' = ' . (int) $group);
        
            $emails = array_merge($emails, $db->setQuery($query)->loadColumn());
        }

	    return array_unique($emails);
    }

    /**
     * Method get the name of a member by id
     * 
     * @param   mixed   array with integers which are the ids of members in db
     *                  json string
     *
     * @return  mixed   FALSE if there is no data
     *                  STRING string with names, diveded with " / "
     */
    public static function getMembersNames($ids, $memberlist)
    {           
        if (gettype($ids) != 'array')
        {
            $ids = json_decode($ids);
        }
        
        $i = count($ids);
        $names = '';
        $it = 0;

        foreach ($ids as $id)
        {
            $it += 1;
            $names .= $memberlist[$id];
            if ($it < $i) {$names .= ' / ';}
        }

        return $names; 
    }

    /**
     * Method get a associated array with members from the database
     *
     * @return  array    ["member_id" => "firstname lastname"]
     *
     */
    public static function getMemberlist()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $fields = array('member_id', 'firstname', 'lastname');
        
        $query->select($db->quoteName($fields))
              ->from($db->quoteName('#__tkdclub_members'));
        $db->setQuery($query);
        $items = $db->loadObjectList();

        // create arrays, $key = member_id, $value = 'firstname lastname'
        $memberlist = array();

        foreach ($items as $i => $item)
        {
            $memberlist[$item->member_id] = $item->firstname.' '.$item->lastname;
        }

        return $memberlist;
    }
}