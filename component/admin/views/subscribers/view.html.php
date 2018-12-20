<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

class TkdClubViewSubscribers extends JViewLegacy
{
    protected $items;
    // public    $togglestats;


    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->sidebar = JHtmlSidebar::render();
        $this->addToolbar();
        
        /* if ($this->togglestats)
        {
            $this->trainerdata = $this->get('Trainerdata');
            $this->trainingsdata = $this->get('Trainingsdata');
        } */
        
        parent::display($tpl);
    }

    /**
	 * Add the page title and toolbar.
	 */   
    protected function addToolbar()
    {
        // Adding the fieldpath for the filters
        JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));
        
        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_SUBSCRIBER_ADMIN_VIEW'), 'tkdclub');
        
        $canDo = TkdClubHelperActions::getActions();

        JToolBarHelper::divider();
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::addNew('subscriber.add', 'JTOOLBAR_NEW');}
        
        if ($canDo->get('core.edit'))
        {JToolBarHelper::editList('subscriber.edit', 'JTOOLBAR_EDIT');}
        
        if ($canDo->get('core.delete'))
        {JToolBarHelper::deleteList('COM_TKDCLUB_TRAINING_DELETE_QUESTION', 'subscribers.delete','JTOOLBAR_DELETE');}

        $toolbar = JToolbar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.'/buttons');

        /* if ($this->togglestats)
        {JToolBarHelper::custom('subscribers.togglestats', 'eye-close', 'eye-close', 'COM_TKDCLUB_BUTTON_STATS', false);}
        else {JToolBarHelper::custom('subscribers.togglestats', 'eye-open', 'eye-open', 'COM_TKDCLUB_BUTTON_STATS', false);} */
        
        $toolbar->appendButton('RawFormat',  'download', 'COM_TKDCLUB_BUTTON_EXPORT', 'export.subscribers');
        
        if ($canDo->get('core.admin'))
        {   JToolBarHelper::divider();
            JToolBarHelper::preferences('com_tkdclub');}
            
        JHtmlSidebar::setAction('index.php?option=com_tkdclub&view=subscribers');
        JToolBarHelper::divider();    
                        
    }

}