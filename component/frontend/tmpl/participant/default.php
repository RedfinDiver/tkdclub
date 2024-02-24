<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('com_tkdclub.reload_fields')
	->useStyle('com_tkdclub.tkdclub-site');

$params = ComponentHelper::getParams('com_tkdclub');
$menu = Factory::getApplication()->getMenu()->getActive();
$item_params = $menu->getParams();

$places_free = $this->event_data['max'] - $this->event_data['subscribed'];
$subscribed = $this->event_data['subscribed'] ? $subscribed = $this->event_data['subscribed'] : $subscribed = 0;
$deadline = new DateTime($this->event_data['deadline']);
$now = new DateTime();
$stop_sub = $now->diff($deadline)->invert;

if ($params->get('captcha') != '0')
{
    // TODO: Handle Plugin with new joomla/event package
    PluginHelper::importPlugin('captcha');
}

?>

<div class="tkdclub_subscribe">
    <h2>
        <?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCRIBE_TO') .'"'. $this->event_data['title'].'"'
        . Text::_('COM_TKDCLUB_EVENT_ON') . HTMLHelper::_('date', $this->event_data['date'], Text::_('DATE_FORMAT_LC4')); ?>
    </h2>
    <!-- blocking form if it is set in parameters -->   
    <?php if ($item_params->get('block_form_places') && $places_free <= 0) : ?>
        <h4><?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCRIPTION_UNPOSSIBLE_PLACES'); ?></h4>
    <?php elseif ($item_params->get('block_form_deadline') && $stop_sub == 1) : ?>
        <h4><?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCRIPTION_UNPOSSIBLE_DEADLINE'); ?></h4>
    <?php else : ?>
        <div>
            <?php if ($item_params->get('show_places')): ?>
                <!-- Listing of subscribed participants -->
                <?php if ($places_free > 0) : ?>
                    <p>    
                        <strong><?php echo Text::_('COM_TKDCLUB_EVENT_PLACES_FREE'); ?><?php echo $places_free; ?></strong>
                    <br/>
                        <?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS'); ?><?php echo $subscribed ?>
                    </p>
                <?php endif; ?>
            
                <?php if ($places_free <= 0) :?>
                    <p>    
                        <strong><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_SUBSCRIBE_WAITLIST'); ?></strong>
                    <br/>
                        <?php echo Text::_('COM_TKDCLUB_PARTICIPANT_WAITLIST'); ?><?php echo ($this->event_data['max'] - $subscribed) * -1; ?>
                    </p>
                <?php endif;?>
            <?php endif;?>
        </div>   
    <?php endif;?>

    <p><?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCIPTION_PLEASE_FILL'); ?></p>

    <hr>

    <form action="<?php echo Route::_('index.php?option=com_tkdclub') ?>"
        method="post"
        name="adminForm"
        id="participant-form"
        class="form-validate">

        <div class="form-horizontal">
            <fieldset>
                <!-- check for multi-participants -->
                <?php if ($item_params->get('allow_multi')){ echo $this->form->renderField('group');} ?>
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

                <!-- render GDPR field -->
                <?php 
                    $privacy_field = $this->form->getField('privacy_agreed');
                    $privacy_message = $item_params->get('privacy_message') ? $item_params->get('privacy_message') : Text::_('COM_TKDCLUB_PARTICIPANT_MENUITEM_PRIVACY_MESSAGE_DEFAULT')
                ?>
                <div class="control-group">
                    <div class="control-label"><?php echo $privacy_field->label; ?>
                    </div>
                    <label class="controls checkbox"><?php echo $privacy_field->input; ?>
                        <?php echo $privacy_message; ?>
                    </label>
                </div>

                <!-- render store_email field -->
                <?php
                    $store_mail_field = $this->form->getField('store_data');
                    $store_mail_message = $item_params->get('store_email_message') ? $item_params->get('store_email_message') : Text::_('COM_TKDCLUB_PARTICIPANT_MENUITEM_STORE_EMAIL_MESSAGE_DEFAULT');
                ?>
                <div class="control-group">
                    <div class="control-label"><?php echo $store_mail_field->label; ?>
                    </div>
                    <label class="controls checkbox"><?php echo $store_mail_field->input; ?>
                        <?php echo $store_mail_message; ?>
                    </label>
                </div>

            </fieldset>

            <div class="btn-toolbar">
                <div class="btn-group">
                    <button class="btn btn-primary validate" type="submit"><?php echo Text::_('COM_TKDCLUB_SUBSCRIBE'); ?></button>
                </div>
            </div>
                    
            <div>
                <input type="hidden" name="option" value="com_tkdclub">
                <input type="hidden" name="task" value="participant.subscribe">
                <input type="hidden" name="return" value="<?php echo $menu->id ?>">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div> 
        </div>

    </form>
</div>