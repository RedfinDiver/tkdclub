<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class plgUserTkdclubmemberInstallerScript
{
    function install ($parent)
    {
        // Activate the plugin
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . ' = 1');
        $query->where($db->quoteName('element') . ' = ' . $db->quote('tkdclubmember'));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->query();
        $db->execute();
        
        echo '<p>'. JText::_('PLG_USER_TKDCLUBMEMBER_ENABLED') .'</p>';
    }
}