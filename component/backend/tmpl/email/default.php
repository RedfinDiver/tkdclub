<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

//test email active
if ($this->email_test) {
    Factory::getApplication()->enqueueMessage(Text::_('COM_TKDCLUB_EMAIL_TESTMAIL_ACTIVE') . $this->email_test, 'message');
}
?>

<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.adminForm;

        if (pressbutton == 'email.cancel') {
            Joomla.submitform(pressbutton);
            return;
        }

        // do field validation
        if (form.jform_subject.value == "") {
            alert(Joomla.Text._('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_SUBJECT'));
        } else if (form.jform_message.value == "") {
            alert(Joomla.Text._('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_MESSAGE'));
        } else {
            Joomla.submitform(pressbutton);
        }
    }
</script>

<?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
        <?php endif; ?>

        <form action="<?php echo Route::_('index.php?option=com_tkdclub&view=email'); ?>" name="adminForm" method="post" id="adminForm" class="form-validate" enctype="multipart/form-data">

            <div class="row-fluid">
                <div class="span8">
                    <fieldset class="adminform">
                        <?php foreach ($this->form->getFieldset('email') as $field) : ?>
                            <?php echo $field->renderField(); ?>
                        <?php endforeach; ?>
                    </fieldset>
                    <input type="hidden" name="task" value="" />
                    <?php echo HTMLHelper::_('form.token'); ?>

                </div>
                <?php if (!$this->email_test) : ?>
                    <div class="span4">
                        <h4><?php echo Text::_('COM_TKDCLUB_EMAIL_RECIPIENTS') ?></h4>
                        <fieldset class="form-vertical">
                            <div class="control-group checkbox">
                                <div class="controls"><?php echo $this->form->getInput('active'); ?> <?php echo $this->form->getLabel('active'); ?></div>
                            </div>
                            <div class="control-group checkbox">
                                <div class="control-label"><?php echo $this->form->getInput('supporter'); ?> <?php echo $this->form->getLabel('supporter'); ?></div>
                            </div>
                            <div class="control-group checkbox">
                                <div class="control-label"><?php echo $this->form->getInput('inactive'); ?> <?php echo $this->form->getLabel('inactive'); ?></div>
                            </div>
                            <div class="control-group checkbox">
                                <div class="control-label"><?php echo $this->form->getInput('newsletter'); ?> <?php echo $this->form->getLabel('newsletter'); ?></div>
                            </div>
                            <div class="control-label"><?php echo $this->form->getLabel('event'); ?> </div>
                            <div class="controls"><?php echo $this->form->getInput('event'); ?> </div>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </form>