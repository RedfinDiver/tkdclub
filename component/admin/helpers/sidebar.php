<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
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

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_TRAININGS'), 'index.php?option=com_tkdclub&view=trainings', $vName == 'trainings');

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_MEDALS'), 'index.php?option=com_tkdclub&view=medals', $vName == 'medals');

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_PROMOTIONS'), 'index.php?option=com_tkdclub&view=promotions', $vName == 'promotions');
        
        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_CANDIDATES'), 'index.php?option=com_tkdclub&view=candidates', $vName == 'candidates');

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_EVENTS'), 'index.php?option=com_tkdclub&view=events', $vName == 'events');
        
        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_PARTICIPANTS'), 'index.php?option=com_tkdclub&view=participants', $vName == 'participants');

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_EMAIL'), 'index.php?option=com_tkdclub&view=email', $vName == 'email');

        JHtmlSidebar::addEntry(
            JText::_('COM_TKDCLUB_SIDEBAR_STATISTICS'), 'index.php?option=com_tkdclub&view=statistics', $vName == 'statistics');
    }
}