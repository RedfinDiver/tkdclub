<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Field for licenses
 *
 */
class LicensesField extends ListField
{
    /**
     * The form field type.
     * 
     */
    protected $type = 'Licenses';

    /**
     * Method to get the field input markup for licenses field.
     * 
     * The field is designed to provide always the 10 most common licenses in taekwondo:
     *  - Trainer (A / B / C / D)
     *  - Referee for Poomsae and Kyorugi (region / federal / international)
     * 
     * These 10 licenses are translated in female/male strings.
     * 
     * By adding other licenses in the component configuration more licenses can be added.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
     */
    public function getOptions()
    {
        $functions = TkdclubHelper::getList(ComponentHelper::getParams('com_tkdclub')->get('licenses'));
        $options = array();

        if ($functions) {
            foreach ($functions as $function) {
                $options[] = HTMLHelper::_('select.option', $function, $function);
            }
        }

        // make sure to have always some common standard licenses to select from
        if ($this->form->getValue('sex') == 'male' || $this->form->getValue('sex') == '') {
            $options[] = HTMLHelper::_('select.option', 'trainer_d', Text::_('COM_TKDCLUB_SELECT_TRAINER_D'));
            $options[] = HTMLHelper::_('select.option', 'trainer_c', Text::_('COM_TKDCLUB_SELECT_TRAINER_C'));
            $options[] = HTMLHelper::_('select.option', 'trainer_b', Text::_('COM_TKDCLUB_SELECT_TRAINER_B'));
            $options[] = HTMLHelper::_('select.option', 'trainer_a', Text::_('COM_TKDCLUB_SELECT_TRAINER_A'));
            $options[] = HTMLHelper::_('select.option', 'referee_r_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_R_KY'));
            $options[] = HTMLHelper::_('select.option', 'referee_r_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_R_PO'));
            $options[] = HTMLHelper::_('select.option', 'referee_s_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_S_KY'));
            $options[] = HTMLHelper::_('select.option', 'referee_s_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_S_PO'));
            $options[] = HTMLHelper::_('select.option', 'referee_i_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_I_KY'));
            $options[] = HTMLHelper::_('select.option', 'referee_i_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_I_PO'));
        } elseif ($this->form->getValue('sex') == 'female') {
            $options[] = HTMLHelper::_('select.option', 'trainer_d', Text::_('COM_TKDCLUB_SELECT_TRAINER_D_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'trainer_c', Text::_('COM_TKDCLUB_SELECT_TRAINER_C_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'trainer_b', Text::_('COM_TKDCLUB_SELECT_TRAINER_B_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'trainer_a', Text::_('COM_TKDCLUB_SELECT_TRAINER_A_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_r_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_R_KY_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_r_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_R_PO_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_s_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_S_KY_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_s_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_S_PO_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_i_ky', Text::_('COM_TKDCLUB_SELECT_REFEREE_I_KY_FEMALE'));
            $options[] = HTMLHelper::_('select.option', 'referee_i_po', Text::_('COM_TKDCLUB_SELECT_REFEREE_I_PO_FEMALE'));
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
