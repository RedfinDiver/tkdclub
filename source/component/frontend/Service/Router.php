<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Redfindiver\Component\Tkdclub\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\RouterViewConfiguration;

/**
 * Routing class from com_tkdclub
 *
 * @since  5.0
 */
class Router extends RouterView
{
	/**
     * Content Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
		$medal  = new RouterViewConfiguration('medal');
        $this->registerView($medal);
        
		$medals  = new RouterViewConfiguration('medals');
        $this->registerView($medals);

		$participant  = new RouterViewConfiguration('participant');
        $this->registerView($participant);

		$training  = new RouterViewConfiguration('training');
        $this->registerView($training);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }
}
