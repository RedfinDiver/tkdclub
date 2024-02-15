<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

//test email active
if ($this->email_test) {
    Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_EMAIL_TESTMAIL_ACTIVE') . $this->email_test, 'message');
}

HtmlHelper::_('bootstrap.tooltip');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_tkdclub.email')
    ->useStyle('com_tkdclub.tkdclub-admin');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=email'); ?>" name="adminForm" method="post" id="adminForm" class="form-validate" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-6">
            <fieldset id="fieldset-personal_data" class="options-form">
                <legend><?php echo Text::_('COM_TKDCLUB_EMAIL_DATA'); ?></legend>
                <div>
                    <?php echo $this->form->renderFieldset('email'); ?>
                </div>
            </fieldset>
        </div>
        <div class="col-lg-6">
            <fieldset id="fieldset-taekwondo_data" class="options-form">
                <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_EMAIL_RECPIENTS'); ?></legend>
                <div>
                    <?php echo $this->form->renderFieldset('recipients'); ?>
                </div>
            </fieldset>
            <fieldset id="fieldset-taekwondo_data" class="options-form">
                <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_EMAIL_ATTACHMENTS'); ?></legend>
                <div>
                    <?php echo $this->form->renderFieldset('attachments'); ?>
                </div>
            </fieldset>
        </div>
    </div>
</form>