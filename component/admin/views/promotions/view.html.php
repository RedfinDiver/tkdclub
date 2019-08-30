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
 * view-class for view: 'promotions'
 */
class TkdClubViewPromotions extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    protected $allrows;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        /* TODO mini statistics for promotions
        $this->togglestats = Factory::getSession()->get('togglestats_promotions', null, 'tkdclub');
        
        if ($this->togglestats)
        {
            $this->promotiondata = $this->get('Promotionsdata');
        } */

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_PROMOTION_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::divider();

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('promotion.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('promotion.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.edit.state')) {
            ToolBarHelper::publish('promotions.publish', 'JTOOLBAR_CHECKIN', true);
            ToolBarHelper::unpublish('promotions.unpublish', 'COM_TKDCLUB_PROMOTION_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_PROMOTION_DELETE_QUESTION', 'promotions.delete', 'JTOOLBAR_DELETE', true);
        }

        if ($canDo->get('core.admin')) {
            ToolBarHelper::preferences('com_tkdclub');
        }

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');

        // TODO mini statistiks for promotions
        /* if ($this->togglestats)
        {
            ToolBarHelper::custom('promotions.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else
        {
            ToolBarHelper::custom('promotions.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        } */

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.promotions');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/pruefungen.html';
        JToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => Text::_('COM_TKDCLUB_PROMOTION_DATE'),
        );
    }
}
