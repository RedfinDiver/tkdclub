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
use Joomla\CMS\Session\Session;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate')
    ->useScript('com_tkdclub.mask-iban')
    ->useScript('com_tkdclub.iban')
    ->useScript('com_tkdclub.member-format-iban')
    ->useStyle('com_tkdclub.tkdclub-admin');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="member-form" class="form-validate" enctype="multipart/form-data">   
    <div class="row form-vertical">
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('lastname'); ?>
        </div>
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('firstname'); ?>
        </div>
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('birthdate'); ?>
        </div>
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('sex'); ?>
        </div>
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('entry'); ?>
        </div>
        <div class="col-12 col-md-2">
            <?php echo $this->form->renderField('member_state'); ?>
        </div>
    </div>

    <div>
        <?php echo HtmlHelper::_('uitab.startTabSet', 'myTab', array('active' => 'personal')); ?>
        
            <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'personal', empty($this->item->id) ? Text::_('COM_TKDCLUB_MEMBER_NEW_TAB', true) : Text::_('COM_TKDCLUB_MEMBER_EDIT', true)); ?>
            <div class="row">
                <div class="col-lg-3">
                    <fieldset id="fieldset-personal_data" class="options-form">
                        <legend><?php echo Text::_('COM_TKDCLUB_MEMBER_DATA'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('personal_data'); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-3">
                    <fieldset id="fieldset-taekwondo_data" class="options-form">
                        <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_TAEKWONDO_DATA'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('taekwondo_data'); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-3">
                    <fieldset id="fieldset-club_data" class="options-form">
                        <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_CLUB_DATA'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('club_data'); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-3">
                    <fieldset id="fieldset-club_data" class="options-form">
                        <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_MORE_DATA'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('more_data'); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
            <?php echo HtmlHelper::_('uitab.endTab'); ?>

            <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'attachment', Text::_('COM_TKDCLUB_MEMBER_ATTACHMENTS', true)); ?>
            <div class="row">
                <div class="col-lg-5">
                    <fieldset id="fieldset-club_data" class="options-form">
                        <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_MEMBER_FILE_APPEND'); ?></legend>
                        <?php if (!$this->item->id) : ?>
                            <?php echo Text::_('COM_TKDCLUB_MEMBER_SAVE_FIRST') ?>
                        <?php else: ?>
                        <div>
                            <?php echo $this->form->renderFieldset('attachments'); ?>
                        </div>
                        <?php endif; ?>
                    </fieldset>
                </div>
                <div class="col-lg-7">
                    <div class="row">
                        <?php if (!$this->item->id) : ?>
                            <div></div>
                        <?php else : ?>
                        <fieldset id="fieldset-club_data" class="options-form">
                            <legend class="card-title"><?php echo Text::_('COM_TKDCLUB_MEMBER_FILES_APPENDED'); ?></legend>
                            
                            <?php if (!$this->item->attachments) :?>
                                <?php echo Text::_('COM_TKDCLUB_MEMBER_NO_FILES'); ?>
                            <?php else : ?>
                            <?php $attachments = (array) json_decode($this->item->attachments); ?>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th><?php echo Text::_('COM_TKDCLUB_MEMBER_FILENAME')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php foreach ($attachments as $file_path => $file_name) : ?>
                                        <?php $question = Text::_('COM_TKDCLUB_MEMBER_FILE_DELETE_QUESTION') . $file_name; ?>
                                        <tr>
                                            <td class="w-1">
                                                <?php $deleteLink = Route::_("index.php?option=com_tkdclub&view=member&layout=edit&task=member.deletefile&file_path=" . $file_path . '&file_name=' . $file_name . '&id=' . $this->item->id .  '&' . Session::getFormToken() . '=1') ?>
                                                <a class="btn btn-danger btn-sm" onclick='return window.confirm("<?php echo $question ?>")' 
                                                title="<?php echo Text::_('COM_TKDCLUB_MEMBER_FILE_DELETE'); ?>" 
                                                href="<?php echo $deleteLink; ?>">
                                                    <span class="icon-unpublish"></span>
                                                </a>
                                            </td>
                                            <td class="w-1">
                                                <?php $downloadLink = Route::_("index.php?option=com_tkdclub&task=member.downloadfile&file_path=" . $file_path . '&file_name=' . $file_name . '&id=' . $this->item->id . '&' . Session::getFormToken() . '=1'); ?>
                                                <a class="btn btn-success btn-sm" target="blank"
                                                title="<?php echo Text::_('COM_TKDCLUB_MEMBER_FILE_DOWNLOAD');?>"
                                                href="<?php echo $downloadLink ?>">
                                                <span class="icon-download"></span>
                                                </a>
                                            </td>
                                            <td><?php echo $file_name; ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </fieldset>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            <?php echo HtmlHelper::_('uitab.endTab'); ?>

            <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'wins', Text::_('COM_TKDCLUB_MEMBER_MEDALS', true)); ?>
            <?php if (empty($this->medals)) : ?>
                <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                    <?php echo Text::_('COM_TKDCLUB_MEMBER_NO_MEDALS'); ?>
                </div>
            <?php else : ?>
                <div class="span4">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_DATE'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_MEMBER_MEDAL_CHAMPIONSSHIP'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_MEMBER_MEDAL_CLASS'); ?></th>
                                <th class="center " width="5%"><?php echo Text::_('COM_TKDCLUB_MEMBER_MEDAL_PLACING'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->medals as $medal) : ?>
                                <tr>

                                    <td>
                                        <?php echo HtmlHelper::_('date', $medal->date, Text::_('DATE_FORMAT_LC4')); ?>
                                    </td>
                                    <td width=""><?php echo $medal->championship; ?></td>
                                    <td width=""><?php echo $medal->class; ?></td>
                                    <td class="center" width=""><?php echo $medal->placing; ?></td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <?php echo HtmlHelper::_('uitab.endTab'); ?>

            <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'trainings_data', Text::_('COM_TKDCLUB_SIDEBAR_TRAININGS', true)); ?>
            <?php if (!$this->trainings['all']) : ?>
                <div class="alert alert-info">
                    <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                    <?php echo Text::_('COM_TKDCLUB_MEMBER_NO_TRAINING_DATA'); ?>
				</div>
            <?php else : ?>
                <div>
                    <p><?php echo Text::_('COM_TKDCLUB_MEMBER_TRAININGS_ALL') . $this->trainings['all'] ?></p>
                    <p><?php echo Text::_('COM_TKDCLUB_MEMBER_TRAININGS_S_LASTPROMOTION') . $this->trainings['sLastPromotion'] ?></p>
                </div>
            <?php endif; ?>

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