<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Supports the options-markup for promotions
 * used in backend
 */
class PromotionsField extends ListField
{
    /**
     * The form field type.
     *
     * @var		string
     */
    protected $type = 'promotions';
    
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

        // add columns from promotions list
        $query->select('id, date, city, promotion_state, type');
        $query->from($db->quoteName('#__tkdclub_promotions'))
              ->order($db->quoteName('date') .  ' ASC');
        
        // only activated promotions
        $query->where('promotion_state = 1');

        // instate variable options
        $options = array();

        $db->setQuery($query);
        $promotions = $db->loadObjectList();

        foreach ($promotions as $i => $promotion)
        {
            $text = '';
            $type = '';

            if ($this->element['isCandidateForm'] == 'true') // checking for candidate form, in select field in list view we don´t need promotion type in text
            {
                $promotion->type == 'kup' ? $type = Text::_('COM_TKDCLUB_KUP') : $type = Text::_('COM_TKDCLUB_DAN');
                $text = HTMLHelper::_('date', $promotion->date, Text::_('DATE_FORMAT_LC4')) . ' ' . '(' . $promotion->city . ' / ' . $type .')';
            }
            else
            {
                $text = HTMLHelper::_('date', $promotion->date, Text::_('DATE_FORMAT_LC4')) . ' ' . '(' . $promotion->city .')';
            }

            $options[] = HTMLHelper::_('select.option', $promotion->id, $text);
        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }
        
        return $options;  
    }
    
}