<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdClubHelperList', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/list.php');

/**
 * Supports the options-markup from component parameter
 */
class JFormFieldTraintypes extends JFormFieldList
{
    /**
     * The form field type.
     */
    protected $type = 'traintypes';
        
    /**
     * Method to get the field input markup for field 'traintypes'
     */    
    public function getOptions()
    {
        $types = TkdClubHelperList::getList(JComponentHelper::getParams('com_tkdclub')->get('training_types'));

        if ($types)
        {
            foreach($types as $type)
            {
                $options[] = JHtml::_('select.option', $type, $type);
            }

            $options = array_merge(parent::getOptions(), $options);

            return $options; 
        }

        return parent::getOptions();
    }
    
}