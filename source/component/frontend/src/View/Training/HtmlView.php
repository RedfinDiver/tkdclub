<?php
/**
 * @package    Taekwondo Club Site
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Site\View\Training;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View-class for edit-view training
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    
    public function display($tpl = null)
    {
        $user = Factory::getUser();
        $app  = Factory::getApplication();
        
        if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_tkdclub');
        }
        
        if ($authorised !== true)
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return false;
		}

        $this->state = $this->get('State');
        $this->form  = $this->get('Form');
        
        parent::display($tpl);
    }
}