<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

JFactory::getDocument()->addScriptDeclaration("
Joomla.submitbutton = function(task)
{
    if (task == 'promotion.cancel' || document.formvalidator.isValid(document.getElementById('promotion-form'))) 
            {
        Joomla.submitform(task, document.getElementById('promotion-form'));
    }
};
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&promotion_id=' .(int) $this->item->promotion_id); ?>"
      method="post"
      name="adminForm"
      id="promotion-form"
      class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <fieldset>
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'promotion')); ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'promotion', empty($this->item->promotion_id) ? JText::_('COM_TKDCLUB_PROMOTION_NEW_TAB', true) : JText::sprintf('COM_TKDCLUB_PROMOTION_EDIT_TAB', $this->item->promotion_id, true)); ?>
                    <?php
                        foreach($this->form->getFieldset('promotion_data') as $field):
                            // If the field is hidden, render nothing
                            if ($field->hidden):

                            else:
                            ?>

                            <div class="control-group">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input ?></div>
                            </div>

                            <?php
                            endif;
                        endforeach;
                    ?>

                    <?php echo JHtml::_('bootstrap.endTab'); ?>

                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                        <?php if (empty($this->item->promotion_id)) : ?>
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
    </div>   

    <div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>  
    </div>

</form>

