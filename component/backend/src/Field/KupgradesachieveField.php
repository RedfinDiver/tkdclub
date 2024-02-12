<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;

/**
 * Supports the options-markup for taekwondo-grades
 * used in frontend for event subscription
 */
class KupgradesachieveField extends ListField
{       
        /**
         * The form field type.
         *
         * @var		string
         * @since   1.0
         */
        protected $type = 'kupgradesachieve';

        /**
         * Method to get the field input markup.
         *
         * @return  string	The field input markup.
         *
         * @since   1.0
         */        
        public function getOptions()
        {
            $grades = array

            (   '10. Kup' => '10. Kup' . Text::_('COM_TKDCLUB_10_KUP_DESC'),
                '09. Kup' => '09. Kup' . Text::_('COM_TKDCLUB_9_KUP_DESC'),
                '08. Kup' => '08. Kup' . Text::_('COM_TKDCLUB_8_KUP_DESC'),
                '07. Kup' => '07. Kup' . Text::_('COM_TKDCLUB_7_KUP_DESC'),
                '06. Kup' => '06. Kup' . Text::_('COM_TKDCLUB_6_KUP_DESC'),
                '05. Kup' => '05. Kup' . Text::_('COM_TKDCLUB_5_KUP_DESC'),
                '04. Kup' => '04. Kup' . Text::_('COM_TKDCLUB_4_KUP_DESC'),
                '03. Kup' => '03. Kup' . Text::_('COM_TKDCLUB_3_KUP_DESC'),
                '02. Kup' => '02. Kup' . Text::_('COM_TKDCLUB_2_KUP_DESC'),
                '01. Kup' => '01. Kup' . Text::_('COM_TKDCLUB_1_KUP_DESC'),
            );

            foreach($grades as $key => $grade)
            {

                $options[] = HTMLHelper::_('select.option', $key, $grade);

            }

            return array_merge(parent::getOptions(), $options);  

        }
}