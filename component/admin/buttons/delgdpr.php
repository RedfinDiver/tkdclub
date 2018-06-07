<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Renders a gdpr delete button
 */
class JToolbarButtonDelgdpr extends JToolbarButtonConfirm
{
	protected $_name = 'Delgdpr';

	/**
	 * This button gives a message if the user is sure to delete all data
     * compliant with the gdpr 
	 */
    protected function _getCommand($msg, $name, $task, $list)
	{
		return	parent::_getCommand($msg, $name, $task, $list = false);
	}
}
