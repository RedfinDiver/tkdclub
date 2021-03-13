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
 * Model-class for edit view 'medal'
 *
 * @since  1.0
 */
class MedalModel extends AdminModel
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $config   Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   1.0
     * @throws  Exception
     */
    public function getTable($type = 'Medals', $prefix = 'Administrator', $config = array())
    {
        return  parent::getTable($type, $prefix, $config);
    }
        
    /**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.0
	 */
    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'medal',  $options);

        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }

    /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   1.0
	 */
    protected function loadFormData()
    {
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.medal.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }
        
        return $data;
    }

    /**
    * Method to get a single record.
    *
    * @param   integer  $pk  The id of the primary key.
    *
    * @return  JObject|boolean  Object on success, false on failure.
    *
    * @since   12.2
    */
    public function getItem($pk = null)
    {   
        // Handling the String for multible field
        $items = parent::getItem($pk);
        $ids = json_decode($items->get('winner_ids'));
        $items->set('winner_ids',$ids);

        return $items;
    }

}