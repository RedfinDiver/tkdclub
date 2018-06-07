<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdClubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for edit-view: 'event'
 */
class TkdClubViewEvent extends JViewLegacy
{
    protected $item;
    protected $form;
    
    public function display($tpl = null)
            
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        
        $this->addToolbar();
        parent::display($tpl);
    }
    
    protected function addToolbar()
            
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));

        if ($this->item->event_id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_EVENT_NEW_TITLE'), 'tkdclub');
        }
        else
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_EVENT_EDIT_TITLE'), 'tkdclub');
        }
      
        $canDo = TkdClubHelperActions::getActions();
 
        JToolBarHelper::apply('event.apply', 'JTOOLBAR_APPLY');
        
        JToolBarHelper::save('event.save', 'JTOOLBAR_SAVE');
        
        if ($canDo->get('core.create'))
        {
            JToolBarHelper::save2copy('event.save2copy');
        }
        
        if ($canDo->get('core.create'))
        {
            JToolBarHelper::save2new('event.save2new');
        }
        
        JToolBarHelper::cancel('event.cancel', 'JTOOLBAR_CANCEL');
    }
}