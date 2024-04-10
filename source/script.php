<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

 \defined('_JEXEC') or die;
 
 use Joomla\CMS\Installer\InstallerScript;
 
 /**
  * Installation class to perform additional changes during install/uninstall/update
  *
  * @since  __DEPLOY_VERSION__
  */
 class Pkg_TkdclubInstallerScript extends InstallerScript
 {
     /**
      * Extension script constructor.
      *
      * @since   __DEPLOY_VERSION__
      */
     public function __construct()
     {
         $this->minimumJoomla = '5.0.0';
         $this->minimumPhp    = JOOMLA_MINIMUM_PHP;
     }
 }
 