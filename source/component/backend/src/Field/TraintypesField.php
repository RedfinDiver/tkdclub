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
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Supports the options-markup from component parameter
 */
class TraintypesField extends ListField
{
    /**
     * The form field type.
     */
    protected $type = 'traintypes';

    /**
     * Method to get the field input markup for field training types
     * 
     * The field can be used in 2 different ways:
     *  1) As filter field - in xml markup use isFilter="true".
     *     Input then is generated from the existing trainings table.
     * 
     *  2) As input field in edit form, no extra markup in xml.
     *     Input then is generated from component configuration.
     * 
     * By adding other training types in the component configuration more training types can be added.
     *
     * @return  string	The field input markup.
     * 
     */
    public function getOptions()
    {
        $options = array();

        // check if field is used as filter, then get options from the database entries.
        if ($this->element['isFilter'] == 'true') {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('DISTINCT(type)');
            $query->from($db->quoteName('#__tkdclub_trainings'));
            $query->order('type ASC');
            $db->setQuery($query);

            if ($types = $db->loadColumn()) 
            {
                foreach ($types as $type)
                {
                    // Just rendering existing types, not empty strings.
                    $type != '' ? $options[] = HTMLHelper::_('select.option', $type, $type) : null;
                }
            }
        }
        else // Not used as filter field, so get the training types from the parameter.
        {
            $types = TkdclubHelper::getList(ComponentHelper::getParams('com_tkdclub')->get('training_types'));

            if (!$types) {
                Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_TRAINING_NO_TRAINTYPES_DEFINED'), 'warning');
            }
            else
            {
                foreach ($types as $type)
                {
                    $options[] = HTMLHelper::_('select.option', $type, $type);
                }
            }
        }

        if ($this->form) // Checking if we are in a form, then merge additional xml data.
        {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }
}
