<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for view: 'participants'
 */
class TkdClubViewParticipants extends JViewLegacy
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
        // $this->togglestats = JFactory::getSession()->get('togglestats_participants', null, 'tkdclub');
        //ordering and sorting
        $this->sortDirection = $this->state->get('list.direction');
        $this->sortColumn = $this->state->get('list.ordering');
        //filter forms
        $this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));
        
        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PARTICIPANT_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('participant.add');
        }
        
        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('participant.edit', 'JTOOLBAR_EDIT');
        }
        
        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('COM_TKDCLUB_PARTICIPANT_DELETE_QUESTION', 'participants.delete', 'JTOOLBAR_DELETE');
        }
        
        if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::publish('participants.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('participants.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        
        $toolbar = JToolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');

        if ($canDo->get('core.admin'))
        {
            $toolbar->appendButton('Delgdpr', 'COM_TKDKLUB_PARTICIPANT_GDPR_DELETE_MESSAGE', 'flash', 'COM_TKDKLUB_PARTICIPANT_GDPR_DELETE', 'participants.delete_gdpr');
        }

        /* // TODO statistics for participants
        if ($this->togglestats)
        {
            JToolBarHelper::custom('participants.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else
        {
            JToolBarHelper::custom('participants.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        } */

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.participants');
        
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=participants');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/teilnehmer.html';
        JToolbarHelper::help( '', false, $help_url );
    }

    protected function getSortFields()
	{
		return array(
			'b.date' => JText::_('COM_TKDCLUB_EVENT_DATE'),
		);
	}

}