<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

JFactory::getDocument()->addScriptDeclaration("
Joomla.submitbutton = function(task)
{
    if (task == 'training.cancel' || document.formvalidator.isValid(document.getElementById('training-form'))) 
            {
        Joomla.submitform(task, document.getElementById('training-form'));
    }
};
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&training_id=' .(int) $this->item->training_id); ?>"
      method="post"
      name="adminForm"
      id="training-form"
      class="form-validate">

    <!-- start of tabs -->
    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'trainingdata')); ?>
                    
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'trainingdata', empty($this->item->training_id) ? JText::_('COM_TKDCLUB_TRAINING_NEW_TAB', true) : JText::sprintf('COM_TKDCLUB_TRAINING_EDIT', $this->item->training_id, true)); ?>
                <div class="row-fluid">             
                    <div class="span6">
                                <?php echo $this->form->renderField('payment_state'); ?>
                                <?php echo $this->form->renderField('date'); ?>
                                <?php echo $this->form->renderField('trainer'); ?>
                                <?php echo $this->form->renderField('km_trainer'); ?>
                                <?php echo $this->form->renderField('type'); ?>
                                <?php echo $this->form->renderField('participants'); ?>
                                <?php echo $this->form->renderField('km'); ?>
                                <?php echo $this->form->renderField('notes'); ?>
                    </div>
                    <div class="span6">
                                <?php echo $this->form->renderField('assist1'); ?>
                                <?php echo $this->form->renderField('km_assist1'); ?>
                                <?php echo $this->form->renderField('spacer1'); ?>
                                <?php echo $this->form->renderField('assist2'); ?>
                                    <?php echo $this->form->renderField('km_assist2'); ?>
                                <?php echo $this->form->renderField('spacer2'); ?>
                                <?php echo $this->form->renderField('assist3'); ?>
                                <?php echo $this->form->renderField('km_assist3'); ?>
                    </div>
                </div>        
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                <?php if (empty($this->item->training_id)) : ?>
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

    <input type="hidden" name="task" value="" />
    <?php   echo JHtml::_('form.token'); ?>

</form>