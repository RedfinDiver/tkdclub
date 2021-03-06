<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

FormHelper::loadFieldClass('list');
JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

/**
 * Supports the options-markup from component parameter
 */
class JFormFieldChampionshiptypes extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'championshiptypes';

    /**
     * Method to get the field input markup for field 'championshiptypes'
     */
    public function getOptions()
    {
        $options = array();

        // check if field is used as filter, then get options from the database entries
        if ($this->element['isFilter'] == 'true') {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('DISTINCT(type)');
            $query->from($db->quoteName('#__tkdclub_medals'));
            $query->where("type > ''");
            $query->order('type ASC');
            $db->setQuery($query);

            if ($types = $db->loadColumn()) {
                foreach ($types as $type) {
                    $options[] = JHtml::_('select.option', $type, $type);
                }
            }
        } else // not used as filter field, so get the options from the parameter
        {
            $types = Helper::getList(ComponentHelper::getParams('com_tkdclub')->get('championship_types'));

            if ($types) {
                foreach ($types as $type) {
                    $options[] = JHtml::_('select.option', $type, $type);
                }
            }

            // make sure to have always "Poomsae" and "Kyorugi" available
            $options[] = JHtml::_('select.option', 'Kyorugi', JText::_('COM_TKDCLUB_KYORUGI'));
            $options[] = JHtml::_('select.option', 'Poomsae', JText::_('COM_TKDCLUB_POOMSAE'));
        }

        if ($this->form) // checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }
}
