<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Participants;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * view-class for view: 'participants'
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    protected $allrows;


    public function display($tpl = null)
    {

        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        // TODO statistics for participants
        // $this->togglestats = Factory::getSession()->get('togglestats_participants', null, 'tkdclub');
        //ordering and sorting
        $this->sortDirection = $this->state->get('list.direction');
        $this->sortColumn = $this->state->get('list.ordering');
        //filter forms
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_PARTICIPANT_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('participant.add');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('participant.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_PARTICIPANT_DELETE_QUESTION', 'participants.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo->get('core.edit.state')) {
            ToolBarHelper::publish('participants.publish', 'JTOOLBAR_PUBLISH', true);
            ToolBarHelper::unpublish('participants.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        $toolbar = ToolBar::getInstance('toolbar');

        if ($canDo->get('core.admin')) {
            $toolbar->appendButton('confirm', 'COM_TKDKLUB_PARTICIPANT_GDPR_DELETE_MESSAGE', 'flash', 'COM_TKDKLUB_PARTICIPANT_GDPR_DELETE', 'participants.delete_gdpr');
        }


        if ($canDo->get('core.admin')) {
            ToolBarHelper::preferences('com_tkdclub');
        }

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/teilnehmer.html';
        ToolBarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'b.date' => Text::_('COM_TKDCLUB_EVENT_DATE'),
        );
    }
}
