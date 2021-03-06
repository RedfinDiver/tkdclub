<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * Trainings table class
 */
class TrainingsTable extends Table
{   
    /**
	 * Indicates that columns fully support the NULL value in the database
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $_supportNullValue = true;
    
    /**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since   1.0
	 */
    public function __construct($db)      
    {
        parent::__construct('#__tkdclub_trainings', 'training_id', $db);
    }

    /**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.7.0
	 */
    public function store($updateNulls = true)
    {
        $date   = Factory::getDate()->toSql();
        $userId = Factory::getUser()->id;

        $this->modified = $date;

        if ($this->training_id)
        {
            // Existing item
            $this->modified_by = $userId;
        }
        else
        {
            $this->created = $date;
            $this->created_by = $userId;
        }

        // taking care of null values
		empty($this->assist1) ? $this->assist1 = null : null;
        empty($this->km_assist1) ? $this->km_assist1 = null : null;
		empty($this->assist2) ? $this->assist2 = null : null;
        empty($this->km_assist2) ? $this->km_assist2 = null : null;
		empty($this->assist3) ? $this->assist3 = null : null;
        empty($this->km_assist3) ? $this->km_assist3 = null : null;
        
        return parent::store($updateNulls);
    }
}