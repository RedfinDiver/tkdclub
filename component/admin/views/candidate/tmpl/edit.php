<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/getcandidatedata.js');
JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JLoader::register('Tkdclubmodelexamparts', JPATH_COMPONENT_ADMINISTRATOR .'/models/examparts.php');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'candidate.cancel' || document.formvalidator.isValid(document.id('candidate-form')))
		{
			Joomla.submitform(task, document.getElementById('candidate-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&id=' .(int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="candidate-form"
      class="form-validate">
    
    <div class="row-fluid"> 
		<div class="span12 form-horizontal">
        <fieldset>
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'exampart_data')); ?>
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'candidate_data', empty($this->item->id) ? JText::_('COM_TKDCLUB_CANDIDATE_ADD_TAB', true) : JText::sprintf('COM_TKDCLUB_CANDIDATE_EDIT_TAB', $this->item->id, true)); ?>
                    <?php echo $this->form->renderField('test_state'); ?>
                    <?php echo $this->form->renderField('id_promotion'); ?>
                    <?php echo $this->form->renderField('id_candidate'); ?>
                    <?php echo $this->form->renderField('lastpromotion'); ?>
                    <?php echo $this->form->renderField('grade'); ?>
                    <?php echo $this->form->renderField('grade_achieve'); ?>
                    <?php echo $this->form->renderField('notes'); ?>
                
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                    <?php if (empty($this->item->id)) : ?>
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
        </fieldset>
    </div>  
    
    <input type="hidden" name="task" value="" />
    <?php   echo JHtml::_('form.token'); ?>

    <div id="waittext" class="hidden"><?php echo JText::_('COM_TKDCLUB_WAIT_FOR_AJAX_RESPONSE'); ?></div>
    <div id="errortext" class="hidden"><?php echo JText::_('COM_TKDCLUB_ERROR_CHECK_MESSAGES'); ?></div>
</form>