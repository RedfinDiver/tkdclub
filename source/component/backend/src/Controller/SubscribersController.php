<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class SubscribersController extends AdminController
{
    protected $text_prefix = 'COM_TKDCLUB_SUBSCRIBERS';

    public function getModel($name = 'subscriber', $prefix = 'Administrator', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}