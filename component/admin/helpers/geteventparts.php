<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdclubHelperGetEventparts
{
    /**
     * Gets the number of participants for a event
     * 
     * @param   int $event_id id of the event in the events-table
     * 
     * @return  int number of participants for the event
     * 
     * @see     views/events/default.php
     */
    public static function getEventparts($event_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('sum('.$db->quoteName('registered').')')
              ->from($db->quoteName('#__tkdclub_event_participants'))
              ->where('event_id = '.$db->quote($event_id));

        $db->setQuery($query);
        $db->execute();

        return $db->loadResult();
    }
}