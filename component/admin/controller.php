<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
JLoader::register('TkdclubHelperSidebar', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/sidebar.php');
/**
 * TKD Club Main Controller
 */
class TkdClubController extends JControllerLegacy
{  
    protected $default_view = 'members';

    public function display($cachable = false, $urlparams = false)
	{
		$input  = JFactory::getApplication()->input;        
		$view   = $input->get('view', $this->default_view);
		$layout = $input->get('layout', 'default');     
        TkdclubHelperSidebar::addSubmenu($view);

        parent::display($cachable, $urlparams);
	}
}
