<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports the options-markup from parameters
 *
 * @since  1.0
 */
class JFormFieldMembers extends JFormFieldList
{       
    /**
     * The form field type.
     */
    protected $type = 'members';

    /**
     * Method to get the field input markup for a field with all memers.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
     */       
    public function getOptions()
    {
        // add members from the members table
        $db = Factory::getDBO();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('member_id', 'firstname', 'lastname')));
        $query->from($db->quoteName('#__tkdclub_members'));

        // only active members if it is set in the xml
        if ($this->element['onlyactive'] == 'true')
        {
            $query->where('member_state = "active"');
        }

        $query->order('member_id ASC');
        $db->setQuery($query);
        $members = $db->loadObjectList();
        $options = array();

        foreach($members as $member)
        {
            $options[] = HTMLHelper::_('select.option', $member->member_id, $member->firstname . ' ' . $member->lastname);
        }

        if ($this->form) // if we are in a form merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;    
    }
        
}