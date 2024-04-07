<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */ 

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Field for board member functions
 *
 */
class FunctionsField extends ListField
{
    /**
     * The form field type.
     */
    protected $type = 'Functions';

    /**
     * Method to get the field input markup for functions field.
     * 
     * The field is designed to provide always the 4 most common functions of board members in a club:
     *  1) president
     *  2) vicepresident
     *  3) treasurer
     *  4) secretary
     * 
     * These 4 functions are translated in female/male strings.
     * 
     * By adding other functions in the component configuration more functions can be added.
     *
     * @return  string	The field input markup.
     *
     */
    public function getOptions()
    {
        $functions = TkdclubHelper::getList(ComponentHelper::getParams('com_tkdclub')->get('functions'));
        $options = array();

        if ($functions) {
            foreach ($functions as $function) {
                $options[] = HTMLHelper::_('select.option', $function, $function);
            }
        }

        // make sure to have always some common standard function to select from
        if ($this->form->getValue('sex') == 'male' || $this->form->getValue('sex') == '') {
            $options[] = HTMLHelper::_('select.option', 'president', Text::_('COM_TKDCLUB_SELECT_PRESIDENT'));
            $options[] = HTMLHelper::_('select.option', 'vpresident', Text::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT'));
            $options[] = HTMLHelper::_('select.option', 'treasurer', Text::_('COM_TKDCLUB_SELECT_TREASURER'));
            $options[] = HTMLHelper::_('select.option', 'secretary', Text::_('COM_TKDCLUB_SELECT_SECRETARY'));
            $options[] = HTMLHelper::_('select.option', 'trainer', Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM'));
        } elseif ($this->form->getValue('sex') == 'female') {
            $options[] = HTMLHelper::_('select.option', 'president', Text::_('COM_TKDCLUB_SELECT_PRESIDENT_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'vpresident', Text::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'treasurer', Text::_('COM_TKDCLUB_SELECT_TREASURER_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'secretary', Text::_('COM_TKDCLUB_SELECT_SECRETARY_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'trainer', Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM_FEMALE'));
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
