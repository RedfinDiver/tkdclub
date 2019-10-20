<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * view-class for edit-view: 'participant'
 */
class TkdClubViewParticipant extends HtmlView
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