<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

/**
 * view-class for edit-view: 'participant'
 */
class TkdClubViewParticipant extends HtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        if ($this->item->id == NULL) {
            ToolbarHelper::title($clubname . Text::_('COM_TKDCLUB_PARTICIPANT_NEW_TITLE'), 'tkdclub');
        } else {

            ToolbarHelper::title($clubname . Text::_('COM_TKDCLUB_PARTICIPANT_EDIT_TITLE'), 'tkdclub');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolbarHelper::apply('participant.apply', 'JTOOLBAR_APPLY');

        ToolbarHelper::save('participant.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolbarHelper::save2new('participant.save2new');
        }

        ToolbarHelper::cancel('participant.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/teilnehmer.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
