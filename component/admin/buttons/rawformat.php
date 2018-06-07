<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Renders a csv download button
 */
class JToolbarButtonRawFormat extends JToolbarButtonStandard
{
	protected $_name = 'RawFormat';

	/**
	 * Get the JavaScript command for the button
	 * Refer to the script function RawFormatSubmitbutton in stead of the
	 * standard Joomla.submitbutton
	 */
	protected function _getCommand($name, $task, $list)
	{
		return	str_replace("Joomla.submitbutton", "RawFormatSubmitbutton",
			parent::_getCommand($name, $task, $list) );
	}
}