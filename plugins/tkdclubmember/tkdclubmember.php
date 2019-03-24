<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2019 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Format\Json;

Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tkdclub/tables');

/**
 * An example custom profile plugin.
 *
 * @since  1.6
 */
class PlgUserTkdclubmember extends JPlugin
{
	/**
    * Load the language file on instantiation.
    *
    * @var    boolean
    * @since  3.1
    */
	protected $autoloadLanguage = true;

	/**
	 * Layout from the active menuitem
	 * 
	 * @var	 string
	 */
	protected $layout = '';

	/**
	 * All the fields in the members table
	 * 
	 * @var   array
	 */
	protected $allFields = array();

	/**
	 * Which fields to update in the memberstable, all others are not allowed
	 * to update by the user him-/herself
	 * 
	 * @var   array
	 */
	protected $updateFields = array();

	/**
	 * Which fields to ignore during update from the user in the memberstable
	 * 
	 * @var	  array
	 */
	protected $ignoreFields = array();

	/**
	 * JTable instance of members table
	 */
	protected $row = '';
	
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		// add the JTable instance
		$this->row = Table::getInstance('members', 'TkdclubTable', array());
		
		// set the update- and ignore-fields
		$this->allFields = array_keys($this->row->getFields());

		$this->updateFields = array(
			'street', 'zip', 'city', 'country', 'phone', 'email',
			'modified', 'modified_by', 'checked_out', 'checked_out_time'
		);
		
		$this->ignoreFields = array_diff($this->allFields, $this->updateFields);
		
		// get the layout from active menu item
		$query = Factory::getApplication()->getMenu()->getActive()->query;
		isset($query['layout']) ? $this->layout = $query['layout'] : $this->layout = '';

		FormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/component/com_tkdclub/models/fields');

		parent::__construct($subject, $config);
	}

	/**
	 * Adds additional fields to the user editing form
	 *
	 * @param   Form   $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareForm(Form $form, $data)
	{
		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_users.registration', 'com_users.profile')))
		{
			return true;
		}

		// Check whether this is frontend or admin
		if (Factory::getApplication()->isAdmin())
		{
			return true;
		}

		// Remove the original fields
		foreach ($form->getFieldset('default') as $field)
		{
			$form->removeField($field->getAttribute('name'));
		}

		// Add the custom registration fields to the form
		Form::addFormPath(__DIR__ . '/member');

		// which form file is to load?
		if($name == 'com_users.registration')
		{
			$form->loadFile('member');

			// We need the name-field because of Joomlas JS validation
			$form->setFieldAttribute('name', 'type', 'hidden');
    		$form->setValue('name', null, 'placeholder');

		}
		elseif($name == 'com_users.profile' && $this->layout == '')
		{
			$form->loadFile('profile');
			$form->removeField('name');
		}
		elseif($name == 'com_users.profile' && $this->layout == 'edit')
		{
			$form->loadFile('edit');
			$form->removeField('name');
		}

		return true;
	}

	/**
	 * Runs on content preparation
	 *
	 * @param   string  $context  The context for the data
	 * @param   object  $data     An object containing the data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		if (is_object($data))
		{
			$userId = isset($data->id) ? $data->id : 0;

			if ($userId > 0)
			{
				// load the data from members table
				$memberId = $this->getMemberId((int) $userId);
				$this->row->load($memberId);

				// add the data to the form
				foreach($this->allFields as $field)
				{
					$data->$field = $this->row->$field;
				}
			}
		}			
		
		return true;
	}

	/**
	 * Runs before data validation
	 * 
	 * @param	object	$form	the form object
	 * @param	array	$user	the data for the user
	 * 
	 * @return	boolean
	 */

	public function onUserBeforeDataValidation($form, &$user)
	{
		if ($form->getName() != 'com_users.registration')
		{
			return true;
		}
	
		// setting the name
		if (!$user['name'] or $user['name'] === 'placeholder')
		{
			$user['name'] = $user['firstname'] . ' ' . $user['lastname'];
		}
	}

	/**
	 * Saves member data to the members table
	 *
	 * @param   array    $data    entered user data
	 * @param   boolean  $isNew   true if this is a new user
	 * @param   boolean  $result  true if saving the user worked
	 * @param   string   $error   error message
	 *
	 * @return  boolean
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		// first check for succeded store
		if($result === false)
		{
			return false;

		}

		// get the Joomla! user-id
		$userId = ArrayHelper::getValue($data, 'id', 0, 'int');

		if($isNew) // new user
		{
			$memberdata = array(
				
				'firstname'    => $data['firstname'],
				'lastname'	   => $data['lastname'],
				'sex'		   => $data['sex'],
				'birthdate'	   => $data['birthdate'],
				'citizenship'  => $data['citizenship'],
				'street'	   => $data['street'],
				'zip'		   => $data['zip'],
				'city'		   => $data['city'],
				'country'	   => $data['country'],
				'phone'		   => $data['phone'],
				'email'		   => $data['email'],
				'user_id'	   => $userId, // links joomla user with member
				'member_state' => 'active',
				'entry'		   => Factory::getDate()->toSql(),
				'created_by'   => $userId	
			);
	
			$this->row->save($memberdata);
		}

		if(!$isNew) // user editing data
		{
			$update = array();
			
			// create update array only for allowed fields to update
			foreach($this->updateFields as $field)
			{
				!empty($data[$field]) ? $update[$field] = $data[$field] : null;
			}

			// save the data
			$this->row->member_id = $this->getMemberId($userId);
			$this->row->save($update, '', $this->ignoreFields);
		}
		
		return true;
	}

	/**
	 * Get the data from the members table by user_id
	 */
	public function getMemberId($user_id)
	{
		$db = Factory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select($db->qn('member_id'));
		$query->from($db->qn('#__tkdclub_members'));
		$query->where($db->qn('user_id') . ' = ' . (int) $user_id);
		
		$db->setQuery($query);

		return $db->loadResult();
	}
}