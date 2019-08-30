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
use Joomla\CMS\Form\FormHelper;

/**
 * view-class for view: trainings
 */
class TkdClubViewTrainings extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    protected $trainerdata;
    protected $trainingsdata;
    public    $togglestats;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->togglestats = Factory::getSession()->get('togglestats_trainings', null, 'tkdclub');
        $this->salaryparams = $this->get('Salaryparams');

        if ($this->togglestats) {
            $this->trainerdata = $this->get('Trainerdata');
            $this->trainingsdata = $this->get('Trainingsdata');
        }

        if (!$this->salaryparams && $this->togglestats) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_TRAINING_NO_SALARY_DEFINED'), 'warning');
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
        // Adding the fieldpath for the filters
        FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_ADMIN_VIEW'), 'tkdclub');

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::divider();

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('training.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolBarHelper::editList('training.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_TRAINING_DELETE_QUESTION', 'trainings.delete', 'JTOOLBAR_DELETE');
        }

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT . '/buttons');

        if ($this->togglestats) {
            ToolBarHelper::custom('trainings.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        } else {
            ToolBarHelper::custom('trainings.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        }

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.trainings');

        if ($canDo->get('core.admin')) {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=trainings');
        ToolBarHelper::divider();

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/trainings.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => Text::_('COM_TKDCLUB_TRAINING_DATE'),
        );
    }
}
