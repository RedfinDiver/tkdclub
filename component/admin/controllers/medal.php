<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubControllerMedal extends JControllerForm
{
    protected $text_prefix = 'COM_TKDCLUB_MEDAL';

    /**
     * Overriding save method for additional data and string conversation
    */
    public function save($key = null, $urlVar = null)
	{
        // getting the saving user and the current datetime
        $user_id = JFactory::getUser()->id;
        $date = JFactory::getDate()->toSql();

        // getting the data in the fields
        $data  = $this->input->post->get('jform', array(), 'array');

        if ($data['medal_id'] > 0)
        {                    
            //already existing item, just changing the modified fields
            $data['modified'] = $date;
            $data['modified_by'] = intval($user_id);
        }
        elseif ($this->input->get('medal_id') == 0)
        {
            //new created item, just setting create fields
            $data['created'] = $date;
            $data['created_by'] = intval($user_id);            
        }

        // convert the array from the winner_id field into json string format
        $data['winner_ids'] = json_encode($data['winner_ids']);

        //pushing back the data to the input object
        $this->input->post->set('jform', $data);

         parent::save($key = null, $urlVar = null);
    }
}