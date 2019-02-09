<?php

// Set flag that this is a parent file.
const _JEXEC = 1;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', '/home/markus/Dokumente/webroot/dev_tkdclub');
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

// Load Library language
$lang = JFactory::getLanguage();
// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
|| $lang->load('files_joomla.sys', JPATH_SITE, null, true);


class TkdclubBirthdayreminder extends JApplicationCli
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 */
	public function doExecute()
	{
		$component = JComponentHelper::getComponent('com_tkdclub');

		$params = $component->params;
		$this->out($params);

		$birthdays = $this->getBirthdays();
		$recipients = $this->getRecipients();
		$this->out('test');
	}

	/**
	 * Fetch data from database for today birthdays
	 * 
	 * @return	object	when there are members todays birthday
	 * 			boolean	 false when no members with birthday are found
	 */
	public function getBirthdays()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->qn('#__tkdclub_members'));
		$query->where('DAY(' . $db->qn('birthdate') . ')' . ' = ' 
							 . 'DAY(CURDATE())');
		$query->where('MONTH(' . $db->qn('birthdate') . ')' . ' = ' 
		. 'MONTH(CURDATE())');

		$db->setQuery($query);

		return $db->loadObjectList() ? $db->loadObjectList() : false ;
	}

	private function getRecipients()
	{
		return true;
	}
}

JApplicationCli::getInstance('TkdclubBirthdayreminder')->execute();