<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Field to get trainers
 * 
 */
class TrainersField extends ListField
{
    /**
     * The form field type.
     * 
     */
    protected $type = 'Trainers';

    /**
     * Method to get the field input markup for field trainers.
     * 
     * The field can be used in 2 different ways:
     *  1) As filter field - in xml markup use isFilter="true".
     *     Input then is generated from the existing training table.
     * 
     *  2) As input field in edit form, no extra markup in xml.
     *     Input then is generated from members table for members with trainer function.
     * 
     * @return  string	The field input markup.
     * 
     */
    public function getOptions()
    {
        $options = array();
        $app = Factory::getApplication();

        // For filter use add trainers from the existing training table
        if ($this->element['isFilter'] == 'true') {

            // Return of this function when called with $fromTrainingsTable = true is a object
            // So some more work is to do for rendering the correct field
            $names = TkdclubHelper::getTrainer($fromTrainingsTable = true);
            $trainers = array();

            foreach ($names as $name) {
                $trainers[$name->id] = $name->firstname . ' ' . $name->lastname;
            }
        }

        // NO filter use, add trainers from the members table where in functions the member is defined as trainer
        if ($this->element['isFilter'] == 'false') {
            $trainers = TkdclubHelper::getTrainer($fromTrainingsTable = false);

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
