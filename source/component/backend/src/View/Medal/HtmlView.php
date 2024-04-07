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
   /**
	 * The Form object
	 *
	 * @var    Form
	 * 
	 */
    protected $form;

    /**
	 * The active item
	 *
	 * @var    object
	 * 
	 */
    protected $item;

    /**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 */
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
    protected function addToolbar()
    {
        // No menu in edit view
        Factory::getApplication()->input->set('hidemainmenu', true);

        $clubname   = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));
        $isNew      = ($this->item->id == 0);

        $isNew ? ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_NEW'), 'tkdclub tkdclub-logo-v-sw') :
                    ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_MEDAL_CHANGE'), 'tkdclub tkdclub-logo-v-sw');

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('medal.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('medal.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2copy('medal.save2copy');
        }

        if ($canDo->get('core.create')) {
            ToolBarHelper::save2new('medal.save2new');
        }

        ToolBarHelper::cancel('medal.cancel', 'JTOOLBAR_CLOSE');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/erfolge.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
