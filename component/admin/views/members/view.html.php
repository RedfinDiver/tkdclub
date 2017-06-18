<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * View class for a list of members.
 *
 * @since  1.0
 */
class TkdClubViewMembers extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    
    //allrows in the database
    protected $allrows;

    /**
     * displays the view
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        
        //allrows in the database
        $this->allrows = $this->get('Allrows');

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
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name');
        $clubname == TRUE ? JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_ADMIN_VIEW'), 'tkdclub') 
                            : JToolBarHelper::title(JText::_('COM_TKDCLUB') . JText::_('COM_TKDCLUB_MEMBER_ADMIN_VIEW'), 'tkdclub');

        $canDo = TkdClubHelperActions::getActions();

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('member.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('member.edit', 'JTOOLBAR_EDIT');   
        }

        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('COM_TKDCLUB_MEMBER_DELETE_QUESTION', 'members.delete','JTOOLBAR_DELETE');
        }

        $toolbar = JToolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');
        $toolbar->appendButton('RawFormat',  'download', 'Export csv', 'export.members');

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_tkdclub');
        }

        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=members');
        JToolBarHelper::divider();
    }

    protected function getSortFields()
	{
		return array(
			'member_id' => JText::_('COM_TKDCLUB_MEMBER_ID'),
		);
	}
    
}