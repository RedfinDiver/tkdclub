<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports the options-markup for medal years
 * used in backend and frontent
 */
class JFormFieldMedalyears extends JFormFieldList
{
    /**
     * @var		string
     */
    protected $type = 'medalyears';
    
    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     */
    public function getOptions()
    {   
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('DISTINCT YEAR(date)');
        $query->from($db->quoteName('#__tkdclub_medals'));
        $query->order('date DESC');

        $db->setQuery($query);
        $years = $db->loadColumn();
                    
        foreach($years as $year)
        {

            $options[] = JHtml::_('select.option', $year, $year);

        }
        
        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }
        
        return $options;       
    }
    
}
