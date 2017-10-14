<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Supports the options-markup from component parameter
 */
class JFormFieldFilterTraintypes extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'filtertraintypes';
        
    /**
     * Method to get the field input markup for FILTER FIELD 'traintypes' in trainings list view
     */    
    public function getOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT(type)');
        $query->from($db->quoteName('#__tkdclub_trainings'));
        $query->order('type ASC');
        $db->setQuery($query);

        if ($types = $db->loadColumn())
        {
            foreach($types as $type)
            {
                $options[] = JHtml::_('select.option', $type, $type);
            }
            
            if ($this->form) //checking if we are in a form, then merge additional xml data
            {
                $options = array_merge(parent::getOptions(), $options); 
            }
            
            return $options;
        }
        
        return parent::getOptions();
    }
}