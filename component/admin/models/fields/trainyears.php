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
         * @since   2.0
         */
        protected $type = 'trainyears';
        
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

            $query->select('DISTINCT substring(date,1,4)');
            $query->from($db->quoteName('#__tkdclub_trainings'));
            $query->order($db->quoteName('date') . 'ASC');

            $db->setQuery($query);
            
            if ($years = $db->loadColumn())
            {
                //maximum years to show, calculation if years should be deleted from array
                $number_years = JComponentHelper::getParams('com_tkdclub')->get('training_years', 5);
                $years_in_array = count($years)-1;
                
                if (($years_in_array - $number_years) > 0)
                {
                    $years_to_delete = ($number_years - $years_in_array) * -1;
                    
                    
                    for ($i = 0; $years_to_delete > 0; $i++)
                    {
                        unset($years[$i]);
                        $years_to_delete--;
                    }
                }
                
                foreach($years as $year)
                {

                    $options[] = JHtml::_('select.option', $year, $year);

                }
                
                $options = array_reverse($options);

                if ($this->form) //checking if we are in a form, then merge additional xml data
                {
                    $options = array_merge(parent::getOptions(), $options); 
                }
                
                return $options;
            }
            
            return parent::getOptions();
        }
    
}