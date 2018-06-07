<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JLoader::register('TkdclubHelperActions', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/actions.php');

/**
 * view-class for edit-view: 'promotion'
 */
class TkdClubViewPromotion extends JViewLegacy
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
        
        if ($this->item->promotion_id == NULL)
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PROMOTION_NEW_TITLE'), 'tkdclub');
        }
        else 
        {
            JToolBarHelper::title($clubname . JText::_('COM_TKDCLUB_PROMOTION_EDIT_TITLE'), 'tkdclub'); 
        }
      
        $canDo = TkdClubHelperActions::getActions();
 
        JToolBarHelper::apply('promotion.apply', 'JTOOLBAR_APPLY');
        
        JToolBarHelper::save('promotion.save', 'JTOOLBAR_SAVE');
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2copy('promotion.save2copy');}
        
        if ($canDo->get('core.create'))
        {JToolBarHelper::save2new('promotion.save2new');}
        
        
        JToolBarHelper::cancel('promotion.cancel', 'JTOOLBAR_CANCEL');
        
        JToolbarHelper::help('', '', 'http://tkdclub.readthedocs.io/de/latest/pruefungen.html');
    }
}