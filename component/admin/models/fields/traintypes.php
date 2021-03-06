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
use Joomla\CMS\Component\ComponentHelper;

FormHelper::loadFieldClass('list');
JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/list.php');

/**
 * Supports the options-markup from component parameter
 */
class JFormFieldTraintypes extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'traintypes';

    /**
     * Method to get the field input markup for field 'traintypes'
     */
    public function getOptions()
    {
        $options = array();

        // check if field is used as filter, then get options from the database entries
        if ($this->element['isFilter'] == 'true') {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('DISTINCT(type)');
            $query->from($db->quoteName('#__tkdclub_trainings'));
            $query->order('type ASC');
            $db->setQuery($query);

            if ($types = $db->loadColumn()) {
                foreach ($types as $type) {
                    // Just rendering existing types, not empty strings
                    $type != '' ? $options[] = HTMLHelper::_('select.option', $type, $type) : null;
                }
            }
        } else // not used as filter field, so get the training types from the parameter
        {
            $types = Helper::getList(ComponentHelper::getParams('com_tkdclub')->get('training_types'));

            if (!$types) {
                Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_TRAINING_NO_TRAINTYPES_DEFINED'), 'warning');
            } else {
                foreach ($types as $type) {
                    $options[] = HTMLHelper::_('select.option', $type, $type);
                }
            }
        }

        if ($this->form) // checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }
}
