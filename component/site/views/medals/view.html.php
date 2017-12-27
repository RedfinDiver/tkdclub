<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperMembers', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/members.php');

class TkdClubViewMedals extends JViewLegacy
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