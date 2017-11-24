<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
JLoader::register('TkdclubHelperSidebar', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/sidebar.php');

/**
 * TKD Club Main Controller
 */
class TkdClubController extends JControllerLegacy
{  
    protected $default_view = 'members';

    public function display($cachable = false, $urlparams = false)
	{    
		$view   = $this->input->get('view', $this->default_view);
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt($view . '_id');     
		TkdclubHelperSidebar::addSubmenu($view);
		
		// Check for edit form.
		if ($view == 'member' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.member', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=members', false));

			return false;
		}
		
		if ($view == 'training' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.training', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=trainings', false));

			return false;
		}

		if ($view == 'medal' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.medal', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=medals', false));

			return false;
		}

		if ($view == 'promotion' && $layout == 'edit' && !$this->checkEditId('com_tkdclub.edit.promotion', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=promotions', false));

			return false;
		}

        parent::display($cachable, $urlparams);
	}
}
