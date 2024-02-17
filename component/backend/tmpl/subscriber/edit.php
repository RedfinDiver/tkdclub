<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useStyle('com_tkdclub.tkdclub-admin');

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="subscriber-form" class="form-validate">
    <div>
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'subscriberdata')); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'participant', empty($this->item->id) ? Text::_('COM_TKDCLUB_SUBSCRIBER_NEW_TAB', true) : Text::_('COM_TKDCLUB_SUBSCRIBER_EDIT_TAB', true)); ?>
                <div class="row">
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'participant', empty($this->item->id) ? Text::_('COM_TKDCLUB_SUBSCRIBER_NEW_TAB', true) : Text::_('COM_TKDCLUB_SUBSCRIBER_EDIT_TAB', true)); ?>
                    <?php echo $this->form->renderFieldset('subscriberdata') ?>
                </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
            <?php if (empty($this->item->id)) : ?>
                <div class="alert alert-info">
                    <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                    <?php echo Text::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                </div>
            <?php else : ?>
                <div>
                    <?php foreach ($this->form->getFieldset('item_data') as $field) : ?>
                        <?php echo $field->renderField(); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.endTabset'); ?>
    </div>
    <div>
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>