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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View class for a list of medals.
 *
 */
class HtmlView extends BaseHtmlView
{
    /**
	 * An array of items
	 *
	 * @var  array
	 */
    protected $items;

    /**
	 * The pagination object
	 *
	 * @var  \JPagination
	 */
    protected $pagination;

    /**
	 * The model state
	 *
	 * @var  \JObject
	 */
    protected $state;

    /**
	 * Number of all visible rows in view
	 *
	 * @var  integer
	 */
    protected $total;

    /**
	 * Number of all rows in table
	 *
	 * @var  integer
	 */
    protected $allrows;

    /**
	 * An array of member ids and names
	 *
	 * @var  array
	 */
    protected $memberlist;

    /**
	 * An array of data for statistics
	 *
	 * @var  array
	 */
    protected $medaldata;

    /**
	 * Form object for search filters
	 *
	 * @var  \JForm
	 */
	public $filterForm;


	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

    /**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total         = $this->get('Total');
        $this->allrows       = $this->get('Allrows');
        $this->memberlist    = TkdclubHelper::getMemberlist();
        $this->medaldata     = $this->get('Medaldata');


        $this->addToolbar();
        parent::display($tpl);
    }

    /**
	 * Add the page title and toolbar.
	 *
	 * @return  void
     * 
	 */
    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');
        $toolbar = Toolbar::getInstance('toolbar');

        if ($canDo->get('core.create'))
        {
            $toolbar->addNew('medal.add', 'JTOOLBAR_NEW');
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

            $childBar->publish('medals.publish')->listCheck(true);
            $childBar->unpublish('medals.unpublish')->listCheck(true);

            if ($canDo->get('core.delete'))
            {
                $childBar->delete('medals.delete')
                    ->text('JTOOLBAR_DELETE')
                    ->message('COM_TKDCLUB_MEDAL_DELETE_QUESTION')
                    ->listCheck(true);
            }

        }

        ToolBarHelper::custom('medalsstats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);

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
		->task('export.medals')
		->listCheck(false);

		$dlchild->standardButton('download-html')
		->icon('fa fa-file-download')
		->text('COM_TKDCLUB_EXPORT_CSV')
		->task('export.medals')
		->listCheck(true);

        if ($canDo->get('core.admin'))
        {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_tkdclub');
        }

        ToolBarHelper::divider();

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/erfolge.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
