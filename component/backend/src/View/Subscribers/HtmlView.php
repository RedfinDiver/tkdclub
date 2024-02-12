<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Subscribers;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\MVC\View\HtmlView;

class SubscribersView extends HtmlView
{
    protected $items;
    // public    $togglestats;


    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->addToolbar();
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');

        /* if ($this->togglestats)
        {
            $this->trainerdata = $this->get('Trainerdata');
            $this->trainingsdata = $this->get('Trainingsdata');
        } */

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        // Adding the fieldpath for the filters
        FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolbarHelper::title($clubname . Text::_('COM_TKDCLUB_SUBSCRIBER_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolbarHelper::divider();

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('subscriber.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('subscriber.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList('COM_TKDCLUB_SUBSCRIBER_DELETE_QUESTION', 'subscribers.delete', 'JTOOLBAR_DELETE');
        }

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');

        /* if ($this->togglestats)
        {ToolbarHelper::custom('subscribers.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);}
        else {ToolbarHelper::custom('subscribers.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);} */

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.subscribers');

        if ($canDo->get('core.admin')) {
            ToolbarHelper::divider();
            ToolbarHelper::preferences('com_tkdclub');
        }

        ToolbarHelper::divider();

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/newsletterabos.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => Text::_('COM_TKDCLUB_SUBSCRIBER_SUBSCRIBED'),
        );
    }
}
