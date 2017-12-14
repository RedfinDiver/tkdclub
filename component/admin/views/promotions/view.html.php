<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for view: 'promotions'
 */
class TkdClubViewPromotions extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $total;
    protected $allrows;

    public function display($tpl = null)
    {
        
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->togglestats = JFactory::getSession()->get('togglestats_promotions', null, 'tkdclub');
        
        if ($this->togglestats)
        {
            $this->memberdata = $this->get('Promotionsdata');
        }
        
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));
        
        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PROMOTION_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();
        
        JToolBarHelper::divider();
        
        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('promotion.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('promotion.edit', 'JTOOLBAR_EDIT');   
        }

        if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::publish('promotions.publish', 'JTOOLBAR_CHECKIN', true);
            JToolBarHelper::unpublish('promotions.unpublish', 'COM_TKDCLUB_PROMOTION_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('COM_TKDCLUB_PROMOTION_DELETE_QUESTION', 'promotions.delete','JTOOLBAR_DELETE', true);
        }
        
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_tkdclub');
        }

        $toolbar = JToolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');

        if ($this->togglestats)
        {JToolBarHelper::custom('promotions.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);}
        else {JToolBarHelper::custom('promotions.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);}

        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.promotions');
        
        JToolbarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungen.html');
    }

    protected function getSortFields()
	{
		return array(
			'date' => JText::_('COM_TKDCLUB_PROMOTION_DATE'),
		);
	}
}