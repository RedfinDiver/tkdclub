<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * Helper-class for com_tkdclub
 * With this class it is possible to get members names according to ids
 * Used in:
 * /views/medals/view.html.php
 * /views/medals/default.php
 * /controllers/export.raw.php
 *
 * @since  2.0
 */
class TkdclubHelperMembers
{       
    /**
    * Method get the name of a member by id
    *
    * @return  mixed    FALSE if there is no data
    *                   ARRAY $attachments with data otherwise
    *
    * @since   2.1
    */
    public function getMembersNames($ids, $memberlist)
    {   
        $names = '';
        if (is_numeric($ids)) // just one winner -> one integer value
        {
            $names =  $memberlist[$ids];
        }
        else
        {   
            $name_ids = explode(',', $ids);
            $i = count((array) $name_ids);
            $it = 0;
            foreach ( $name_ids as $name_id)
            {
                if ($i == 1) { $names .=  $memberlist[$name_id]; } // only one winner
                else 
                {   
                    $it += 1;
                    $names .= $memberlist[$name_id];
                    if ($it < $i) {$names .= ' / ';}
                }
            }
        }    

        return $names;
    }

    /**
     * Method get the list of members from the database
     *
     * @return  array    ["member_id" => "firstname lastname"]
     *
     */
    public function getMemberlist()
        {
             $db = JFactory::getDbo();
             $query = $db->getQuery(true);

             // selecting the members
             $fields = array('member_id', 'firstname', 'lastname');
             $query->select($db->quoteName($fields))
                   ->from($db->quoteName('#__tkdclub_members'));

                 $db->setQuery($query);
             $items = $db->loadObjectList();

             // create arrays, $key = member_id, $value = 'firstname lastname'
             $namelist = array();
             foreach ($items as $i => $item)
             {
                 $namelist[$item->member_id] = $item->firstname.' '.$item->lastname;
             }

             return $namelist;
         }
}