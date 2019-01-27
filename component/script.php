<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Filesystem\File;
use \Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

class com_tkdclubInstallerScript
{
    public function update($parent)
    {
        $this->deleteUnexisting();
        echo '<p>' . JText::sprintf('vom update', $parent->get('manifest')->version) . '</p>';
    }

    public function deleteUnexisting()
    {
        $files = array(

            // site
            '/components/com_tkdclub/views/index.html',
            '/components/com_tkdclub/models/eventpart.php',
            '/components/com_tkdclub/models/index.html',
            '/components/com_tkdclub/models/forms/eventpart.xml',
            '/components/com_tkdclub/controllers/eventpart.json.php',
            '/components/com_tkdclub/controllers/eventpart.php',
            '/components/com_tkdclub/controllers/index.html',
            '/components/com_tkdclub/language/index.html',
            '/components/com_tkdclub/language/de-DE/index.html',
            '/components/com_tkdclub/assets/css/medallist.css',
            '/components/com_tkdclub/assets/images/bronce.png',
            '/components/com_tkdclub/assets/images/bronze.png',
            '/components/com_tkdclub/assets/images/gold.png',
            '/components/com_tkdclub/assets/images/silver.png',
            '/components/com_tkdclub/assets/images/silber.png',

            // admin
            '/administrator/components/com_tkdclub/assets/index.html',
            '/administrator/components/com_tkdclub/assets/css/index.html',
            '/administrator/components/com_tkdclub/assets/images/index.html',
            '/administrator/components/com_tkdclub/assets/images/active.png',
            '/administrator/components/com_tkdclub/assets/images/bronce.png',
            '/administrator/components/com_tkdclub/assets/images/exam_add-32.png',
            '/administrator/components/com_tkdclub/assets/images/exam_edit-32.png',
            '/administrator/components/com_tkdclub/assets/images/exam_publish-32.png',
            '/administrator/components/com_tkdclub/assets/images/exams-16.png',
            '/administrator/components/com_tkdclub/assets/images/exams-48.png',
            '/administrator/components/com_tkdclub/assets/images/exam_unpublish.png',
            '/administrator/components/com_tkdclub/assets/images/gold.png',
            '/administrator/components/com_tkdclub/assets/images/inactive.png',
            '/administrator/components/com_tkdclub/assets/images/loading-24.gif',
            '/administrator/components/com_tkdclub/assets/images/medalbronce-16.png',
            '/administrator/components/com_tkdclub/assets/images/medalgold-16.png',
            '/administrator/components/com_tkdclub/assets/images/medals-16.png',
            '/administrator/components/com_tkdclub/assets/images/medals-48.png',
            '/administrator/components/com_tkdclub/assets/images/medalsilver-16.png',
            '/administrator/components/com_tkdclub/assets/images/member-48.png',
            '/administrator/components/com_tkdclub/assets/images/members-16.png',
            '/administrator/components/com_tkdclub/assets/images/members-48.png',
            '/administrator/components/com_tkdclub/assets/images/optimised.svg',
            '/administrator/components/com_tkdclub/assets/images/save-24.png',
            '/administrator/components/com_tkdclub/assets/images/silver.png',
            '/administrator/components/com_tkdclub/assets/images/supporter.png',
            '/administrator/components/com_tkdclub/assets/images/exam_unpublish-32.png',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-16.png',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-16-white.png',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-32.png',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-48.png',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-black.svg',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-kick.svg',
            '/administrator/components/com_tkdclub/assets/images/taekwondo-kick-white.svg',
            '/administrator/components/com_tkdclub/assets/images/tkd-14-black.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-14-white.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-18-black.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-18-white.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-24-black.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-24-white.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-36-black.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-36-white.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-48-black.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-48-white.png',
            '/administrator/components/com_tkdclub/assets/images/tkd-fighter.svg',
            '/administrator/components/com_tkdclub/assets/images/trainings-16.png',
            '/administrator/components/com_tkdclub/assets/images/trainings-48.png',
            '/administrator/components/com_tkdclub/assets/images/xls.png',
            '/administrator/components/com_tkdclub/assets/js/index.html',
            '/administrator/components/com_tkdclub/assets/js/agedistchart.js',
            '/administrator/components/com_tkdclub/assets/js/genderchart.js',
            '/administrator/components/com_tkdclub/assets/js/statechart.js',
            '/administrator/components/com_tkdclub/assets/js/submitbutton.js',
            '/administrator/components/com_tkdclub/assets/js/tkdclub.js',
            '/administrator/components/com_tkdclub/assets/js/trainnumberschart.js',
            '/administrator/components/com_tkdclub/assets/js/trainpartschart.js',
            '/administrator/components/com_tkdclub/assets/js/traintypeschart.js',
            '/administrator/components/com_tkdclub/controllers/equipments.php',
            '/administrator/components/com_tkdclub/controllers/index.html',
            '/administrator/components/com_tkdclub/controllers/equipment.php',
            '/administrator/components/com_tkdclub/controllers/eventparts.php',
            '/administrator/components/com_tkdclub/controllers/eventpart.php',
            '/administrator/components/com_tkdclub/controllers/exams.php',
            '/administrator/components/com_tkdclub/controllers/exam.php',
            '/administrator/components/com_tkdclub/controllers/examparts.php',
            '/administrator/components/com_tkdclub/controllers/exampart.php',
            '/administrator/components/com_tkdclub/controllers/mail.php',
            '/administrator/components/com_tkdclub/controllers/exampart.php',
            '/administrator/components/com_tkdclub/controllers/stats.php',
            '/administrator/components/com_tkdclub/helpers/tkdclub.php',
            '/administrator/components/com_tkdclub/helpers/index.html',
            '/administrator/components/com_tkdclub/includes/membercharts.php',
            '/administrator/components/com_tkdclub/includes/trainingscharts.php',
            '/administrator/components/com_tkdclub/language/index.html',
            '/administrator/components/com_tkdclub/language/de-DE/index.html',
            '/administrator/components/com_tkdclub/models/index.html',
            '/administrator/components/com_tkdclub/models/equipments.php',
            '/administrator/components/com_tkdclub/models/equipment.php',
            '/administrator/components/com_tkdclub/models/eventparts.php',
            '/administrator/components/com_tkdclub/models/eventpart.php',
            '/administrator/components/com_tkdclub/models/exams.php',
            '/administrator/components/com_tkdclub/models/exam.php',
            '/administrator/components/com_tkdclub/models/examparts.php',
            '/administrator/components/com_tkdclub/models/exampart.php',
            '/administrator/components/com_tkdclub/models/mail.php',
            '/administrator/components/com_tkdclub/models/stats.php',
            '/administrator/components/com_tkdclub/models/fields/index.html',
            '/administrator/components/com_tkdclub/models/fields/equipcat.php',
            '/administrator/components/com_tkdclub/models/fields/examparts.php',
            '/administrator/components/com_tkdclub/models/fields/exams.php',
            '/administrator/components/com_tkdclub/models/fields/years.php',
            '/administrator/components/com_tkdclub/models/forms/index.html',
            '/administrator/components/com_tkdclub/models/forms/equipment.xml',
            '/administrator/components/com_tkdclub/models/forms/eventpart.xml',
            '/administrator/components/com_tkdclub/models/forms/exam.xml',
            '/administrator/components/com_tkdclub/models/forms/exampart.xml',
            '/administrator/components/com_tkdclub/models/forms/filter_examparts.xml',
            '/administrator/components/com_tkdclub/models/forms/filter_eventparts.xml',
            '/administrator/components/com_tkdclub/models/forms/mail.xml',
            '/administrator/components/com_tkdclub/sql/updates/index.html',
            '/administrator/components/com_tkdclub/sql/index.html',
            '/administrator/components/com_tkdclub/tables/index.html',
            '/administrator/components/com_tkdclub/tables/equipments.php',
            '/administrator/components/com_tkdclub/tables/eventparts.php',
            '/administrator/components/com_tkdclub/tables/examparts.php',
            '/administrator/components/com_tkdclub/tables/exams.php',
            '/administrator/components/com_tkdclub/views/index.html',
        );

        $folders = array(

            // site
            '/components/com_tkdclub/views/eventpart',

            //admin
            '/administrator/components/com_tkdclub/views/equipment',
            '/administrator/components/com_tkdclub/views/equipments',
            '/administrator/components/com_tkdclub/views/eventpart',
            '/administrator/components/com_tkdclub/views/eventparts',
            '/administrator/components/com_tkdclub/views/exams',
            '/administrator/components/com_tkdclub/views/exam',
            '/administrator/components/com_tkdclub/views/mail',
            '/administrator/components/com_tkdclub/views/exampart',
            '/administrator/components/com_tkdclub/views/examparts',
            '/administrator/components/com_tkdclub/views/stats',
        );

        foreach ($files as $file)
		{
			if (File::exists(JPATH_ROOT . $file) && !File::delete(JPATH_ROOT . $file))
			{
				echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br />';
			}
		}

		foreach ($folders as $folder)
		{
			if (Folder::exists(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder))
			{
				echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder) . '<br />';
			}
		}

    }

}