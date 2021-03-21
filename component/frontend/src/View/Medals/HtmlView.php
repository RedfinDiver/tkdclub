<?php
/**
 * @package    Taekwondo Club Site
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Site\View\Medals;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

class HtmlView extends BaseHtmlView
{
    /**
	 * An array of items
	 *
	 * @var  array
	 */
    protected $items;

    /**
	 * The model state
	 *
	 * @var  \JObject
	 */
    protected $state;

     /**
	 * The pagination object
	 *
	 * @var  \JPagination
	 */
    protected $pagination;
    
    /**
	 * Number of all visible rows in view
	 *
	 * @var  integer
	 */
    protected $total;

    /**
	 * Number of all rows in table
	 *
	 * @var  integer
	 */
    protected $allrows;

    /**
	 * An array of member ids and names
	 *
	 * @var  array
	 */
    protected $memberlist;

    /**
	 * An array of data for statistics
	 *
	 * @var  array
	 */
    protected $medaldata;
    
    public function display($tpl = null)
    {
        $this->items        = $this->get('Items');
        $this->state        = $this->get('State');
        $this->pagination   = $this->get('Pagination');
        $this->total        = $this->get('Total');
        $this->allrows      = $this->get('Allrows');
        $this->memberlist   = TkdclubHelper::getMemberlist();
        $this->medaldata    = $this->get('Medaldata');
        
        parent::display($tpl);
    }
}