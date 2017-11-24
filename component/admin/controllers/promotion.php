<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubControllerPromotion extends JControllerForm
{
    protected $text_prefix = 'COM_TKDCLUB_PROMOTION';

    /**
     * Overriding save method for additional info about
     * modifiy and create date
    */
    public function save($key = null, $urlVar = null)
    {
        //getting the saving user and the current datetime
        $user_id = JFactory::getUser()->id;
        $date = JFactory::getDate()->toSql();
        //getting the data array
        $fields = $this->input->post->get('jform', array(), 'array');

        if ($this->input->get('promotion_id') > 0)
        {                    
            //already existing item, just changing the modified fields
            $fields['modified'] = $date;
            $fields['modified_by'] = intval($user_id);
        }
        elseif ($this->input->get('promotion_id') == 0)
        {
            //new created item, just setting create fields
            $fields['created'] = $date;
            $fields['created_by'] = intval($user_id);            
        }
        
        //pushing back the fields to the input object
        $this->input->post->set('jform', $fields);
        
        parent::save($key, $urlVar);
    }
    
}