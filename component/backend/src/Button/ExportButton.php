<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Button;

defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\Button\StandardButton;

/**
 * Renders a csv export button
 */
class ExportButton extends StandardButton
{
	protected $_name = 'Export';

	/**
	 * Get the JavaScript command for the button
	 * Refer to the script function RawFormatSubmitbutton in stead of the
	 * standard Joomla.submitbutton
	 */
	protected function _getCommand()
	{
		return	str_replace("Joomla.submitbutton", "Tkdclub_exportbutton",
			parent::_getCommand());
	}
}