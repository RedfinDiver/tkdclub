<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Medals;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * view-class for list view medals
 */
class HtmlView extends BaseHtmlView

{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    protected $allrows;
    protected $memberlist;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->memberlist = TkdclubHelper::getMemberlist();

        $this->togglestats = Factory::getSession()->get('togglestats_medals', null, 'tkdclub');

        if ($this->togglestats) {
            $this->medaldata = $this->get('Medaldata');
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('medal.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('medal.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.edit.state')) {
            ToolBarHelper::publish('medals.publish', 'JTOOLBAR_PUBLISH', true);
            ToolBarHelper::unpublish('medals.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_MEDAL_DELETE_QUESTION', 'medals.delete', 'JTOOLBAR_DELETE');
        }

        if ($this->togglestats) {
            ToolBarHelper::custom('medals.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        } else {
            ToolBarHelper::custom('medals.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        }

        if ($canDo->get('core.admin')) {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_tkdclub');
        }

        ToolBarHelper::divider();

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/erfolge.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => Text::_('COM_TKDCLUB_MEDAL_DATEWIN'),
        );
    }
}
