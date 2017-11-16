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
        if (task == 'medal.cancel' || document.formvalidator.isValid(document.getElementById('medal-form'))) 
                {
            Joomla.submitform(task, document.getElementById('medal-form'));
        }
    };
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&id=' .(int) $this->item->medal_id); ?>"
      method="post"
      name="adminForm"
      id="medal-form"
      class="form-validate">

<div class="row-fluid"> 
		<!-- Begin Medals -->
		<div class="form-horizontal">
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'medal')); ?>
                     <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'medal', empty($this->item->medal_id) ? JText::_('COM_TKDCLUB_MEDAL_NEW_TAB', true) : JText::sprintf('COM_TKDCLUB_MEDAL_EDIT', $this->item->medal_id, true)); ?>
                     <?php foreach ($this->form->getFieldset('medal_data') as $field) : ?>
                            <?php echo $field->renderField(); ?>
                    <?php endforeach; ?> 
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                    <?php if (empty($this->item->medal_id)) : ?>
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
          
    <div>
        <input type="hidden" name="task" value="" />
        <?php   echo JHtml::_('form.token'); ?>
    </div>     
</form>