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
 * medals table class
 */
class TkdClubTableMedals extends Table
{
    public function __construct(&$db)      
    {
        $this->setColumnAlias('published', 'state'); // needed for autoworking of publish-method
        parent::__construct('#__tkdclub_medals', 'medal_id', $db);
    }

    public function store($updateNulls = false) {

        // Transform the winner_ids field
        if (is_array($this->winner_ids))
        {
            // take json_encode for array conversion
            $this->winner_ids = json_encode($this->winner_ids, JSON_NUMERIC_CHECK);
        }

        $date   = Factory::getDate()->toSql();
        $userId = Factory::getUser()->id;

        $this->modified = $date;

        if ($this->medal_id)
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