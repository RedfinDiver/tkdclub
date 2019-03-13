<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2019 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

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

	function onUserBeforeDataValidation($form, &$user)
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

		// setting the username when nothing is set in the form
		if (!$user['username'])
		{
			$user['username'] = $user['firstname'] . ' ' . $user['lastname'];
		}

		// setting the email and the password as we don`t ask for 2 times
		$user['password2'] = $user['password1'];
		$user['email2'] = $user['email1'];
	}
}