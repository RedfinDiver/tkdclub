<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
/**
* sidebar helper
*/
class TkdclubHelperSidebar
{
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_MEMBERS'), 'index.php?option=com_tkdclub&view=members', $vName == 'members');
    }
}