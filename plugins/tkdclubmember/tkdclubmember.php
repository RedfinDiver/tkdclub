<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2019 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

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
	 * Which fields to update in the memberstable
	 * 
	 * @var   array
	 */
	protected $updateFields = array(
		'street', 'zip', 'city', 'country', 'phone', 'email'
	);
	
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

		if (!in_array($name, array('com_users.registration', 'com_users.user')))
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

		// Add the custom registration fields to the form.
		Form::addFormPath(__DIR__ . '/member');
		$form->loadFile('member');

		// Hide the name field, we create the name later
		// We need this because of some JS validation clientside in Joomla
		$form->setFieldAttribute('name', 'type', 'hidden');
    	$form->setValue('name', null, 'placeholder');

		foreach ($form->getFieldset('default') as $field)
		{
			$form->removeField($field->getAttribute('name'));
		}

		return true;
	}

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

		// setting the email and the password as we don`t ask for 2 times
		$user['password2'] = $user['password1'];
		$user['email2'] = $user['email1'];
	}

	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		$userId = ArrayHelper::getValue($data, 'id', 0, 'int');
		$row = Table::getInstance('members', 'TkdclubTable', array());

		if($isNew)
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
				'user_id'	   => $userId,
				'member_state' => 'active',
				'entry'		   => Factory::getDate()->toSql(),
				'created_by'   => $userId	
			);
	
			$row->save($memberdata);
		}

		if(!$isNew)
		{
			$memberId = $this->getMemberdata($userId);

			$update = new stdClass();
			$update->member_id = $memberId;
			
			foreach($this->updateFields as $field)
			{
				!empty($data[$field]) ? $update->$field = $data[$field] : null;
			}
			$update->modified_by = $userId;
			$update->modified = Factory::getDate()->toSql();

			$result = Factory::getDbo()->updateObject('#__tkdclub_members', $update, 'member_id');

		}
		
		return true;
	}

	/**
	 * Get the data from the members table by user_id
	 */
	public function getMemberdata($user_id)
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