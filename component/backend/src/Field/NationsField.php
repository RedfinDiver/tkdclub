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
 * Supports the options-markup from parameters
 *
 * @since  1.0
 */
class NationsField extends ListField
{
    /**
     * The form field type.
     *
     * @var		string
     * @since   1.0
     */
    protected $type = 'Nations';

    /**
     * Method to get the field input markup.
     *
     * @return  string	The field input markup.
     *
     * @since   1.0
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
