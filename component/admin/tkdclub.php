<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

// staying in the selected tab after saving
JHtml::_('behavior.tabstate');

// access check
if (!JFactory::getUser()->authorise('core.manage', 'com_tkdclub'))
{
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = JControllerLegacy::getInstance('tkdclub');
$input = JFactory::getApplication()->input;

$controller->execute($input->get('task'));
$controller->redirect();