<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

JText::script('COM_TKDCLUB_MEMBER_STATE_ACTIVE');
JText::script('COM_TKDCLUB_MEMBER_STATE_INACTIVE');
JText::script('COM_TKDCLUB_MEMBER_STATE_SUPPORTER');
JText::script('COM_TKDCLUB_MEMBER_SEX_FEMALE');
JText::script('COM_TKDCLUB_MEMBER_SEX_MALE');
JText::script('COM_TKDCLUB_STATISTIC_STATE_ALL_MEMBERS_IN_DB');
JText::script('COM_TKDCLUB_STATISTIC_GENDER_DIST');
JText::script('COM_TKDCLUB_STATISTIC_AGE_DIST');
JText::script('COM_TKDCLUB_STATISTIC_TRAINTYPES_DIST');
JText::script('COM_TKDCLUB_STATISTIC_TRAININGS_PER_YEAR');
JText::script('COM_TKDCLUB_STATISTIC_AVERAGE_PARTICIPANTS');
JText::script('COM_TKDCLUB_STATISTIC_AS_TRAINER_MALE');
JText::script('COM_TKDCLUB_STATISTIC_AS_TRAINER_FEMALE');
JText::script('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_MALE');
JText::script('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_FEMALE');
JText::script('COM_TKDCLUB_SIDEBAR_TRAININGS');
JText::script('COM_TKDCLUB_TRAINING_NOT_PAID');
JText::script('COM_TKDCLUB_SUM');
JText::script('COM_TKDCLUB_TRAINING_PAY');
JText::script('JGLOBAL_NO_MATCHING_RESULTS');

/**
 * View class for statistics view
 */
class TkdClubViewStatistics extends HtmlView
{
    public function display($tpl = null)
    {
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_STATISTIC_ADMIN_VIEW'), 'tkdclub');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/statistik.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
