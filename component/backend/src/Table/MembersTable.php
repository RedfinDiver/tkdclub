<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
* Members table class
*/
class MembersTable extends Table
{
	/**
	 * Indicates that columns fully support the NULL value in the database
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $_supportNullValue = true;

	protected $_jsonEncode = array();

	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since   1.0
	 */
	public function __construct($db)
	{
		$this->_jsonEncode = array('functions', 'licenses');

		parent::__construct('#__tkdclub_members', 'member_id', $db);

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

		// taking care of null values
		empty($this->memberpass) ? $this->memberpass = null : null;
		empty($this->lastpromotion) ? $this->lastpromotion = null : null;
		empty($this->leave) ? $this->leave = null : null;
		empty($this->created) ? $this->created = null : null;


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
        
        return parent::store($updateNulls);
    }
}