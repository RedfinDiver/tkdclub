<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Field to get members
 *
 */
class MembersField extends ListField
{       
    /**
     * The form field type.
     * 
     */
    protected $type = 'Members';

    /**
     * Method to get the field input markup for a field with members.
     * 
     * The field can be used in 3 different ways:
     *  1) Setup in xml markup onlyactive="true" -> only active members are selected.
     *  2) Setup in xml markup onlywinner="true" -> only members present in the medal table are selected.
     *  3) No setup in xml markup -> all members present in the database are selected.
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

        $query->select($db->quoteName(array('id', 'firstname', 'lastname')));
        $query->from($db->quoteName('#__tkdclub_members'));

        // only active members if it is set in the xml
        if ($this->element['onlyactive'] == 'true')
        {
            $query->where('member_state = "active"');
        }

        // Only members present in the medals table
        if ($this->element['onlywinner'] == 'true')
        {
            if ($winners = $this->getWinners())
            {
                $query->whereIn($db->quoteName('id'), $winners, ParameterType::INTEGER);
            }
            else
            {
                // We don't have some medals yet
                $query->where($db->quoteName('id') . ' = 0');
            }
            
        }

        $query->order('id ASC');
        $db->setQuery($query);
        $members = $db->loadObjectList();
        $options = array();

        foreach($members as $member)
        {
            $options[] = HTMLHelper::_('select.option', $member->id, $member->firstname . ' ' . $member->lastname);
        }

        if ($this->form) // If we are in a form merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;    
    }

    protected function getWinners()
    {
        $db = Factory::getDBO();
        
        $q1 = $db->getQuery(true);
        $q1->select($q1->quoteName('winner_1'))
            ->from($q1->quoteName('#__tkdclub_medals'))
            ->where($q1->quoteName('winner_1') . ' > 0');

        $q2 = $db->getQuery(true);
        $q2->select($q2->quoteName('winner_2'))
            ->from($q2->quoteName('#__tkdclub_medals'))
            ->where($q1->quoteName('winner_2') . ' > 0');

        $q3 = $db->getQuery(true);
        $q3->select($q3->quoteName('winner_3'))
            ->from($q3->quoteName('#__tkdclub_medals'))
            ->where($q1->quoteName('winner_3') . ' > 0');

        $query = $db->getQuery(true);
        $query = $q1->union($q2)->union($q3);

        $db->setQuery($query);
        return $db->loadColumn();
    }
        
}