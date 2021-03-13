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

/**
 * Supports the options-markup for medal years
 * used in backend and frontent
 */
class MedalyearsField extends ListField
{
    /**
     * @var		string
     */
    protected $type = 'Medalyears';
    
    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     */
    public function getOptions()
    {   
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('DISTINCT YEAR(date)');
        $query->from($db->quoteName('#__tkdclub_medals'));
        $query->order('date DESC');

        $db->setQuery($query);
        $years = $db->loadColumn();

        $options = array();
                    
        foreach($years as $year)
        {
            $options[] = HTMLHelper::_('select.option', $year, $year);
        }
        
        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }
        
        return $options;       
    }
    
}
