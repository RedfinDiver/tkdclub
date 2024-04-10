<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

 \defined('_JEXEC') or die;

 use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @since  5.0.1
 */
class Com_TkdclubInstallerScript
{
    /**
     * Function called before extension installation/update/removal procedure commences
     *
     * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     *
     * @since   5.0.1
     */
    public function preflight($type, $parent)
    {
        $files = [
            '/components/com_tkdclub/src/Controller/participant.json.php'
        ];

       
        foreach ($files as $file) {
            if (File::exists(JPATH_ROOT . $file)) {
                File::delete(JPATH_ROOT . $file);
            }
        }

        echo 'hier bin ich!!!!';
        return true;
    }
}