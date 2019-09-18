<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

/**
* candidates table class
*/
class TkdClubTableCandidates extends Table
{
    public function __construct(&$db)      
    {
        $this->setColumnAlias('published', 'test_state'); // needed for autoworking of publish-method
        parent::__construct('#__tkdclub_candidates', 'id', $db);
    }

    /**
	 * Stores a candidate
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 */
	public function store($updateNulls = false)
	{
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
    
        return parent::store($updateNulls);
    }
}