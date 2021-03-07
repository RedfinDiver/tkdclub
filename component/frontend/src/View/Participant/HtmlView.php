<?php
/**
 * @package    Taekwondo Club Site
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Site\View\Participant;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * view-class for edit-view: 'participant'
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $event_data;

    public function display($tpl = null)
    {
        
        $this->form = $this->get('Form');
        $this->event_data = $this->get('Eventdata');

        parent::display($tpl);
    }
}