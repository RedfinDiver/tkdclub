<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports the options-markup for promotions
 * used in backend
 */
class JFormFieldPromotions extends JFormFieldList
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // add columns from promotions list
        $query->select('promotion_id, date, city, promotion_state, type');
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
                $promotion->type == 'kup' ? $type = JText::_('COM_TKDCLUB_KUP') : $type = JText::_('COM_TKDCLUB_DAN');
                $text = JHtml::_('date', $promotion->date, JText::_('DATE_FORMAT_LC4')) . ' ' . '(' . $promotion->city . ' / ' . $type .')';
            }
            else
            {
                $text = JHtml::_('date', $promotion->date, JText::_('DATE_FORMAT_LC4')) . ' ' . '(' . $promotion->city .')';
            }

            $options[] = JHtml::_('select.option', $promotion->promotion_id, $text);
        }

        if ($this->form) //checking if we are in a form, then merge additional xml data
        {
            $options = array_merge(parent::getOptions(), $options); 
        }
        
        return $options;  
    }
    
}