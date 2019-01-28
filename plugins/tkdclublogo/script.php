<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

use \Joomla\CMS\Filesystem\File;
use \Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class plgQuickiconTkdclublogoInstallerScript
{
    public function install($parent)
    {
        // Activate the plugin
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . ' = 1');
        $query->where($db->quoteName('element') . ' = ' . $db->quote('tkdclublogo'));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->query();
        $db->execute();
            
        echo '<p>'. JText::_('PLG_QUICKICON_TKDCLUBLOGO_ENABLED') .'</p>';
    }

    public function update($parent)
    {
        $this->deletePluginFolder();
        $this->removePluginFromDatabase();
    }

    /**
     * From 3.0.0 the plugin was renamed to tkdclublogo
     */
    public function deletePluginFolder()
    {
        $folders = array(
            '/plugins/quickicon/tkdclub'
        );

        foreach ($folders as $folder)
		{
			if (Folder::exists(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder))
			{
				echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder) . '<br />';
			}
		}
    }

    /**
     * Remove the old plugin from the database
     */
    public function removePluginFromDatabase()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->delete($db->quoteName('#__extensions'));
        $query->where($db->quoteName('name') . ' = ' . $db->quote('plg_quickicon_tkdclub'));

        $db->setQuery($query);
        $db->execute();
    }
}