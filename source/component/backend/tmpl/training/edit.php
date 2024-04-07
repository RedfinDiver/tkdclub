<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_tkdclub.trainer-select')
    ->useStyle('com_tkdclub.tkdclub-admin');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="training-form" class="form-validate">
    <div class="row form-vertical">
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('date'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('type'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('participants'); ?>
        </div>
        <div class="col-12 col-md-3">
            <?php echo $this->form->renderField('participant_ids'); ?>
        </div>
    </div>
    <div>
        <?php echo HtmlHelper::_('uitab.startTabSet', 'myTab', array('active' => 'trainingdata')); ?>
        <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'trainingdata', empty($this->item->id) ? Text::_('COM_TKDCLUB_TRAINING_NEW_TAB', true) : Text::_('COM_TKDCLUB_TRAINING_EDIT', true)); ?>
            <div class="row">
                <div class="col-lg-6">
                    <fieldset id="fieldset-trainer_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_TRAINING_TRAINER'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('class_lead'); ?>
                        </div>
                    </fieldset>
                    <fieldset id="fieldset-trainer_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></legend>
                        <div>
                            <?php echo $this->form->renderField('notes'); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-6">
                    <fieldset id="fieldset-trainer_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('assistents'); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
        <?php echo HtmlHelper::_('uitab.endTab'); ?>

        <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
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

        <?php echo HtmlHelper::_('uitab.endTab'); ?>
        <?php echo HtmlHelper::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo HtmlHelper::_('form.token'); ?>

    </div>
</form>