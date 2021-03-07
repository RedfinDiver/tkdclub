<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;

/**
 * Routing class from com_tkdclub
 *
 * @since  2.1
 */
class Router extends RouterBase
{
	/**
	 * Build the route for the com_tkdclub component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.0.0
	 */
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		
		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
		}


		if (isset($query['view']))
		{
			$view = $query['view'];

			if (empty($query['Itemid']) || empty($menuItem) || $menuItem->component != 'com_tkdclub')
			{
				$segments[] = $query['view'];
			}

			// We need to keep the view for forms since they never have their own menu item
			if ($view != 'form')
			{
				unset($query['view']);
			}
		}


		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.0.0
	 */
	public function parse(&$segments)
	{
		//nothing to parse for the Version 3.0.0
	}
}
