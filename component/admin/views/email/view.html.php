<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Text::script('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_SUBJECT');
Text::script('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_MESSAGE');

/**
 * View class for email view
 */
class TkdClubViewEmail extends JViewLegacy
{

    protected $form;

    protected $email_test;

    public function display($tpl = null)
    {
        // Get data from the model
        $this->form = $this->get('Form');

        $this->addToolbar();
        $this->getTestmail();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', false);

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_EMAIL_ADMIN_VIEW'), 'tkdclub');

        ToolBarHelper::custom('email.send', 'envelope.png', 'send_f2.png', 'COM_TKDCLUB_EMAIL_TOOLBAR_SEND', false);
        ToolBarHelper::divider();
        ToolBarHelper::preferences('com_tkdclub');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/email.html';
        ToolBarHelper::help('', false, $help_url);
    }

    protected function getTestmail()
    {
        $this->email_test = ComponentHelper::getParams('com_tkdclub')->get('email_test');
    }
}
