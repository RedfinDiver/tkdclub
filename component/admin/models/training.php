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
        $this->updated_rows = 0;
        $result = true;

        // First create array for query parameters
        $parameters = array(
            'update_trainer' => array(
                'field' => 'trainer_paid',
                'conditions' => array(
                    'trainer_paid', 'trainer'
                )
            ),
            'update_assist1' => array(
                'field' => 'assist1_paid',
                'conditions' => array(
                    'assist1_paid', 'assist1'
                )
            ),
            'update_assist2' => array(
                'field' => 'assist2_paid',
                'conditions' => array(
                    'assist2_paid', 'assist2'
                )
            ),
            'update_assist3' => array(
                'field' => 'assist3_paid',
                'conditions' => array(
                    'assist3_paid', 'assist3'
                )
            )
        );

        foreach ($parameters as $parameter)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            // Fields to update.
            $fields = array(
                $db->quoteName($parameter['field']) . ' = 1'
            );

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName($parameter['conditions'][0]) . ' = 0',
                $db->quoteName($parameter['conditions'][1]) . ' = ' . $member_id
            );

            $query->update($db->quoteName('#__tkdclub_trainings'))->set($fields)->where($conditions);
            $db->setQuery($query);

            $result = $db->execute();
            $rows = $db->getAffectedRows();
            $this->updated_rows += $db->getAffectedRows();
            
            if ($result === false)
            {
                return $result;
            }
        }

        return true;
    }

}