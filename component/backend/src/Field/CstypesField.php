<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

/**
 * Supports the options-markup from component parameter
 */
class CstypesField extends Listfield
{
    /**
     * The form field type.
     */
    protected $type = 'Cstypes';

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
                    $options[] = HTMLHelper::_('select.option', $type, $type);
                }
            }
        } else // not used as filter field, so get the options from the parameter
        {
            $types = TkdclubHelper::getList(ComponentHelper::getParams('com_tkdclub')->get('championship_types'));

            if ($types) {
                foreach ($types as $type) {
                    $options[] = HTMLHelper::_('select.option', $type, $type);
                }
            }

            // make sure to have always "Poomsae" and "Kyorugi" available
            $options[] = HTMLHelper::_('select.option', 'Kyorugi', Text::_('COM_TKDCLUB_KYORUGI'));
            $options[] = HTMLHelper::_('select.option', 'Poomsae', Text::_('COM_TKDCLUB_POOMSAE'));
        }

        if ($this->form) // checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }
}
