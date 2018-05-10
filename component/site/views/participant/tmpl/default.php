<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'components/com_tkdclub/assets/js/field.js');

$params = JComponentHelper::getParams('com_tkdclub');
$item_params = JFactory::getApplication()->getParams()->toObject();

$places_free = $this->event_data['max'] - $this->event_data['subscribed'];
$subscribed = $this->event_data['subscribed'];
$deadline = new DateTime($this->event_data['deadline']);
$now = new DateTime();
$stop_sub = $now->diff($deadline)->invert;

if ($params->get('captcha') != '0')
{
    JPluginHelper::importPlugin('captcha');
    $dispatcher = JDispatcher::getInstance();
    $dispatcher->trigger('onInit',''); 
}

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

<div>
    <h2><?php echo JText::_('COM_TKDCLUB_SUBSCRIBE_SUCCESS') .'"'. $this->event_data['title'] .'"'. ' am ' . JHtml::_('date', $this->event_data['date'], JText::_('DATE_FORMAT_LC4')); ?></h2>
    <!-- blocking form if it is set in parameters -->   
    <?php if ($item_params->block_form_places && $places_free <= 0) : ?>
        <h4><?php echo JText::_('COM_TKDCLUB_SUBSCRIPTION_UNPOSSIBLE_PLACES'); ?></h4>
    <?php elseif ($item_params->block_form_deadline && $stop_sub == 1) : ?>
        <h4><?php echo JText::_('COM_TKDCLUB_SUBSCRIPTION_UNPOSSIBLE_DEADLINE'); ?></h4>
    <?php else : ?>
        <div>
            <?php if ($item_params->show_places) : ?>
                <!-- Listing of subscribed participants -->
                <?php if ($places_free > 0) : ?>
                    <p>    
                        <strong><?php echo JText::_('COM_TKDCLUB_EVENT_PLACES_FREE'); ?><?php echo $places_free; ?></strong>
                    <br/>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_SUBSCRIBED'); ?><?php echo $subscribed ?>
                    </p>
                <?php endif; ?>
            
                <?php if ($places_free <= 0) :?>
                    <p>    
                        <strong><?php echo JText::_('COM_TKDCLUB_EVENT_SUBSCRIBE_WAITLIST'); ?></strong>
                    <br/>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_WAITLIST'); ?><?php echo ($this->event_data['max_parts'] - $subscribed) * -1; ?>
                    </p>
            <?php endif;?>
        </div>   
    <?php endif;?>
</div>
<div>
    <p> <?php echo JText::_('COM_TKDCLUB_PLEASE_FILL_IN'); ?></p>
</div>
<hr>
<form action="<?php echo JRoute::_('index.php?option=com_tkdclub') ?>"
      method="post"
      name="adminForm"
      id="participant-form"
      class="form-validate">

<div class="row-fluid">
	<div class="span12 form-horizontal">
        <fieldset>
            <!-- check for multi-participants -->
            <?php if ($item_params->allow_multi){ echo $this->form->renderField('group');} ?>
            <!-- render fields for all participants -->
            <?php
                foreach($this->form->getFieldset('participant_data_all') as $field)
                {
                    // If the field is not hidden, render it
                    if (!$field->hidden)
                    {
                        echo $this->form->renderField($field->fieldname);
                    }

                };
            ?>
            <!-- render fields for multiple or single participants -->
            <div id="single_multiple">
                <?php
                    foreach($this->form->getFieldset('participant_data_single') as $field)
                    {
                        // If the field is not hidden, render it
                        if (!$field->hidden)
                        {
                            echo $this->form->renderField($field->fieldname);
                        }

                    };
                ?>
            </div>
            <!-- user fields -->
            <?php
                foreach($this->form->getFieldset('participant_data_userfields') as $field)
                {
                    // If the field is not hidden, render it
                    if (!$field->hidden)
                    {
                        echo $this->form->renderField($field->fieldname);
                    }

                };
            ?>
            <!-- render captcha field -->
            <?php echo $this->form->renderField('captcha'); ?>
        </fieldset>
        <div class="btn-toolbar">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('participant.subscribe')">
                    <span class="icon-ok"></span> <?php echo JText::_('COM_TKDCLUB_SUBSCRIBE') ?>
                </button>
            </div>
        </div>
        </div>
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div> 
    </div>
</div>  
</form>

<?php endif; ?>