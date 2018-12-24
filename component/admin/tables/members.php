<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
* Members table class
*/
class TkdClubTableMembers extends JTable
{
    public function __construct(&$db)      
    {
        parent::__construct('#__tkdclub_members', 'member_id', $db);
    }

    public function store($updateNulls = false) {

        // Transform the functions field
		if (is_array($this->functions))
		{
			$functions = new Registry($this->functions);
			$this->functions = (string) $functions;
        }

        // Transform the licenses field
		if (is_array($this->licenses))
		{
			$licenses = new Registry($this->licenses);
			$this->licenses = (string) $licenses;
        }

        $date   = JFactory::getDate()->toSql();
		$userId = JFactory::getUser()->id;

		$this->modified = $date;

		if ($this->member_id)
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