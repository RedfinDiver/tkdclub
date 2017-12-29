<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Events list view controller
 */
class TkdClubControllerEvents extends JControllerAdmin
{
    protected $text_prefix = 'COM_TKDCLUB_EVENT';

    public function getModel($name = 'event', $prefix = 'TkdClubModel', $config = array())      
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}