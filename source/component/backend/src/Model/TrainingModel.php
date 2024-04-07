<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Model-class for edit view training.
 */
class TrainingModel extends AdminModel
{
    /**
	 * How many rows were updated on execution of paytraining method
     * Used for controller messages
	 *
	 * @var  integer
	 */
    public $updated_rows = 0;

    /**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   3.0
	 * @throws  \Exception
	 */
    public function getTable($type = 'Trainings', $prefix = 'Administrator', $config = array())
    {
        return  parent::getTable($type, $prefix, $config);
    }

    /**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 * @since   1.6
	 */
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

    /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
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
     * @param   $id     integer The member-id in the database
     * @return  bool    true if updates succeeded, false if not
     */
    public function paytrainings($id)
    {
        $this->updated_rows = 0;
        $result = true;
        $userId = Factory::getUser()->id;
        $date   = Factory::getDate()->toSql();

        /**
         * Create array for query parameters
         * Each array has the form ['field_to_update', 'field_condition_1', 'field_condition_2']
         * 
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
                $db->quoteName($parameter[2]) . ' = ' . (int) $id
            );

            $query->update($db->quoteName('#__tkdclub_trainings'))->set($fields)->where($conditions);
            $db->setQuery($query);

            $result = $db->execute();
            
            if ($result === false)
            {
                return $result;
            }

            $this->updated_rows += $db->getAffectedRows();
        }
        
        return true;
    }
}
