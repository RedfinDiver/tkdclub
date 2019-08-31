<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Model-class for edit view 'subscriber'
 */
class TkdClubModelSubscriber extends JModelAdmin
{

    public function getTable($type = 'subscribers', $prefix = 'TkdClubTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'subscriber',  $options);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.subscriber.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $text = array(
            "1" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_MANUAL',
            "2" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_FORM'
        );

        $data->origin = $data->origin ? JText::_($text[$data->origin]) : null;

        return $data;
    }
}
