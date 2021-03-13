<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Medal;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * view-class for edit-view: 'medal'
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
        $clubname   = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        $isNew      = ($this->item->medal_id == 0);

        if ($isNew)
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_NEW'), 'tkdclub');
        }
        else
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_CHANGE'), 'tkdclub');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('medal.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('medal.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2copy('medal.save2copy');
        }

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2new('medal.save2new');
        }

        ToolBarHelper::cancel('medal.cancel', 'JTOOLBAR_CANCEL');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/erfolge.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
