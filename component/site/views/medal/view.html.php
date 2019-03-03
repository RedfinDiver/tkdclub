<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * view-class for edit-view: 'medal'
 */
class TkdClubViewMedal extends HtmlView
{
    protected $form;
    
    public function display($tpl = null)
    {
        $user = JFactory::getUser();
        $app  = JFactory::getApplication();
        
        if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_tkdclub');
        }
        
        if ($authorised !== true)
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return false;
		}
        
        $this->form = $this->get('Form');

        parent::display($tpl);
    }
}