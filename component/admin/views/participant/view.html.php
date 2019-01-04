<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

/**
 * view-class for edit-view: 'participant'
 */
class TkdClubViewParticipant extends JViewLegacy
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
        
        if ($this->item->id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PARTICIPANT_NEW_TITLE'), 'tkdclub');
            
        } else {
            
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PARTICIPANT_EDIT_TITLE'), 'tkdclub');
        }
      
        $canDo = TkdClubHelperActions::getActions();
 
        JToolBarHelper::apply('participant.apply', 'JTOOLBAR_APPLY');
        
        JToolBarHelper::save('participant.save', 'JTOOLBAR_SAVE');
        
        if ($canDo->get('core.create'))
        {
            JToolBarHelper::save2new('participant.save2new');
        }
        
        JToolBarHelper::cancel('participant.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/teilnehmer.html';
        JToolbarHelper::help( '', false, $help_url );
    }
}