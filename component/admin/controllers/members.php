<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
* listview members controller
*/
class TkdclubControllerMembers extends AdminController
{
    protected $text_prefix = 'COM_TKDCLUB_MEMBER';

    public function getModel($name = 'member', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Toggle on/off the stats
     * 
     * With this method the statistics are switched on or off
     * in the members list view
     * 
     * @return void
     */ 
    public function togglestats()
    {
        // Check for request forgeries.
        Session::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $session = Factory::getSession();

        if (!$session->get('togglestats_members', null, 'tkdclub'))
        {
            $session->set('togglestats_members', 'ON', 'tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_ON';  
        }
        else
        {
            $session->clear('togglestats_members','tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_OFF';  
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=members', Text::_($msg));
    }

    /**
     * For Ajax call in statistic view
     */
    public function getmemberdata()
    {
        // Check for request forgeries.
        Session::checkToken('GET') or jexit(Text::_('JINVALID_TOKEN'));

        $model = $this->getModel($name = 'members', $prefix = 'TkdClubModel', $config = array());
        $data = $model->getmemberdata();
        echo json_encode($data);

        Factory::getApplication()->close();
    }
}