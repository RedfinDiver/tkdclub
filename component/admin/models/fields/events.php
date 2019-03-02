<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports the options-markup for events
 * used in backend and frontend
 */
class JFormFieldEvents extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'events';
    
    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     * @since   2.0
     */
    public function getOptions()
    {   
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $options = array();

        // Add fields from the events-table
        $query->select('*');
        $query->from($db->quoteName('#__tkdclub_events'));

        // only published events if it is set in the xml
        if ($this->element['onlypublished'] == 'true')
        {
            $query->where('published = 1');
        }
        
        $db->setQuery($query);
        $events = $db->loadObjectList();

        foreach ($events as $event)
        {
            $title = htmlspecialchars(substr($event->title, 0, 18));
            $options[] = JHtml::_('select.option', $event->event_id, JHtml::_('date', $event->date, JText::_('DATE_FORMAT_LC4')) . ' ' . ' - ' . $title . '...');
        }
        
        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }

        return $options;  
    }
}