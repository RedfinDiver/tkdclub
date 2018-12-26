<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class of edit-view 'training'
 */
class TkdClubViewTraining extends JViewLegacy
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
        
        if ($this->item->training_id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_TRAINING_NEW'), 'tkdclub');
        }
        else 
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_TRAINING_CHANGE'), 'tkdclub');
        }
      
        $canDo = TkdClubHelperActions::getActions();
        
        JToolBarHelper::apply('training.apply', 'JTOOLBAR_APPLY');
        
        JToolBarHelper::save('training.save', 'JTOOLBAR_SAVE');
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2copy('training.save2copy');}
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2new('training.save2new');}
        
        JToolBarHelper::cancel('training.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        JToolbarHelper::help( '', false, $help_url );
    }
}