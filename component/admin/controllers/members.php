<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
* listview members controller
*/
class TkdclubControllerMembers extends JControllerAdmin
{
    public function getModel($name = 'member', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * set custom prefix for message string
     */
    public function delete()
    {
        $this->text_prefix = 'COM_TKDCLUB_MEMBER';

        return parent::delete();
    }

    /**
     * set custom prefix for message string
     */
    public function checkin()
    {
        $this->text_prefix = 'COM_TKDCLUB_MEMBER';
        
        return parent::checkin();
    }
}