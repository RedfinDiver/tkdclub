<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for view: trainings
 */
class TkdClubViewTrainings extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    //allrows in the database
    protected $allrows;
    protected $trainings;
    protected $trainingspart;
 

    public function display($tpl = null)
    {
        
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        //allrows in the database
        $this->allrows = $this->get('Allrows');
        $this->trainings = $this->get('Trainings');
	    $this->trainingspart = $this->get('Trainingspart');
        $this->trainerdata = $this->get('Trainerdata');
        $this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

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
        JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name');
        $clubname == TRUE ? JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_TRAINING_ADMIN_VIEW'), 'tkdclub') 
                          : JToolBarHelper::title(JText::_('COM_TKDCLUB') . JText::_('COM_TKDCLUB_TRAINING_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();

        JToolBarHelper::divider();
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::addNew('training.add', 'JTOOLBAR_NEW');}
        
        if ($canDo->get('core.edit'))
        {JToolBarHelper::editList('training.edit', 'JTOOLBAR_EDIT');}
        
        if ($canDo->get('core.delete'))
        {JToolBarHelper::deleteList('COM_TKDCLUB_TRAINING_DELETE_QUESTION', 'trainings.delete','JTOOLBAR_DELETE');}
        
        JToolBarHelper::publish('trainings.publish', 'COM_TKDCLUB_TRAINING_PAID', true);
        JToolBarHelper::unpublish('trainings.unpublish', 'COM_TKDCLUB_TRAINING_NOT_PAID', true);

        $toolbar = JToolbar::getInstance('toolbar');
		$toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');
		$toolbar->appendButton('RawFormat',  'download', 'Export csv', 'export.trainings');
        
        if ($canDo->get('core.admin'))
        {   JToolBarHelper::divider();
            JToolBarHelper::preferences('com_tkdclub');}
            
        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=trainings');
        JToolBarHelper::divider();    
                        
    }

    protected function getSortFields()
	{
		return array(
			'date' => JText::_('COM_TKDCLUB_TRAINING_DATE'),
		);
	}
}