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
 * view-class of edit-view 'training'
 */
class TkdClubViewTraining extends HtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        if ($this->item->training_id == NULL) {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_NEW'), 'tkdclub');
        } else {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_CHANGE'), 'tkdclub');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('training.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('training.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2new('training.save2new');
        }

        ToolBarHelper::cancel('training.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
