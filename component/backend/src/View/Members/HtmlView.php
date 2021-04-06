<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Members;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View class for a list of members.
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

    /**
	 * Number of all rows in table
	 *
	 * @var  integer
	 */
    protected $allrows;

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
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->total         = $this->get('Total');
        $this->allrows       = $this->get('Allrows');
        $this->togglestats   = Factory::getSession()->get('togglestats_members', null, 'tkdclub');

        if ($this->togglestats)
        {
            $this->memberdata = $this->get('Memberdata');
        }

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
        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEMBER_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        if ($canDo->get('core.create'))
        {
            ToolBarHelper::addNew('member.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.delete')) {
            ToolBarHelper::deleteList('COM_TKDCLUB_MEMBER_DELETE_QUESTION', 'members.delete', 'JTOOLBAR_DELETE');
        }

        if ($this->togglestats)
        {
            ToolBarHelper::custom('members.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else
        {
            ToolBarHelper::custom('members.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
        }

        ToolbarHelper::custom('export.members', 'download', '', 'COM_TKDCLUB_EXPORT_CSV', true);
        ToolbarHelper::custom('export.members', 'download', '', 'COM_TKDCLUB_EXPORT_ALL_CSV', false);
       
        if ($canDo->get('core.admin'))
        {
            ToolBarHelper::preferences('com_tkdclub');
        }

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
