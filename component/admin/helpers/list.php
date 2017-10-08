<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* helper class to get a associative array from string
* mostly used for generating lists for select boxes out of parameters
*/
class TkdclubHelperList
{  
    public static function getList($string = '')
    {
        if(empty($string))
        {
            return false;
        }
        else
        {
            $parts = explode(',', $string);
            $result = array();
            foreach ($parts as $key => $value)
            {
                $result[$value] = $value;
            }

            return $result;
        }
    }
}