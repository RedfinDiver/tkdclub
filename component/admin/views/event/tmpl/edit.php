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

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form')))
		{
			
			Joomla.submitform(task, document.getElementById('event-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&event_id=' .(int) $this->item->event_id); ?>"
      method="post"
      name="adminForm"
      id="event-form"
      class="form-validate">

<div class="row-fluid"> 
		<div class="span12 form-horizontal">
        <fieldset>
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'event')); ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'event', empty($this->item->event_id) ? JText::_('COM_TKDCLUB_EVENT_NEW_TAB', true) : JText::sprintf('COM_TKDCLUB_EVENT_EDIT_TAB', $this->item->event_id, true)); ?>
                    <?php
                    foreach($this->form->getFieldset('eventdata') as $field)
                    {
                        // If the field is not hidden, render it
                        if (!$field->hidden)
                        {
                            echo $this->form->renderField($field->fieldname);
                        }

                    };
					?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'item_data', JText::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                    <?php if (empty($this->item->event_id)) : ?>
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
	<div>
        <input type="hidden" name="task" value="" />
        <?php   echo JHtml::_('form.token'); ?>
    </div>     
</form>