<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');
JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

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
        $app = Factory::getApplication();

        // For filter use add trainers from the existing training table
        if ($this->element['isFilter'] == 'true') {
            /**
             * Return of this function when called with $fromTrainingsTable = true is a object
             * So some more work is to do for rendering the correct field
             */
            $names = Helper::getTrainer($fromTrainingsTable = true);
            $trainers = array();

            foreach ($names as $name) {
                $trainers[$name->member_id] = $name->firstname . ' ' . $name->lastname;
            }
        }

        // NO filter use, add trainers from the members table where in functions the member is defined as trainer
        if ($this->element['isFilter'] == 'false') {
            $trainers = Helper::getTrainer($fromTrainingsTable = false);

            if (!$trainers) {
                $app->enqueueMessage(Text::_('COM_TKDCLUB_TRAINING_NO_TRAINERS_DEFINED'), 'warning');
            }
        }

        foreach ($trainers as $id => $name) {
            $options[] = HTMLHelper::_('select.option', $id, $name);
        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }
}
