<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * Model-class for edit view 'training'
 */
class TkdClubModelTraining extends AdminModel
{

    public function getTable($type = 'Trainings', $prefix = 'TkdClubTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
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
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.training.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }
        
        return $data;
    }

    /**
     * Updates the training for the given trainer in database
     * 
     * @param   $member_id  integer The member-id in the database
     * @return  bool    true if updates succeeded, false if not
     */
    public function paytrainings($member_id)
    {
        $this->updated_rows = 0;
        $result = true;
        $userId = Factory::getUser()->id;
        $date   = Factory::getDate()->toSql();

        /**
         * Create array for query parameters
         * Each array has the form ['field_to_update', 'field_condition_1', 'field_condition_2']
         */
        $parameters = array(
            array('trainer_paid', 'trainer_paid', 'trainer'),
            array('assist1_paid', 'assist1_paid', 'assist1'),
            array('assist2_paid', 'assist2_paid', 'assist2'),
            array('assist3_paid', 'assist3_paid', 'assist3'),
        );

        foreach ($parameters as $parameter)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);

            // Fields to update.
            $fields = array(
                $db->quoteName($parameter[0]) . ' = 1',
                $db->quoteName('modified') . ' = ' . $db->quote($date),
                $db->quoteName('modified_by') . ' = ' . $db->quote($userId),
            );

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName($parameter[1]) . ' = 0',
                $db->quoteName($parameter[2]) . ' = ' . $member_id
            );

            $query->update($db->quoteName('#__tkdclub_trainings'))->set($fields)->where($conditions);
            $db->setQuery($query);

            $result = $db->execute();
            $this->updated_rows += $db->getAffectedRows();
            
            if ($result === false)
            {
                return $result;
            }
        }
        
        return true;
    }
}