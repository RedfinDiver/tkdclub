<?php
/**
 * @package     Administrator
 * @subpackage	com_tkdclub
 * @copyright	Copyright (C) 2016 Markus Moser, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
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
