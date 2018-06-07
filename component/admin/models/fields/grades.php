<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports the options-markup for taekwondo-grades
 * used in backend an frontend
 */
class JFormFieldGrades extends JFormFieldList
{       
    /**
     * The form field type.
     */
    protected $type = 'grades';
    /**
     * Method to get the field input markup.
     */        
    public function getOptions()
    {
        $list = JText::_('COM_TKDCLUB_NO_GRADE_LISTVIEW');
        $sgrades = JText::_('COM_TKDCLUB_SELECT_STUDENTGRADE');
        $mgrades = JText::_('COM_TKDCLUB_SELECT_MASTERGRADE');
        $grades = array

        (       $list => '',
             $sgrades => '',
            '10. Kup' => '10. Kup',
            '9. Kup' => '9. Kup',
            '8. Kup' => '8. Kup',
            '7. Kup' => '7. Kup',
            '6. Kup' => '6. Kup',
            '5. Kup' => '5. Kup',
            '4. Kup' => '4. Kup',
            '3. Kup' => '3. Kup',
            '2. Kup' => '2. Kup',
            '1. Kup' => '1. Kup',
             $mgrades => '',
           '1. Poom' => '1. Poom',
           '2. Poom' => '2. Poom',
           '3. Poom' => '3. Poom',
            '1. Dan' => '1. Dan',
            '2. Dan' => '2. Dan',
            '3. Dan' => '3. Dan',
            '4. Dan' => '4. Dan',
            '5. Dan' => '5. Dan',
            '6. Dan' => '6. Dan',
            '7. Dan' => '7. Dan',
            '8. Dan' => '8. Dan',
            '9. Dan' => '9. Dan',
            '10. Dan' => '10. Dan'
        );

        foreach($grades as $key => $grade)
        {

            $options[] = JHtml::_('select.option', $key, $grade);

        }

        $options[0]->value = 0;
        $options[1]->value = 'students';
        $options[12]->value ='masters';

        return array_merge(parent::getOptions(), $options);  

        }
}