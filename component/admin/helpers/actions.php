<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
* actions helper
*/
class TkdclubHelperActions
{  
    public static function getActions()
    {
        $user	= JFactory::getUser();
        $result	= new JObject;
        $assetName = 'com_tkdclub';
        $level = 'component';

        $actions = JAccess::getActions('com_tkdclub', $level);

        foreach ($actions as $action)
        {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }
}