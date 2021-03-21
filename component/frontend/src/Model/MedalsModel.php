<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Site\Model;

\defined('_JEXEC') or die;

/**
 * Model for medals list view in frontend
 */
class MedalsModel extends \Redfindiver\Component\Tkdclub\Administrator\Model\MedalsModel
{   
    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__tkdclub_medals'));
        $query->order('date DESC');
        
        return $query;
    }
}