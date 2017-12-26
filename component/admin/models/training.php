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


    public function paytrainings($member_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Fields to update.
        $fields = array(
            $db->quoteName('trainer_paid') . ' = 1'
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('trainer_paid') . ' = 0',
            $db->quoteName('trainer') . ' = ' . $member_id
        );

        $query->update($db->quoteName('#__tkdclub_trainings'))->set($fields)->where($conditions);

        $db->setQuery($query);

        $result = $db->execute();
        $this->updated_rows = $db->getAffectedRows();
        return $result;
    }

}