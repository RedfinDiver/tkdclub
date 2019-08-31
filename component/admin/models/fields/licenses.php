<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

/**
 * Supports the options-markup from parameters
 *
 */
class JFormFieldLicenses extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'licenses';

    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
     */
    public function getOptions()
    {
        $functions = Helper::getList(JComponentHelper::getParams('com_tkdclub')->get('licenses'));
        $options = array();

        if ($functions) {
            foreach ($functions as $function) {
                $options[] = JHtml::_('select.option', $function, $function);
            }
        }

        // make sure to have always some common standard licenses to select from
        if ($this->form->getValue('sex') == 'male' || $this->form->getValue('sex') == '') {
            $options[] = JHtml::_('select.option', 'trainer_d', JText::_('COM_TKDCLUB_SELECT_TRAINER_D'));
            $options[] = JHtml::_('select.option', 'trainer_c', JText::_('COM_TKDCLUB_SELECT_TRAINER_C'));
            $options[] = JHtml::_('select.option', 'trainer_b', JText::_('COM_TKDCLUB_SELECT_TRAINER_B'));
            $options[] = JHtml::_('select.option', 'trainer_a', JText::_('COM_TKDCLUB_SELECT_TRAINER_A'));
            $options[] = JHtml::_('select.option', 'referee_r_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_R_KY'));
            $options[] = JHtml::_('select.option', 'referee_r_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_R_PO'));
            $options[] = JHtml::_('select.option', 'referee_s_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_S_KY'));
            $options[] = JHtml::_('select.option', 'referee_s_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_S_PO'));
            $options[] = JHtml::_('select.option', 'referee_i_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_I_KY'));
            $options[] = JHtml::_('select.option', 'referee_i_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_I_PO'));
        } elseif ($this->form->getValue('sex') == 'female') {
            $options[] = JHtml::_('select.option', 'trainer_d', JText::_('COM_TKDCLUB_SELECT_TRAINER_D_FEMALE'));
            $options[] = JHtml::_('select.option', 'trainer_c', JText::_('COM_TKDCLUB_SELECT_TRAINER_C_FEMALE'));
            $options[] = JHtml::_('select.option', 'trainer_b', JText::_('COM_TKDCLUB_SELECT_TRAINER_B_FEMALE'));
            $options[] = JHtml::_('select.option', 'trainer_a', JText::_('COM_TKDCLUB_SELECT_TRAINER_A_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_r_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_R_KY_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_r_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_R_PO_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_s_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_S_KY_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_s_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_S_PO_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_i_ky', JText::_('COM_TKDCLUB_SELECT_REFEREE_I_KY_FEMALE'));
            $options[] = JHtml::_('select.option', 'referee_i_po', JText::_('COM_TKDCLUB_SELECT_REFEREE_I_PO_FEMALE'));
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
