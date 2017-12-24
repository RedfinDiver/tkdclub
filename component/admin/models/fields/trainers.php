<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperTrainer', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/trainer.php');

class JFormFieldTrainers extends JFormFieldList
{       
    /**
    * The form field type.
    */
    protected $type = 'trainers';

    /**
     * Method to get the field input markup for field 'trainers'.
     */       
    public function getOptions()
    {   
        $options = array();

        // For filter use add trainers from the existing training table
        if ($this->element['isFilter'] == 'true')
        {
            /**
             * Return of this function when called with $fromTrainingsTable = true is a object
             * So some more work is to do for rendering the correct field
             */
            $names = TkdclubHelperTrainer::getTrainer($fromTrainingsTable = true);
            $trainers = array();

            foreach($names as $name)
            {
                $trainers[$name->member_id] = $name->firstname . ' ' . $name->lastname;
            }
        }

        // NO filter use, add trainers from the members table where in functions the member is defined as trainer
        if ($this->element['isFilter'] == 'false')
        {
            $trainers = TkdclubHelperTrainer::getTrainer($fromTrainingsTable = false);
    
            if (!$trainers)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_TKDCLUB_TRAINING_NO_TRAINERS_DEFINED'), 'warning');
            }

        }

        foreach($trainers as $id => $name)
        {
            $options[] = JHtml::_('select.option', $id, $name);
        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;    
    }
        
}