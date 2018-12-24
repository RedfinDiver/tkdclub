<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'subscriber.cancel' || document.formvalidator.isValid(document.getElementById('subscriber-form'))) 
                {
			Joomla.submitform(task, document.getElementById('subscriber-form'));
		}
	};
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&id=' .(int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="subscriber-form"
      class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'subscriberdata')); ?>
                
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'participant', empty($this->item->id) ? JText::_('COM_TKDCLUB_SUBSCRIBER_NEW_TAB', true) : JText::_('COM_TKDCLUB_SUBSCRIBER_EDIT_TAB', true)); ?>
                    <?php echo $this->form->renderFieldset('subscriberdata')?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                    <?php if (empty($this->item->id)) : ?>
                        <div class="alert alert-no-items">
                            <?php echo JText::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                        </div>
                    <?php else : ?> 
                        <?php echo $this->form->renderFieldset('item_data')?>
                    <?php endif; ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.endTabset'); ?>
        </div>
    </div>
    <div>
        <input type="hidden" name="task" value="" />
        <?php   echo JHtml::_('form.token'); ?>
    </div>  
</form>