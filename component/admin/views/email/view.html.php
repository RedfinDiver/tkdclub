<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JText::script('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_SUBJECT');
JText::script('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_MESSAGE');

/**
 * View class for email view
 */
class TkdClubViewEmail extends JViewLegacy
{
    
    protected $form;
    
    protected $email_test;
    
    public function display($tpl = null)
    {
        // Get data from the model
		$this->form = $this->get('Form');

		$this->addToolbar();
                $this->getTestmail();
                $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
        
    }
    
    protected function addToolbar()
    {
		JFactory::getApplication()->input->set('hidemainmenu', false);
        
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));
        JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_EMAIL_ADMIN_VIEW'), 'tkdclub');

        JToolbarHelper::custom('email.send', 'envelope.png', 'send_f2.png', 'COM_TKDCLUB_EMAIL_TOOLBAR_SEND', false);
		JToolbarHelper::divider();
        JToolbarHelper::preferences('com_tkdclub');
        
        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/email.html';
        JToolbarHelper::help( '', false, $help_url );
    }
    
    protected function getTestmail()
    {
        $this->email_test = JComponentHelper::getParams('com_tkdclub')->get('email_test');
    }
           
}