<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;


/**
 * view-class for edit-view: 'event'
 */
class HtmlView extends BaseHtmlView
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

        if ($this->item->id == NULL)
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_EVENT_NEW_TITLE'), 'tkdclub tkdclub-logo-v-sw');
        }
        else
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_EVENT_EDIT_TITLE'), 'tkdclub tkdclub-logo-v-sw');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolbarHelper::apply('event.apply', 'JTOOLBAR_APPLY');

        ToolbarHelper::save('event.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolbarHelper::save2new('event.save2new');
        }

        ToolbarHelper::cancel('event.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/veranstaltungen.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
