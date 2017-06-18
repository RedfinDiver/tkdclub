<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* trainings table class
*/
class TkdClubTableTrainings extends JTable
{
    public function __construct(&$db)      
    {
        $this->setColumnAlias('published', 'payment_state'); // needed for autoworking of publish-method
        parent::__construct('#__tkdclub_trainings', 'training_id', $db);
    }

}