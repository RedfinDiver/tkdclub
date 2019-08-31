<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * view-class for edit-view: 'candidates'
 */
class TkdClubViewCandidates extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;

    //allrows in the database
    protected $allrows;

    //all participants on a published exam, used to build filter-list
    protected $allparticipants;

    //all published exams, used to build filter-list
    protected $allexams;


    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows'); // Allrows in the database
        $this->allparticipants = $this->get('Allparticipantsnames'); //all candidates on a published promotions, used to build filter-list

        /* TODO statistics for candidates
        
        $this->togglestats = Factory::getSession()->get('togglestats_candidates', null, 'tkdclub');
        
        if ($this->togglestats)
        {
            $this->candidatedata = $this->get('Candidatedata');
        } */

        //sorting and ordering
        $this->sortDirection = $this->state->get('list.direction');
        $this->sortColumn = $this->state->get('list.ordering');

        // Get filter form.
        $this->filterForm = $this->get('FilterForm');

        // Get active filters.
        $this->activeFilters = $this->get('ActiveFilters');

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_CANDIDATE_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('candidate.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('candidate.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.edit.state')) {
            ToolBarHelper::publish('candidates.publish', 'COM_TKDCLUB_CANDIDATE_PASSED', true);
            ToolBarHelper::unpublish('candidates.unpublish', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', true);
            ToolBarHelper::archiveList('candidates.archive', 'COM_TKDCLUB_CANDIDATE_SUBSCRIBED', true);
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_CANDIDATE_DELETE_QUESTION', 'candidates.delete', 'JTOOLBAR_DELETE');
        }

        /* TODO statistics for candidates
        
        if ($this->togglestats)
        {ToolBarHelper::custom('candidates.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);}
        else {ToolBarHelper::custom('candidates.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);} */

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');
        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.candidates');

        if ($canDo->get('core.admin')) {
            ToolBarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=candidates');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/pruefungsteilnehmer.html';
        ToolBarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'c.date' => Text::_('COM_TKDCLUB_EXAM_DATEEXAM'),
        );
    }
}
