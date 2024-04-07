<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Controller to send emails to members and newsletter subscribers
 */
class EmailController extends FormController
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
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

		// Redirect to admin index if mass mailer disabled in config
		if (Factory::getApplication()->get('massmailoff', 0) == 1)
		{
			Factory::getApplication()->redirect(Route::_('index.php', false));
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