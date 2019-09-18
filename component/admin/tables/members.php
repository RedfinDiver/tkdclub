<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
* Members table class
*/
class TkdClubTableMembers extends Table
{
	protected $_jsonEncode = array();

    public function __construct(&$db)      
    {
		$this->_jsonEncode = array('functions', 'licenses');
        parent::__construct('#__tkdclub_members', 'member_id', $db);
    }

	public function store($updateNulls = false)
	{
        $date   = Factory::getDate()->toSql();
		$userId = Factory::getUser()->id;

		$this->modified = $date;

		if ($this->member_id)
		{
			// Existing item
			$this->modified_by = $userId;
		}
		elseif(!$this->member_id && $this->created_by)
		{
			// new item from member registration
			$this->created = $date;
		}
		else
		{
            $this->created = $date;
            $this->created_by = $userId;
		}
		
		$this->iban = str_replace(' ', '', $this->iban);
        
        return parent::store($updateNulls = false);
    }
}