<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Redfindiver\Plugin\User\Tkdclubmember\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Plugin to extend the registration form for
 * fields used in the members table of com_tkdclub
 *
 * @since  5.0.0
 */
class Tkdclubmember extends CMSPlugin
{
	use DatabaseAwareTrait;

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
	 * Table instance of members table
	 */
	protected $row = '';
	
	/**
     * Constructor
     *
     * @param   DispatcherInterface  $dispatcher  The event dispatcher
     * @param   array                $config      An optional associative array of configuration settings.
     *                                            Recognized key values include 'name', 'group', 'params', 'language'
     *                                            (this list is not meant to be comprehensive).
     *
     * @since   1.5
     */
	public function __construct(DispatcherInterface $dispatcher, array $config = [])
	{
		parent::__construct($dispatcher, $config);

		// add the Table instance
		$this->row = Factory::getApplication()
						->bootComponent('com_tkdclub')
						->getMVCFactory()
						->createTable('Members', 'Administrator');
		
		// set the update- and ignore-fields
		$this->allFields = array_keys($this->row->getFields());

		$this->updateFields = array(
			'street', 'zip', 'city', 'country', 'phone', 'email', 'iban',
			'modified', 'modified_by', 'checked_out', 'checked_out_time'
		);
		
		$this->ignoreFields = array_diff($this->allFields, $this->updateFields);
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
		// Check if we are in site application
		if (!Factory::getApplication()->isClient('site'))
		{
			return true;
		}
		
		// We`re in site - check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_users.registration', 'com_users.profile')))
		{
			return true;
		}

		// We`re in the right form - remove the original fields
		foreach ($form->getFieldset('default') as $field)
		{
			$form->removeField($field->getAttribute('name'));
		}

		// Add the custom registration fields to the form
		FormHelper::addFieldPrefix('Redfindiver\\Component\\Tkdclub\\Administrator\\Field');
		FormHelper::addFormPath(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/forms');

		if($name == 'com_users.registration')
		{
			$form->loadFile('register');

			// We need the name-field because of Joomlas JS validation
			$form->setFieldAttribute('name', 'type', 'hidden');
			$form->setValue('name', null, 'placeholder');

		}
		elseif($name == 'com_users.profile')
		{
			$form->loadFile('profile');

			// we don`t show the name field
			$form->removeField('name');

			// when in edit mode, show only fields possible to change
			$layout = Factory::getApplication()->input->getCmd('layout', '');

			if($layout == 'edit')
			{
				foreach($this->ignoreFields as $field)
				{
					$form->removeField($field);
				}
			}
		}

		// bring in IBAN formating, masking and validation
		$document = Factory::getApplication()->getDocument();

		// Check for HTML document
		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
        {
            return true;
        }

		// WebAssetManager brings in css and js
		$document->getWebAssetManager()
			->getRegistry()->addExtensionRegistryFile('plg_user_tkdclubmember');

		$document->getWebAssetManager()
			->useStyle('plg_user_tkdclubmember.tkdclub')
			->useScript('plg_user_tkdclubmember.iban')
			->useScript('plg_user_tkdclubmember.mask-iban')
			->useScript('plg_user_tkdclubmember.format-iban');

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
		// Check for site application
		if (!Factory::getApplication()->isClient('site'))
		{
			return true;
		}

		// We're in site - check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		// It`s a valid form - proceed with data preparation
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
					// a null date or no data from database is changed to empty string
					if($this->row->$field === '0000-00-00' || !$this->row->$field)
					{
						$data->$field = '';
					}
					else
					{
						$data->$field = $this->row->$field;
					}
				}
			}
		}
		
		if (!HTMLHelper::isRegistered('users.calendar')) {
			HTMLHelper::register('users.calendar', [__CLASS__, 'calendar']);
		}
		
		if (!HTMLHelper::isRegistered('users.sex'))
		{
			HTMLHelper::register('users.sex', [__CLASS__, 'sex']);
		}

		if (!HTMLHelper::isRegistered('users.member_state'))
		{
			HTMLHelper::register('users.member_state', [__CLASS__, 'member_state']);
		}

		if (!HTMLHelper::isRegistered('users.functions'))
		{
			HTMLHelper::register('users.functions', [__CLASS__, 'functions']);
		}

		if (!HTMLHelper::isRegistered('users.licenses'))
		{
			HTMLHelper::register('users.licenses', [__CLASS__, 'licenses']);
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
			$this->row->id = $this->getMemberId($userId);
			$this->row->save($update, '', $this->ignoreFields);
		}
		
		return true;
	}

	/**
	 * Get the data from the members table by user_id
	 */
	public function getMemberId($user_id)
	{
		$db = $this->getDatabase();
		
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			  ->from($db->quoteName('#__tkdclub_members'))
			  ->where($db->quoteName('user_id') . ' = :user_id')
			  ->bind(':user_id', $user_id, ParameterType::INTEGER);
		
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Returns html markup showing a date picker
	 *
	 * @param   string  $value  valid date string
	 *
	 * @return  mixed
	 */
	public static function calendar($value)
	{
		if (empty($value))
		{
			return HTMLHelper::_('users.value', $value);
		}
		else
		{
			return HTMLHelper::_('date', $value, 'DATE_FORMAT_LC4', null);
		}
	}

	/**
	 * Returns html markup for sex field
	 *
	 * @param   string  $value  valid date string
	 *
	 * @return  mixed
	 */
	public static function sex($value)
	{
		$sex = array('female' => 'PLG_USER_TKDCLUBMEMBER_SEX_FEMALE',
					  'male'  => 'PLG_USER_TKDCLUBMEMBER_SEX_MALE');
		
		if (empty($value))
		{
			return HTMLHelper::_('users.value', $value);
		}
		else
		{
			return Text::_($sex[$value]);
		}
	}

	/**
	 * Returns html markup for member state field
	 *
	 * @param   string  $value  valid date string
	 *
	 * @return  mixed
	 */
	public static function member_state($value)
	{
		$state = array(
			'active'    => 'PLG_USER_TKDCLUBMEMBER_STATE_ACTIVE',
			'inactive'	=> 'PLG_USER_TKDCLUBMEMBER_STATE_INACTIVE',
			'support' 	=> 'PLG_USER_TKDCLUBMEMBER_STATE_SUPPORT'
		);
		
		if (empty($value))
		{
			return HTMLHelper::_('users.value', $value);
		}
		else
		{
			return Text::_($state[$value]);
		}
	}

	/**
	 * Returns html markup for functions field
	 *
	 * @param   array  $value  array with functions
	 *
	 * @return  mixed
	 */
	public static function functions($value)
	{
		if (empty($value))
		{
			return HTMLHelper::_('users.value', $value);
		}
		
		$string = '';
		$it = 0;
		$i = count($value);

		$functions = array(
			'president'     => Text::_('PLG_USER_TKDCLUBMEMBER_PRESIDENT'),
			'vpresident'	=> Text::_('PLG_USER_TKDCLUBMEMBER_VPRESIDENT'),
			'treasurer' 	=> Text::_('PLG_USER_TKDCLUBMEMBER_TRAESURER'),
			'secratary'		=> Text::_('PLG_USER_TKDCLUBMEMBER_SECRATARY'),
			'trainer'		=> Text::_('PLG_USER_TKDCLUBMEMBER_TRAINER')
		);

		foreach($value as $func)
		{
			$it += 1;
			array_key_exists($func, $functions) ? $string .= $functions[$func] : $string .= $func;
			$it < $i ? $string .= ', ' : null;
		}

		return $string;
	}

	/**
	 * Returns html markup for licenses field
	 *
	 * @param   array  $value  array with licenses
	 *
	 * @return  mixed
	 */
	public static function licenses($value)
	{
		if (empty($value))
		{
			return HTMLHelper::_('users.value', $value);
		}
		
		$string = '';
		$it = 0;
		$i = count($value);

		$licenses = array(
			'trainer_d'     => Text::_('PLG_USER_TKDCLUBMEMBER_TRAINER_D'),
			'trainer_c'		=> Text::_('PLG_USER_TKDCLUBMEMBER_TRAINER_C'),
			'trainer_b' 	=> Text::_('PLG_USER_TKDCLUBMEMBER_TRAINER_B'),
			'trainer_a'		=> Text::_('PLG_USER_TKDCLUBMEMBER_TRAINER_A'),

			'referee_r_ky'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_R_KY'),
			'referee_s_ky'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_S_KY'),
			'referee_i_ky'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_I_KY'),

			'referee_r_po'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_R_PO'),
			'referee_s_po'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_S_PO'),
			'referee_i_po'	=> Text::_('PLG_USER_TKDCLUBMEMBER_REFEREE_I_PO')
		);

		foreach($value as $license)
		{
			$it += 1;
			array_key_exists($license, $licenses) ? $string .= $licenses[$license] : $string .= $license;
			$it < $i ? $string .= ', ' : null;
		}

		return $string;
	}
}