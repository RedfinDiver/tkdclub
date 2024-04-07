<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Redfindiver\Component\Tkdclub\Administrator\Extension\TkdclubComponent;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Component\Router\RouterFactoryInterface;

/**
 * The Tkdclub service provider
 *
 * @since  4.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('\\Redfindiver\\Component\\Tkdclub'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Redfindiver\\Component\\Tkdclub'));
		$container->registerServiceProvider(new RouterFactory('\\Redfindiver\\Component\\Tkdclub'));

		$container->set(
			ComponentInterface::class,
			function (Container $container)
			{
				$component = new TkdclubComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setMVCFactory($container->get(MVCFactoryInterface::class));

				//$component->setRouterFactory($container->get(RouterFactoryInterface::class));
	
				return $component;
			}
		);
	}
};
