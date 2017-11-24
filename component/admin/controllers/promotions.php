<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * listview promotions controller
 */
class TkdClubControllerPromotions extends JControllerAdmin
{
    protected $text_prefix = 'COM_TKDCLUB_PROMOTION';
    
    public function getModel($name = 'promotion', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
