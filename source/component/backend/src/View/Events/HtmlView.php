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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * view-class for list-view 'events'
 */
class HtmlView extends BaseHtmlView
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

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $toolbar = Toolbar::getInstance('toolbar');

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_EVENT_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('event.add', 'JTOOLBAR_NEW');
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

            if ($canDo->get('core.edit.state')) {
                
                $childBar->publish('events.publish')->listCheck(true);
                $childBar->unpublish('events.unpublish')->listCheck(true);

            }
            
            if ($canDo->get('core.delete'))
            {
                $childBar->delete('events.delete')
                ->text('JTOOLBAR_DELETE')
                ->message('COM_TKDCLUB_EVENT_DELETE_QUESTION')
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
		->task('export.events')
		->listCheck(false);

		$dlchild->standardButton('download-some')
		->icon('fa fa-file-download')
		->text('COM_TKDCLUB_EXPORT_CSV')
		->task('export.events')
		->listCheck(true);

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_tkdclub');
        }

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/veranstaltungen.html';
        ToolbarHelper::help('', false, $help_url);
    }

    protected function getSortFields()
    {
        return array(
            'date' => Text::_('COM_TKDCLUB_PROMOTION_DATE'),
        );
    }
}
