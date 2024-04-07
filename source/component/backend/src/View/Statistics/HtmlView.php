<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Statistics;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

Text::script('COM_TKDCLUB_MEMBER_STATE_ACTIVE');
Text::script('COM_TKDCLUB_MEMBER_STATE_INACTIVE');
Text::script('COM_TKDCLUB_MEMBER_STATE_SUPPORTER');
Text::script('COM_TKDCLUB_MEMBER_SEX_FEMALE');
Text::script('COM_TKDCLUB_MEMBER_SEX_MALE');
Text::script('COM_TKDCLUB_STATISTIC_STATE_ALL_MEMBERS_IN_DB');
Text::script('COM_TKDCLUB_STATISTIC_GENDER_DIST');
Text::script('COM_TKDCLUB_STATISTIC_AGE_DIST');
Text::script('COM_TKDCLUB_STATISTIC_TRAINTYPES_DIST');
Text::script('COM_TKDCLUB_STATISTIC_TRAININGS_PER_YEAR');
Text::script('COM_TKDCLUB_STATISTIC_AVERAGE_PARTICIPANTS');
Text::script('COM_TKDCLUB_STATISTIC_AS_TRAINER_MALE');
Text::script('COM_TKDCLUB_STATISTIC_AS_TRAINER_FEMALE');
Text::script('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_MALE');
Text::script('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_FEMALE');
Text::script('COM_TKDCLUB_SIDEBAR_TRAININGS');
Text::script('COM_TKDCLUB_TRAINING_NOT_PAID');
Text::script('COM_TKDCLUB_SUM');
Text::script('COM_TKDCLUB_TRAINING_PAY');
Text::script('JGLOBAL_NO_MATCHING_RESULTS');

/**
 * View class for statistics view
 */
class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        $this->addToolbar();
        parent::display($tpl);
    }

    public function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_STATISTIC_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.admin'))
        {
            ToolBarHelper::preferences('com_tkdclub');
        }

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/statistik.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
