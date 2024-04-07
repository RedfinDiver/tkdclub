<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\View\Training;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View to edit a training.
 * 
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
	 * @throws  Exception
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

        $clubname = ComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB'));

        if ($this->item->id == NULL)
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_NEW'), 'tkdclub tkdclub-logo-v-sw');
        }
        else
        {
            ToolBarHelper::title($clubname . Text::_('COM_TKDCLUB_TRAINING_CHANGE'), 'tkdclub tkdclub-logo-v-sw');
        }

        $canDo = ContentHelper::getActions('com_tkdclub');

        ToolBarHelper::apply('training.apply', 'JTOOLBAR_APPLY');

        ToolBarHelper::save('training.save', 'JTOOLBAR_SAVE');

        if ($canDo->get('core.create'))
        {
            ToolBarHelper::save2new('training.save2new');
        }

        ToolBarHelper::cancel('training.cancel', 'JTOOLBAR_CLOSE');

        $help_url  = 'https://tkdclub.readthedocs.io/{langcode}/latest/mitglieder.html';
        ToolbarHelper::help('', false, $help_url);
    }
}
