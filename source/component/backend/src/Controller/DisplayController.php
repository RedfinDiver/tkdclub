<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Tkdclub master display controller administrator.
 *
 * @since  3.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'members';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  BaseController|bool  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$view   = $this->input->get('view', $this->default_view);
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt($view . '_id');

		// Check for edit form.
		if ($view == 'member' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.member', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=members', false));

			return false;
		}

		if ($view == 'training' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.training', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=trainings', false));

			return false;
		}

		if ($view == 'medal' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.medal', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=medals', false));

			return false;
		}

		if ($view == 'promotion' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.promotion', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=promotions', false));

			return false;
		}

		if ($view == 'candidate' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.candidate', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=canditates', false));

			return false;
		}

		if ($view == 'event' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.event', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=events', false));

			return false;
		}

		if ($view == 'participant' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.participant', $id)) {
			
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_tkdclub&view=participants', false));

			return false;
		}

		parent::display();
	}
}