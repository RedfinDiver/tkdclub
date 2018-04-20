<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Participants list view controller
 */
class TkdClubControllerParticipants extends JControllerAdmin
{
    public function getModel($name = 'participants', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Toggle on/off the stats
     * 
     * With this method the statistics are switched on or off
     * in the participants list view
     * 
     * @return void
     */ 
    public function togglestats()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $session = JFactory::getSession();

        if (!$session->get('togglestats_participants', null, 'tkdclub'))
        {
            $session->set('togglestats_participants', 'ON', 'tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_ON';  
        }
        else
        {
            $session->clear('togglestats_participants','tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_OFF';  
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=participants', JText::_($msg));
    }
}