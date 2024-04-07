<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Supports the options-markup for all candidates
 */
class CandidatesField extends ListField
{       
    /**
     * The form field type.
     *
     * @var		string
     */
    protected $type = 'candidates';

    /**
     * Method to get the field input markup for a field with all memers.
     *
     * @return  string	The field input markup.
     *
     */       
    public function getOptions()
    {
            // loading all member ids for candidates
            $db = Factory::getDbo();
            $query = $db->getQuery(true);

            // select all necessary columns from candidates table
            // select id_candidate as distinct
            $query->select('DISTINCT a.id_candidate');
            $query->select('a.id_promotion');
            $query->from('#__tkdclub_candidates AS a');

            // adding necessary columns from members table
            $query->select('b.firstname, b.lastname');
            $query->join('LEFT', '#__tkdclub_members AS b ON a.id_candidate = b.id');

            // adding promotions table and selecting only candidates where promotion is active
            $query->join('LEFT', '#__tkdclub_promotions AS c ON a.id_promotion = c.id');
            $query->where('c.promotion_state = 1');

            $db->setQuery($query);
            $items = $db->loadObjectList();

            $options = array();

            foreach ($items as $item)
            {
                 $options[] = HTMLHelper::_('select.option', $item->id_candidate, $item->firstname . ' ' . $item->lastname);
            }

            if ($this->form) //checking if we are in a form, then merge additional xml data
            {
                $options = array_merge(parent::getOptions(), $options); 
            }

            return $options;
    }

        
}