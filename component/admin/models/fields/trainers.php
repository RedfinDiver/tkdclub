<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class JFormFieldTrainers extends JFormFieldList
{       
    /**
    * The form field type.
    */
    protected $type = 'trainers';

    /**
    * Method to get the field input markup for field 'trainers'.
    *
    */       
    public function getOptions()
    {
        // add trainers from the members table where in functions the member is defined as trainer
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('member_id', 'firstname', 'lastname')));
        $query->from($db->quoteName('#__tkdclub_members'));
        $query->order('member_id ASC');
        $query->where($db->quoteName('functions') . ' LIKE '. $db->quote('%Trainer%'));

        $db->setQuery($query);

        $trainers = $db->loadObjectList();

        foreach($trainers as $trainer)
        {
            $options[] = JHtml::_('select.option', $trainer->member_id, $trainer->firstname . ' ' . $trainer->lastname);
        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;    
    }
        
}