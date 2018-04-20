<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'participant.cancel' || document.formvalidator.isValid(document.getElementById('participant-form'))) 
                {
			Joomla.submitform(task, document.getElementById('participant-form'));
		}
	};
");

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="participant-form"
      class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <fieldset>
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'participant')); ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'participant', empty($this->item->id) ? JText::_('COM_TKDCLUB_PARTICIPANT_NEW_TAB', true) : JText::_('COM_TKDCLUB_PARTICIPANT_EDIT_TAB', true)); ?>
                        <?php
                            foreach($this->form->getFieldset('participant_data') as $field)
                            {
                                // If the field is not hidden, render it
                                if (!$field->hidden)
                                {
                                    echo $this->form->renderField($field->fieldname);
                                }
                            };
                        ?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php echo JHtml::_('bootstrap.endTabset'); ?>
            </fieldset>
        </div>
    </div>
    <div>
        <input type="hidden" name="task" value="" />
        <?php   echo JHtml::_('form.token'); ?>
    </div>   
</form>