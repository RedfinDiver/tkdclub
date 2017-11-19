<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * view-class for view: 'promotions'
 */
class TkdClubViewPromotions extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    //allrows in the database
    protected $allrows;
    //all participants of published exams, used for building select-list for filter
    protected $allparticipants;
    //all exam-dates of published exams, used for building select-list for filter
    protected $allexamdates;

    public function display($tpl = null)
    {
        
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        //allrows in the database
        $this->allrows = $this->get('Allrows');
        //all participants of published exams, used for building select-list for filter
        $this->allparticipants = $this->get('Allparticipantsnames');
        //all exam-dates of published exams, used for building select-list for filter
        $this->allexamdates = $this->get('AllExamDates');
        
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name');
        $clubname == TRUE ? JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_ADMIN_EXAMS'), 'exams.png') 
                          : JToolBarHelper::title(JText::_('COM_TKD_CLUB') . JText::_('COM_TKDCLUB_ADMIN_EXAMS'), 'exams.png');
        
        $canDo = TkdClubHelper::getActions();
        
        JToolBarHelper::divider();
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::addNew('exam.add');}
        
        if ($canDo->get('core.edit'))
        {JToolBarHelper::editList('exam.edit','COM_TKDCLUB_TOOLBAR_EXAM_EDITEXAM');}
        
        if ($canDo->get('core.delete'))
        {JToolBarHelper::deleteList('', 'exams.delete', 'COM_TKDCLUB_TOOLBAR_EXAM_DELETE');}
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::publish('exams.publish', 'COM_TKDCLUB_TOOLBAR_EXAM_PUBLISH');}
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::archiveList('exams.archive', 'COM_TKDCLUB_TOOLBAR_EXAM_ARCHIVE');}
        
        if ($canDo->get('core.admin'))
        {JToolBarHelper::preferences('com_tkdclub');}
        
        JToolBarHelper::back('COM_TKDCLUB_TOOLBAR_BACK_TO_PARTS', 'index.php?option=com_tkdclub&view=examparts');
        
        JToolbarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungen.html');
    }
}