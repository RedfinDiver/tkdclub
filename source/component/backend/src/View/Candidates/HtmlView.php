<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Candidates;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * view-class for edit-view: 'candidates'
 */
class HtmlView extends BaseHtmlView
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
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        // Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_CANDIDATE_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolBarHelper::addNew('candidate.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit.state'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);
            
            $childBar = $dropdown->getChildToolbar();

            $childBar->publish('candidates.publish', 'COM_TKDCLUB_CANDIDATE_PASSED')->listCheck(true);
            $childBar->unpublish('candidates.unpublish', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED')->listCheck(true);
            $childBar->archive('candidates.archive', 'COM_TKDCLUB_CANDIDATE_SUBSCRIBED')->listCheck(true);

            if ($canDo->get('core.delete'))
            {
                $childBar->delete('candidates.delete')
                ->text('JTOOLBAR_DELETE')
                ->message('COM_TKDCLUB_CANDIDATE_DELETE_QUESTION')
                ->listCheck(true);
            }
        }

        $export = $toolbar->dropdownButton('download-group')
		->text('COM_TKDCLUB_EXPORT')
		->toggleSplit(false)
		->icon('fa fa-file-download')
		->buttonClass('btn btn-action')
		->listCheck(false);

        $dlchild = $export->getChildToolbar();

        $dlchild->standardButton('download-all')
		->icon('fa fa-file-download')
		->text('COM_TKDCLUB_EXPORT_ALL_CSV')
		->task('export.candidates')
		->listCheck(false);

		$dlchild->standardButton('download-some')
		->icon('fa fa-file-download')
		->text('COM_TKDCLUB_EXPORT_CSV')
		->task('export.candidates')
		->listCheck(true);

        if ($canDo->get('core.admin')) {
            ToolBarHelper::preferences('com_tkdclub');
        }

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
