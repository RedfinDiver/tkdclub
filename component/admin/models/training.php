<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for edit view 'training'
 */
class TkdClubModelTraining extends JModelAdmin
{

    public function getTable($type = 'Trainings', $prefix = 'TkdClubTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'training',  $options);

        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }

    protected function loadFormData()
    {
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.training.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }
        
        return $data;
    }

}