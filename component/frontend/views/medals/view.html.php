<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

class TkdClubViewMedals extends HtmlView
{
    protected $items;
    protected $state;
    protected $pagination;
    protected $total;
    protected $allrows;
    protected $memberlist;
    
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->total = $this->get('Total');
        $this->allrows = $this->get('Allrows');
        $this->memberlist = $this->get('Memberlist');
        parent::display($tpl);
    }
}