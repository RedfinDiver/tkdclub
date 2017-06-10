<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* members table class
*/
class TkdClubTableMembers extends JTable
{
    public function __construct(&$db)      
    {
        parent::__construct('#__tkdclub_members', 'member_id', $db);
    }
}