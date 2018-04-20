<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for edit view 'participant'
 */
class TkdClubModelParticipant extends JModelAdmin
{       
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   2.0
     * @throws  Exception
     */
    public function getTable($type = 'Participants', $prefix = 'TkdClubTable', $config = array())
    {   
        return JTable::getInstance($type, $prefix, $config);
    }
        
    /**
     * Method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   2.0
     */
    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'participant',  $options);

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
	 * @since   2.0
	 */
    protected function loadFormData()
    {
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.participant.data', array());

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }
}