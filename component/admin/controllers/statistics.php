<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubControllerStatistics extends JControllerLegacy
{
    public function __construct($config = array())
    {
        parent::__construct($config = array());
    }
    
    public function getModel($name = 'statistics', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}