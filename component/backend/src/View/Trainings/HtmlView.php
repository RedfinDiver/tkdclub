<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Trainings;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View class for a list of trainings.
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
	 * Data for trainers
	 *
	 * @var  object
	 */
    protected $trainerdata;

    /**
	 * Data of all trainings
	 *
	 * @var  object
	 */
    protected $trainingsdata;

    /**
	 * Toggle to switch on/off statistics
	 *
	 * @var  integer
	 */
    public $togglestats;

    /**
	 * Salary parameters properly set in configuration
	 *
	 * @var  bool
	 */
    public $salaryparams;

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
        $this->total         = $this->get('Total');
        $this->allrows       = $this->get('Allrows');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->togglestats   = Factory::getSession()->get('togglestats_trainings', null, 'tkdclub');
        $this->salaryparams  = $this->get('Salaryparams');

        if ($this->togglestats)
        {
            $this->trainerdata = $this->get('Trainerdata');
            $this->trainingsdata = $this->get('Trainingsdata');
        }

        if (!$this->salaryparams && $this->togglestats) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_TRAINING_NO_SALARY_DEFINED'), 'warning');
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
        // Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_ADMIN_VIEW'), 'tkdclub tkdclub-logo-v-sw');

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

        if ($this->togglestats) {
            ToolBarHelper::custom('trainings.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);
        }
        else
        {
            ToolBarHelper::custom('trainings.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);
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
		->task('export.trainings')
		->listCheck(false);

		$dlchild->standardButton('download-html')
		->icon('fa fa-file-download')
		->text('COM_TKDCLUB_EXPORT_CSV')
		->task('export.trainings')
		->listCheck(true);

        if ($canDo->get('core.admin')) {
            ToolBarHelper::divider();
            ToolBarHelper::preferences('com_tkdclub');
        }

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/trainings.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
