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
 * Model-class for edit view medal.
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
        // Handling the winner fields
        $items = parent::getItem($pk);
        
        $ids = array();
        
        !empty($items->get('winner_1')) ? $ids[] = $items->get('winner_1') : '';
        !empty($items->get('winner_2')) ? $ids[] = $items->get('winner_2') : '';
        !empty($items->get('winner_3')) ? $ids[] = $items->get('winner_3') : '';

        $items->set('winner_ids', $ids);

        unset($items->winner_1);
        unset($items->winner_2);
        unset($items->winner_3);

        return $items;
    }

    /**
	 * Override of the save method.
     * We have to do this because we store the values of
     * the 'winner_ids' field separatly in database fields.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
        $winner_ids = $data['winner_ids'];

        $i = 1;
		foreach ($winner_ids as $id)
		{
			if ($id)
			{
				$data['winner_' . $i] = (int) $id;
				$i++;
			}

			// Only allow up to 3 winners
			if ($i == 4) break;
		}

		// Set unused fields to 0, this is important for the bind method to work
		!isset($data['winner_2']) ? $data['winner_2'] = 0 : '';
		!isset($data['winner_3']) ? $data['winner_3'] = 0 : '';

        // Unset the now useless field
        unset($data['winner_ids']);

        return parent::save($data);
    }
}