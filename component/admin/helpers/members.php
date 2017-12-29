<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * With this class it is possible to get members names according to ids
 * Used in:
 * /views/medals/view.html.php
 * /views/medals/default.php
 * /controllers/export.raw.php
 */
class TkdclubHelperMembers
{       
    /**
     * Method get the name of a member by id
     *
     * @return  mixed    FALSE if there is no data
     *                   STRING string with names, diveded with " / "
     */
    public function getMembersNames($ids, $memberlist)
    {   
        if(empty($ids)) // no id passed
        {
            return false;
        }
        
        $ids = json_decode($ids);
        $i = count($ids);

        if(!is_array($ids)) // only 1 id to check
        {
            return $memberlist[$ids];
        }

        // more ids in array
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
     * Method get the list of members from the database
     *
     * @return  array    ["member_id" => "firstname lastname"]
     *
     */
    public static function getMemberlist()
    {
        $db = JFactory::getDbo();
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