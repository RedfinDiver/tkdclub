<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'member.cancel' || document.formvalidator.isValid(document.getElementById('member-form'))) 
                {
			Joomla.submitform(task, document.getElementById('member-form'));
		}
	};
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&member_id=' . (int) $this->item->member_id); ?>"
    method="post"
    name="adminForm"
    id="member-form"
    class="form-validate"
    enctype="multipart/form-data">

    <!-- top fields which are required -->    
    <div class="form-inline form-inline-header">
        <?php if (empty($this->item->member_id)) : ?>
            <h4 class="text-error"><?php echo JText::_('COM_TKDCLUB_MEMBER_REQUIRED_DATA');  ?></h4>
        <?php endif ?>
            <?php echo $this->form->renderField('lastname'); ?>
            <?php echo $this->form->renderField('firstname'); ?>
            <?php echo $this->form->renderField('sex'); ?>
            <?php echo $this->form->renderField('entry'); ?>
    </div>

    <!-- start of tabs -->
    <div class="form-horizontal">
	    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'personal')); ?>
                         
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'personal', empty($this->item->member_id) ? JText::_('COM_TKDCLUB_MEMBER_NEW_TAB', true) : JText::_('COM_TKDCLUB_MEMBER_EDIT', true)); ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="span4">
                            <h4><?php echo JText::_('COM_TKDCLUB_MEMBER_DATA');?></h4>

                            <?php foreach ($this->form->getFieldset('personal_data') as $field) : ?>
                                <?php echo $field->renderField(); ?>
                            <?php endforeach; ?> 
                        </div>
                        <div class="span4">
                            <h4><?php echo JText::_('COM_TKDCLUB_TAEKWONDO_DATA');?></h4>

                            <?php foreach ($this->form->getFieldset('taekwondo_data') as $field) : ?>
                                <?php echo $field->renderField(); ?>
                            <?php endforeach; ?>
                            <div>
                            <h4><?php echo JText::_('COM_TKDCLUB_MEMBER_PICTURE');?></h4>
                                <?php if (!$this->memberpicture) : ;?>
                                    <?php echo $this->form->renderField('picture'); ?>
                                        <div>
                                            <div class="control-label"></div>
                                            <div class="controls">
                                                <button class="btn btn-large btn-success" onclick="Joomla.submitbutton('member.uploadpicture')">
                                                    <span class="icon-upload icon-white"></span>
                                                    <?php echo JText::_('COM_TKDCLUB_MEMBER_PICTURE_UPLOAD'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div>
                                            <?php $picture_link =  'index.php?option=com_tkdclub&task=member.downloadfile&picture=1&filename='
                                                                   . $this->memberpicture[0] . '&member_id=' . $this->item->member_id . '&' . JSession::getFormToken() .'=1';?>
                                            <img src="<?php echo $picture_link ?>" width="150" height="225"/>
                                        </div>
                                        <div class="btn-group" style="margin-top: 20px">
                                            <a  class="btn hasTooltip"
                                                <?php $question_mb = JText::_('COM_TKDCLUB_MEMBER_PICTURE_DELETE_QUESTION');?>
                                                onclick='return window.confirm("<?php echo $question_mb ?>");'
                                                data-original-title="<?php echo JText::_('COM_TKDCLUB_MEMBER_PICTURE_DELETE'); ?>"
                                                href="index.php?option=com_tkdclub&view=member&layout=edit&task=member.deletepicture&filename=<?php echo $this->memberpicture[0] . '&member_id=' . $this->item->member_id . '&' . JSession::getFormToken() .'=1'; ?>"
                                                >
                                                <span class="icon-unpublish"></span>
                                                <?php echo JText::_('COM_TKDCLUB_DELETE'); ?>
                                            </a>

                                            <a class="btn hasTooltip" data-original-title="<?php echo JText::_('COM_TKDCLUB_MEMBER_PICTURE_DOWNLOAD'); ?>"
                                                href="<?php echo $picture_link ?>"
                                                target="_blank">
                                                <span class="icon-download"></span>
                                                <?php echo JText::_('COM_TKDCLUB_DOWNLOAD'); ?>
                                            </a>
                                        </div>
                                        
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="span4">
                            <h4><?php echo JText::_('COM_TKDCLUB_CLUB_DATA');?></h4>

                            <?php foreach ($this->form->getFieldset('club_data') as $field) : ?>
                                <?php echo $field->renderField(); ?>
                            <?php endforeach; ?>
                              
                        </div>
                    </div>
                </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>    
            <!-- TODO: Just reload the attachment and picture area with javascript. No ugly reloading necessary! -->                        
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachment', JText::_('COM_TKDCLUB_MEMBER_ATTACHMENTS', true)); ?>                         
                <div class="row-fluid">
                    <div class="span12">
                        <div class="span4">
                            <?php if (!$this->item->member_id) : ?>
                                <div>   
                                    <?php echo JText::_('COM_TKDCLUB_MEMBER_SAVE_FIRST') ?>
                                </div>
                            <?php else : ?>
                                <?php echo $this->form->renderField('file'); ?>
                                <div>
                                    <div class="control-label"></div>
                                    <div class="controls">
                                        <button class="btn btn-large btn-success" onclick="Joomla.submitbutton('member.uploadfile')">
                                            <span class="icon-upload icon-white"></span>
                                            <?php echo JText::_('COM_TKDCLUB_MEMBER_FILE_UPLOAD'); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?> 
                            <div class="attachments">  
                                <?php if (empty($this->attachments)) : ?> 
                                    <hr>
                                    <div class="alert alert-no-items">
                                        <?php echo JText::_('COM_TKDCLUB_MEMBER_NO_FILES'); ?>
                                    </div>
                                <?php else : ?>
                                    <hr>
                                    <?php foreach ($this->attachments as $filename) :?>                                                
                                    <div>
                                        <div class="btn-group">
                                            <a  class="btn btn-small hasTooltip"
                                                <?php $question = JText::_('COM_TKDCLUB_MEMBER_FILE_DELETE_QUESTION') . $filename; ?>
                                                onclick='return window.confirm("<?php echo $question ?>");'
                                                data-original-title="<?php echo JText::_('COM_TKDCLUB_MEMBER_FILE_DELETE'); ?>"
                                                href="index.php?option=com_tkdclub&view=member&layout=edit&task=member.deletefile&filename=<?php echo $filename . '&member_id=' . $this->item->member_id . '&' . JSession::getFormToken() .'=1'; ?>"
                                                >
                                                <span class="icon-unpublish"></span>
                                            </a>

                                            <a class="btn btn-small hasTooltip" data-original-title="<?php echo JText::_('COM_TKDCLUB_MEMBER_FILE_DOWNLOAD'); ?>"
                                                href="index.php?option=com_tkdclub&task=member.downloadfile&filename=<?php echo $filename . '&member_id=' . $this->item->member_id . '&' . JSession::getFormToken() .'=1'; ?>"
                                                target="_blank">
                                                <span class="icon-download"></span> 
                                            </a>
                                        </div>
                                            <a target="_blank" href="index.php?option=com_tkdclub&task=member.downloadfile&filename=<?php echo $filename . '&member_id=' . $this->item->member_id . '&' . JSession::getFormToken() .'=1'; ?>">
                                            <?php echo $filename; ?></a>
                                    </div>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="span4">
                            <!-- this was the original place for the member picture -->
                        </div>
                    </div>
                </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'wins', JText::_('COM_TKDCLUB_MEMBER_MEDALS', true)); ?>
                <?php if (empty($this->medals)) : ?>
                    <div class="alert alert-no-items">
                        <?php echo JText::_('COM_TKDCLUB_MEMBER_NO_MEDALS'); ?>
                    </div>
                <?php else : ?>                        
                    <div class="span4">
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th width=""><?php echo JText::_('COM_TKDCLUB_DATE'); ?></th>
                                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_MEDAL_CHAMPIONSSHIP'); ?></th>
                                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_MEDAL_CLASS'); ?></th>
                                    <th class="center "width="5%"><?php echo JText::_('COM_TKDCLUB_MEMBER_MEDAL_PLACING'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->medals as $medal) : ?>
                                    <tr>

                                        <td>
                                            <?php echo JHtml::_('date', $medal->date, JText::_('DATE_FORMAT_LC4')); ?>
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
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                <?php if (empty($this->item->member_id)) : ?>
                    <div class="alert alert-no-items">
                        <?php echo JText::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                    </div>
                <?php else : ?>                          
                    <div> 
                        <?php foreach ($this->form->getFieldset('item_data') as $field) : ?>
                                <?php echo $field->renderField(); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>          
            <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>            
    </div>
    <!-- end of tabs -->
                      
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>

</form>