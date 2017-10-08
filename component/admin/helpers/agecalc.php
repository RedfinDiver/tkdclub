<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* age calculation helper
*/
class TkdclubHelperAge
{
            /**
         * Returns the Age in Years at current date
         * 
         * @param string $birthday date-string in the format YYYY-mm-dd
         * @param string $format   'y' for integer years, 'days' for integer days
         * 
         * @return integer Age on current date in years or days
         * 
         * @since 1.0
         */
        public static function getAge($birthday, $format = 'y')
        {
            $dob = new DateTime($birthday);
            $now = new DateTime('today');
            $age = $dob->diff($now);
            
            if ($format == 'y')
            {
                return $age->y;
            }
            else
            {
                return $age->days;
            }

        }

        /**
         * Returns the Age in Years to particular date
         * 
         * @param string $date date-string in the format YYYY-mm-dd
         * @param string $birthday date-string in the format YYYY-mm-dd
         * 
         * @return string Age on current date
         * 
         * @since 1.0
         */
        public static function getAgetoDate($date, $birthday)
        {
            $dob = new DateTime($birthday);
            $dat = new DateTime($date);
            $age = $dob->diff($dat);
            
            return $age->y;
        }
}