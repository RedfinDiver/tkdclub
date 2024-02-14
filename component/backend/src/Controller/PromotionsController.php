<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * listview promotions controller
 */
class PromotionsController extends AdminController
{
    protected $text_prefix = 'COM_TKDCLUB_PROMOTION';
    
    public function getModel($name = 'Promotion', $prefix = 'Administrator', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Toggle on/off the stats
     * 
     * With this method the statistics are switched on or off
     * in the promotions list view
     * 
     * @return void
     */ 
    public function togglestats()
    {
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $session = Factory::getSession();

        if (!$session->get('togglestats_promotions', null, 'tkdclub'))
        {
            $session->set('togglestats_promotions', 'ON', 'tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_ON';  
        }
        else
        {
            $session->clear('togglestats_promotions','tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_OFF';  
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=promotions', Text::_($msg));
    }
}
