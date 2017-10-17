<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;


JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for edit-view: 'medal'
 */
class TkdClubViewMedal extends JViewLegacy
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

        if ($this->item->medal_id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEDAL_NEW'), 'tkdclub');
        }
        else
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_MEDAL_CHANGE'), 'tkdclub');
        }
      
        $canDo = TkdClubHelperActions::getActions();

        JToolBarHelper::apply('medal.apply', 'JTOOLBAR_APPLY');
        
        JToolBarHelper::save('medal.save', 'JTOOLBAR_SAVE');
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2copy('medal.save2copy');}
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2new('medal.save2new');}
        
        JToolBarHelper::cancel('medal.cancel', 'JTOOLBAR_CANCEL');
    }
}
