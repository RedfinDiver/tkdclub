<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Events;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * view-class for list-view 'events'
 */
class EventsView extends HtmlView
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
        $this->togglestats = Factory::getSession()->get('togglestats_events', null, 'tkdclub');

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
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolbarHelper::title($clubname . JText::_('COM_TKDCLUB_EVENT_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('event.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('event.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList('COM_TKDCLUB_EVENT_DELETE_QUESTION', 'events.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('events.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('events.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');

        /* TODO statistics for events
        if ($this->togglestats)
        {
            ToolbarHelper::custom('events.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else 
        {
            ToolbarHelper::custom('events.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        } */

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.events');

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=events');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/veranstaltungen.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => JText::_('COM_TKDCLUB_PROMOTION_DATE'),
        );
    }
}
