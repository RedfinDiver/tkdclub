<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller to send emails to members and newsletter subscribers
 */
class TkdclubControllerEmail extends JControllerForm
{
	/**
	 * Send the mail
	 *
	 * @return void
	 *
	 */
	public function send()
	{
        // Check for request forgeries.
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Redirect to admin index if mass mailer disabled in config
		if (JFactory::getApplication()->get('massmailoff', 0) == 1)
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php', false));
		}

		$model = $this->getModel('email');

		if ($model->send())
		{
			$type = 'message';
		}
		else
		{
			$type = 'error';
		}

		$msg = $model->getError();
		$this->setredirect('index.php?option=com_tkdclub&view=email', $msg, $type);
	}
}