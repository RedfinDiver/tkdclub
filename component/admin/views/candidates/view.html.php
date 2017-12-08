<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for edit-view: 'examparts'
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
        //allrows in the database
        $this->allrows = $this->get('Allrows');
        //all participants on a published exam, used to build filter-list
        $this->allparticipants = $this->get('Allparticipantsnames');

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
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));

        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_CANDIDATE_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('candidate.add', 'JTOOLBAR_NEW');
        }
        
        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('candidate.edit', 'JTOOLBAR_EDIT');
        }
        
        if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::publish('candidates.publish', 'COM_TKDCLUB_CANDIDATE_PASSED', true);
            JToolBarHelper::unpublish('candidates.unpublish', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', true);
            JToolbarHelper::archiveList('candidates.archive', 'COM_TKDCLUB_CANDIDATE_SUBSCRIBED', true);
        }

        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('COM_TKDCLUB_CANDIDATE_DELETE_QUESTION', 'candidates.delete','JTOOLBAR_DELETE');
        }
        
        $toolbar = JToolbar::getInstance('toolbar');
		$toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');
		$toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.candidates');
        
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_tkdclub');
        }
        
        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=candidates');   
        
        JToolbarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungsteilnehmer.html');

    }

    protected function getSortFields()
	{
		return array(
			'c.date' => JText::_('COM_TKDCLUB_EXAM_DATEEXAM'),
		);
	}
}