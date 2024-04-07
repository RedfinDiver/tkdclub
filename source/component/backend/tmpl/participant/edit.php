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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate')
    ->useStyle('com_tkdclub.tkdclub-admin');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="participant-form" class="form-validate">
    <div class="row form-vertical">
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('event_id'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('firstname'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('lastname'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('registered'); ?>
        </div>
    </div>
    <div>
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'participant')); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'participant', empty($this->item->id) ? Text::_('COM_TKDCLUB_PARTICIPANT_NEW_TAB', true) : Text::_('COM_TKDCLUB_PARTICIPANT_EDIT_TAB', true)); ?>
            <div class="row">
                <div class="col-lg-6">
                    <fieldset id="fieldset-optional_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_OPTIONAL_DATA'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('optional_data'); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-6">
                    <fieldset id="fieldset-optional_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('notes'); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>
                <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                <?php if (empty($this->item->medal_id)) : ?>
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
    <div>
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>