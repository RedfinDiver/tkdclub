<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Supports the options-markup for training years
 * used in backend and frontent
 */
class JFormFieldTrainyears extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var		string
     * 
     */
    protected $type = 'trainyears';
    
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
        $query->from($db->quoteName('#__tkdclub_trainings'));
        $query->order('date DESC');
        $db->setQuery($query);
        
        if ($years = $db->loadColumn())
        {
            // maximum years to show as set in the configuration
            $number_years = JComponentHelper::getParams('com_tkdclub')->get('training_years', 5);
            $min_year = max($years) - $number_years;

            foreach ($years as $key => $year)
            {
               if ($year < $min_year)
               {
                   unset($years[$key]);
               }
            }
            
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
        
        return parent::getOptions();
    }

}