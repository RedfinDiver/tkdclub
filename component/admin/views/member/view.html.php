<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for a edit screen for one member.
 *
 */
class TkdClubViewMember extends HtmlView
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
        Factory::getApplication()->input->set('hidemainmenu', true);

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
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        if ($this->item->member_id == NULL) {
            ToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_NEW_TITLE'), 'tkdclub');
        } else {
            ToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEMBER_EDIT_TITLE'), 'tkdclub');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('member.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('member.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2new('member.save2new');
        }

        ToolBarHelper::cancel('member.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
