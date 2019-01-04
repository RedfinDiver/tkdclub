<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for list-view 'events'
 */
class TkdClubViewEvents extends JViewLegacy
{
    protected $items;
    protected $state;
    protected $pagination;
    protected $total;
    protected $allrows;
 
    public function display($tpl = null)
    {
        
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->togglestats = JFactory::getSession()->get('togglestats_events', null, 'tkdclub');
        
        // TODO statistics for events
        /* if ($this->togglestats)
        {
            // @todo implement statistics
        } */

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    protected function addToolbar()  
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));
    
        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_EVENT_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('event.add', 'JTOOLBAR_NEW');
        }
        
        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('event.edit', 'JTOOLBAR_EDIT');
        }
        
        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('COM_TKDCLUB_EVENT_DELETE_QUESTION', 'events.delete','JTOOLBAR_DELETE');
        }
        
        if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::publish('events.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('events.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        $toolbar = JToolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');

        /* TODO statistics for events
        if ($this->togglestats)
        {
            JToolBarHelper::custom('events.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else 
        {
            JToolBarHelper::custom('events.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        } */

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.events');
        
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_tkdclub');
        }
        
        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=events');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/veranstaltungen.html';
        JToolbarHelper::help( '', false, $help_url );
    }

    protected function getSortFields()
	{
		return array(
			'date' => JText::_('COM_TKDCLUB_PROMOTION_DATE'),
		);
	}
}