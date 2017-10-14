<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');
JLoader::register('TkdclubHelperMembers', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/members.php');

/**
 * view-class for list view medals
 */
class TkdClubViewMedals extends JViewLegacy

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
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');

        $helper = new TkdClubHelperMembers();
        $this->memberlist = $helper->getMemberlist();

        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->togglestats = 0;
        
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name');
        $clubname == TRUE ? JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEDAL_ADMIN_VIEW'), 'tkdclub') 
                          : JToolBarHelper::title(JText::_('COM_TKDCLUB') . JText::_('COM_TKDCLUB_MEDAL_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::addNew('medal.add', 'JTOOLBAR_NEW');}
        
        if ($canDo->get('core.edit'))
        {JToolBarHelper::editList('medal.edit', 'JTOOLBAR_EDIT');}
        
        if ($canDo->get('core.delete'))
        {JToolBarHelper::deleteList('', 'medals.delete','JTOOLBAR_DELETE');}
        
        $toolbar = JToolbar::getInstance('toolbar');
		$toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');
		$toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.medals');
        
        if ($canDo->get('core.admin'))
        {   JToolBarHelper::divider();
            JToolBarHelper::preferences('com_tkdclub');}
            
        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=medals');
        JToolBarHelper::divider();    
        
    }

    protected function getSortFields()
	{
		return array(
			'date' => JText::_('COM_TKDCLUB_MEDAL_DATEWIN'),
		);
	}
}