<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Redfindiver\Plugin\WebServices\Tkdclub\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;

/**
 * Web Services adapter for com_tkdclub.
 *
 * @since  5.0.0
 */
final class Tkdclub extends CMSPlugin
{
    /**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

    /**
     * Registers com_tkdclub's API's routes in the application
     *
     * @param   ApiRouter  &$router  The API Routing object
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function onBeforeApiRoute(&$router)
    {
        $router->createCRUDRoutes(
            'v1/tkdclub/trainings',
            'trainings',
            ['component' => 'com_tkdclub']
        );

        $router->createCRUDRoutes(
            'v1/tkdclub/members',
            'members',
            ['component' => 'com_tkdclub']
        );

        $router->createCRUDRoutes(
            'v1/tkdclub/medals',
            'medals',
            ['component' => 'com_tkdclub']
        );

        $router->createCRUDRoutes(
            'v1/tkdclub/promotions',
            'promotions',
            ['component' => 'com_tkdclub']
        );

        $router->createCRUDRoutes(
            'v1/tkdclub/candidates',
            'candidates',
            ['component' => 'com_tkdclub']
        );
    }
}