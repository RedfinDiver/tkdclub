<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class TkdClubController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array()) 
    {   
        // At first request save the Item-params in the user-State variable to make sure to have it available at all time
        $app = JFactory::getApplication();
        $item_params = $app->getMenu()->getActive()->params->toObject();

        $app->setUserState('com_tkdclub.participant.itemparams', $item_params);
        
        parent::display($cachable, $urlparams);
    }
}