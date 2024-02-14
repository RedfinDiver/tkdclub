<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_tkdclub.getcandidatedata')
    ->useStyle('com_tkdclub.tkdclub-admin');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="candidate-form" class="form-validate">
    <div class="row form-vertical">
        <div class="col-12 col-md-4">
            <?php echo $this->form->renderField('id_promotion'); ?>
        </div>
        <div class="col-12 col-md-4">
            <?php echo $this->form->renderField('id_candidate'); ?>
        </div>
        <div class="col-12 col-md-4">
            <?php echo $this->form->renderField('test_state'); ?>
        </div>
    </div>
    <div>
        <?php echo HtmlHelper::_('uitab.startTabSet', 'myTab', array('active' => 'exampart_data')); ?>
        <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'promotions', empty($this->item->promotion_id) ? Text::_('COM_TKDCLUB_CANDIDATE_ADD_TAB', true) : Text::sprintf('COM_TKDCLUB_CANDIDATE_EDIT_TAB', $this->item->promotion_id, true)); ?>
        <div class="row">
                    <div class="col-lg-6">
                        <fieldset id="fieldset-promotion-data" class="options-form">
                            <legend><?php echo Text::_('COM_TKDCLUB_CANDIDATE_DATA'); ?></legend>
                            <div>
                                <?php echo $this->form->renderFieldset('readonly_data'); ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-lg-6">
                        <fieldset id="fieldset-more-data" class="options-form">
                            <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></legend>
                            <div>
                                <?php echo $this->form->renderField('notes'); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
        <?php echo HtmlHelper::_('uitab.endTab'); ?>
        <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
            <?php if (empty($this->item->promotion_id)) : ?>
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
    </div>
    

        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <div id="waittext" class="hidden"><?php echo Text::_('COM_TKDCLUB_WAIT_FOR_AJAX_RESPONSE'); ?></div>
        <div id="errortext" class="hidden"><?php echo Text::_('COM_TKDCLUB_ERROR_CHECK_MESSAGES'); ?></div>
</form>