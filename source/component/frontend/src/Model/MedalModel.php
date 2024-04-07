<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

class MedalModel extends \Redfindiver\Component\Tkdclub\Administrator\Model\MedalModel
{
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
        // Adding the form from the backend
        Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/forms');
        
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'medal',  $options);

        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }
}
