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
        $options = array();

        // for filter use add trainers from the existing trainining table
        if ($this->element['isFilter'] == 'true')
        {
            $db = JFactory::getDBO();

            $q1 = $db->getQuery(true)
                  ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
                  ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.trainer') . ')' )
                  ->where('trainer>0');
            
            $q2 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist1') . ')' )
            ->where('assist1>0');
            
            $q3 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist2') . ')' )
            ->where('assist2>0');

            $q4 = $db->getQuery(true)
            ->select(array('a.member_id', 'a.firstname', 'a.lastname'))->from($db->quoteName('#__tkdclub_members', 'a'))
            ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.member_id') . ' = ' . $db->quoteName('b.assist3') . ')' )
            ->where('assist3>0');
            
            $query = $q1->union($q2)->union($q3)->union($q4);
            $db->setQuery($query);

            $trainers = $db->loadObjectList();

            foreach($trainers as $trainer)
            {
                $options[] = JHtml::_('select.option', $trainer->member_id, $trainer->firstname . ' ' . $trainer->lastname);
            }
            
        }
        else // NO filter use, add trainers from the members table where in functions the member is defined as trainer
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
    
            $query->select($db->quoteName(array('member_id', 'firstname', 'lastname')));
            $query->from($db->quoteName('#__tkdclub_members'));
            $query->order('member_id ASC');
            $query->where($db->quoteName('functions') . ' LIKE '. $db->quote('%Trainer%'));
    
            $db->setQuery($query);
    
            $trainers = $db->loadObjectList();
    
            if (!$trainers)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_TKDCLUB_TRAINING_NO_TRAINERS_DEFINED'), 'warning');
            }
            else
            {
                foreach($trainers as $trainer)
                {
                    $options[] = JHtml::_('select.option', $trainer->member_id, $trainer->firstname . ' ' . $trainer->lastname);
                }
            }

        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;    
    }
        
}