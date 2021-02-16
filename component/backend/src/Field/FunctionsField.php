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
 * Supports the options-markup from parameters
 *
 */
class FunctionsField extends ListField
{
    /**
     * The form field type.
     */
    protected $type = 'Functions';

    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
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
            $options[] = HTMLHelper::_('select.option', 'secratary', Text::_('COM_TKDCLUB_SELECT_SECRETARY'));
            $options[] = HTMLHelper::_('select.option', 'trainer', Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM'));
        } elseif ($this->form->getValue('sex') == 'female') {
            $options[] = HTMLHelper::_('select.option', 'president', Text::_('COM_TKDCLUB_SELECT_PRESIDENT_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'vpresident', Text::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'treasurer', Text::_('COM_TKDCLUB_SELECT_TREASURER_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'secratary', Text::_('COM_TKDCLUB_SELECT_SECRETARY_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'trainer', Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM_FEMALE'));
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
