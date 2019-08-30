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
use Joomla\CMS\Text;

/**
 * View class for a list of members.
 *
 * @since  1.0
 */
class TkdClubViewMembers extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;

    //allrows in the database
    protected $allrows;

    /**
     * displays the view
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->togglestats = Factory::getSession()->get('togglestats_members', null, 'tkdclub');

        if ($this->togglestats) {
            $this->memberdata = $this->get('Memberdata');
        }

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('member.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('member.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_MEMBER_DELETE_QUESTION', 'members.delete', 'JTOOLBAR_DELETE');
        }

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');

        if ($this->togglestats) {
            ToolBarHelper::custom('members.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        } else {
            ToolBarHelper::custom('members.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        }

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.members');

        if ($canDo->get('core.admin')) {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=members');
        ToolBarHelper::divider();

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'member_id' => Text::_('COM_TKDCLUB_MEMBER_ID'),
        );
    }
}
