<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * View-class of edit-screen 'candidate'
 */
class TkdClubViewCandidate extends JViewLegacy
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
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_CANDIDATE_NEW_TITLE'), 'tkdclub');
        }
        else
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_CANDIDATE_EDIT_TITLE'), 'tkdclub');
        }
      
        $canDo = TkdClubHelperActions::getActions();
        
        JToolBarHelper::apply('candidate.apply', 'JTOOLBAR_APPLY');

        JToolBarHelper::save('candidate.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create'))
        {JToolBarHelper::save2new('candidate.save2new');}

        JToolBarHelper::cancel('candidate.cancel', 'JTOOLBAR_CANCEL');
        
        JToolbarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungsteilnehmer.html');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/pruefungsteilnehmer.html';
        JToolbarHelper::help( '', false, $help_url );
    }
    
}