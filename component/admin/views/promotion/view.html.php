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
 * view-class for edit-view: 'promotion'
 */
class TkdClubViewPromotion extends HtmlView
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

        if ($this->item->promotion_id == NULL) {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_PROMOTION_NEW_TITLE'), 'tkdclub');
        } else {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_PROMOTION_EDIT_TITLE'), 'tkdclub');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('promotion.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('promotion.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2copy('promotion.save2copy');
        }

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2new('promotion.save2new');
        }


        ToolBarHelper::cancel('promotion.cancel', 'JTOOLBAR_CANCEL');

        ToolBarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungen.html');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/pruefungen.html';
        ToolBarHelper::help('', false, $help_url);
    }
}
