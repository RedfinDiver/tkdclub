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
 * promotions table class
 */
class TkdClubTablePromotions extends Table
{
    public function __construct(&$db)      
    {
        $this->setColumnAlias('published', 'promotion_state'); // needed for autoworking of publish-method
        parent::__construct('#__tkdclub_promotions', 'promotion_id', $db);
    }

    public function store($updateNulls = false) {
        
        $date   = Factory::getDate()->toSql();
        $userId = Factory::getUser()->id;

        $this->modified = $date;

        if ($this->promotion_id)
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