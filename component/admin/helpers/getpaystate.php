<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdclubHelperGetpaystate
{
    /**
     * Checks the payment-state of a training
     * 
     * @param int $trainer_paid 0 for not paid, 1 for paid
     */
    public static function getpaystate($trainer_paid, $assist1, $assist2, $assist3, $assist1_paid, $assist2_paid, $assist3_paid)
    {
        // No trainer or assistent paid -> training is not paid at all
        if (!$trainer_paid && !$assist1_paid && !$assist2_paid && !$assist3_paid)
        {
            return 0;
        }
        else
        {
            !$assist1 || $assist1 && $assist1_paid ? $check1 = true : $check1 = false;
            !$assist2 || $assist2 && $assist2_paid ? $check2 = true : $check1 = false;
            !$assist3 || $assist3 && $assist3_paid ? $check3 = true : $check3 = false;

            // All checks good --> training is entirly paid
            if ($check1 && $check2 && $check3 && $trainer_paid)
            {
                return 1;
            }
            else // One or more checks not OK --> so return partly paid
            {
                return 2;
            }
        }
    }

}