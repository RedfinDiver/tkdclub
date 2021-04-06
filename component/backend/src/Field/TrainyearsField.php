<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Field for training years
 * 
 */
class TrainyearsField extends ListField
{
    /**
     * The form field type.
     *
     * @var		string
     * 
     */
    protected $type = 'trainyears';
    
    /**
     * Method to get the field input markup for trainig years.
     * 
     * Returns all years in which a training is in the database.
     * Number of years to be shown can be altered by component configuration.
     * With no configuration, 5 years is the default.
     *
     * @return  string	The field input markup.
     * 
     */
    public function getOptions()
    {   
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('DISTINCT YEAR(date)');
        $query->from($db->quoteName('#__tkdclub_trainings'));
        $query->order('date DESC');
        $db->setQuery($query);
        
        if ($years = $db->loadColumn())
        {
            // Maximum years to show as set in the configuration
            $number_years = ComponentHelper::getParams('com_tkdclub')->get('training_years', 5);
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
                $options[] = HTMLHelper::_('select.option', $year, $year);
            }
            

            if ($this->form) // If we are in a form merge additional xml data
            {
                $options = array_merge(parent::getOptions(), $options); 
            }
            
            return $options;
        }
        
        return parent::getOptions();
    }
}
