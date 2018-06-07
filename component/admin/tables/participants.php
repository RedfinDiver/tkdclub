<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
* Event participants table class
*/
class TkdClubTableParticipants extends JTable
{
    public function __construct(&$db)      
    {
        parent::__construct('#__tkdclub_event_participants', 'id', $db);
    }

    public function store($updateNulls = false) {
        
        $date   = JFactory::getDate()->toSql();
        $userId = JFactory::getUser()->id;

        $this->modified = $date;

        if ($this->id)
        {
            // Existing item
            $this->modified_by = $userId;
        }
        else
        {
            $this->created = $date;
            $this->created_by = $userId;
        }
        
        return parent::store($updateNulls = false);
    }
}