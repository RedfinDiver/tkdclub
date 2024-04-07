<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;

return new class () implements InstallerScriptInterface {

    public function install(InstallerAdapter $parent): bool
    {
        // Activate the plugin
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . ' = 1');
        $query->where($db->quoteName('element') . ' = ' . $db->quote('bdreminder'));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
        
        echo '<p>'. Text::_('PLG_CONSOLE_BDREMINDER_ENABLED') .'</p>';

        return true;
    }

    public function update(InstallerAdapter $parent): bool
    {
        return true;
    }

    public function uninstall(InstallerAdapter $parent): bool
    {
        return true;
    }

    public function preflight(string $type, InstallerAdapter $parent): bool
    {
        return true;
    }

    public function postflight(string $type, InstallerAdapter $parent): bool
    {
        return true;
    }
};