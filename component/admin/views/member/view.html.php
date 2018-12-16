<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * View class for a edit screen for one member.
 *
 */
class TkdClubViewMember extends JViewLegacy
{
    protected $item;
    protected $form;
    protected $attachments;
    protected $memberpicture;
    protected $medals;

    /**
	 * Display the view
	 */
    public function display($tpl = null)
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->attachments = $this->get('Attachments');
        $this->memberpicture = $this->getModel('member')->getAttachments($picture = true);
        $this->medals = $this->get('Medals');

        $this->addToolbar();
        parent::display($tpl);
    }
    /**
	 * Add the page title and toolbar.
	 *
	 */
    protected function addToolbar()
    {
        $clubname = JComponentHelper::getParams('com_tkdclub')->get('club_name', JText::_('COM_TKDCLUB'));

        if ($this->item->member_id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_NEW_TITLE'), 'tkdclub');
            
        }
        else
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_EDIT_TITLE'), 'tkdclub');
        }

        $canDo = TkdClubHelperActions::getActions();

        JToolBarHelper::apply('member.apply', 'JTOOLBAR_APPLY');

        JToolBarHelper::save('member.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::save2copy('member.save2copy');               
        }

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::save2new('member.save2new');
        }

        JToolBarHelper::cancel('member.cancel', 'JTOOLBAR_CANCEL');
    }
}