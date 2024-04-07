<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/**
 * Field for nations
 *
 */
class NationsField extends ListField
{
    /**
     * The form field type.
     *
     * @var		string
     * 
     */
    protected $type = 'Nations';

    /**
     * Method to get the field input markup for nations field.
     * 
     * The field is designed to provide always the 7 most common nations (at least for our geographical enviroment ;-) :
     *  AUT,GER,SUI,ITA,CRO,BIH,SRB
     * 
     * By adding other nations in the component configuration more nations can be added.
     *
     * @return  string	The field input markup.
     *
     */
    public function getOptions()
    {
        $nations = TkdclubHelper::getList(ComponentHelper::getParams('com_tkdclub')
            ->get('nations', ',AUT,GER,SUI,ITA,CRO,BIH,SRB'));

        foreach ($nations as $nation) {
            $options[] = HTMLHelper::_('select.option', $nation, $nation);
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
