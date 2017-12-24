<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('TkdClubHelperList', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/list.php');

/**
 * Supports the options-markup from parameters
 *
 * @since  1.0
 */
class JFormFieldNations extends JFormFieldList
{       
    /**
     * The form field type.
     *
     * @var		string
     * @since   1.0
     */
    protected $type = 'nations';

    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
     */        
    public function getOptions()
    {
        $nations = TkdClubHelperList::getList(JComponentHelper::getParams('com_tkdclub')
                    ->get('nations', ',AUT,GER,SUI,ITA,CRO,BIH,SRB'));

        foreach($nations as $nation)
        {
            $options[] = JHtml::_('select.option', $nation, $nation);
        }

        $options = array_merge(parent::getOptions(), $options);
        
        return $options;  
    }
        
}
