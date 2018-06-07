<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('TkdClubHelperList', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/list.php');

/**
 * Supports the options-markup from parameters
 *
 */
class JFormFieldFunctions extends JFormFieldList
{       
        /**
         * The form field type.
         */
        protected $type = 'functions';
        
       /**
         * Method to get the field input markup.
         *
         * @return  string	The field input markup.
         *
         * @since   1.0
         */       
        public function getOptions()
        {
            $functions = TkdClubHelperList::getList(JComponentHelper::getParams('com_tkdclub')->get('functions'));
            $options = array();

            if($functions)
            {
                foreach($functions as $function)
                {
                    $options[] = JHtml::_('select.option', $function, $function);
                }
            }

            // make sure to have always some common standard function to select from
            if ($this->form->getValue('sex') == 'male' || $this->form->getValue('sex') == '')
            {
                $options[] = JHtml::_('select.option', 'president', JText::_('COM_TKDCLUB_SELECT_PRESIDENT'));
                $options[] = JHtml::_('select.option', 'vpresident', JText::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT'));
                $options[] = JHtml::_('select.option', 'treasurer', JText::_('COM_TKDCLUB_SELECT_TREASURER'));
                $options[] = JHtml::_('select.option', 'secratary', JText::_('COM_TKDCLUB_SELECT_SECRETARY'));
                $options[] = JHtml::_('select.option', 'trainer', JText::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM'));
            }
            elseif ($this->form->getValue('sex') == 'female')
            {
                $options[] = JHtml::_('select.option', 'president', JText::_('COM_TKDCLUB_SELECT_PRESIDENT_FEMALE'));
                $options[] = JHtml::_('select.option', 'vpresident', JText::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT_FEMALE'));
                $options[] = JHtml::_('select.option', 'treasurer', JText::_('COM_TKDCLUB_SELECT_TREASURER_FEMALE'));
                $options[] = JHtml::_('select.option', 'secratary', JText::_('COM_TKDCLUB_SELECT_SECRETARY_FEMALE'));
                $options[] = JHtml::_('select.option', 'trainer', JText::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM_FEMALE'));
            }

            $options = array_merge(parent::getOptions(), $options); 
            
            return $options;  
        }
        
}