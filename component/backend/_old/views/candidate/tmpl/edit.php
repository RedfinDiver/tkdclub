<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('script', 'administrator/components/com_tkdclub/assets/js/getcandidatedata.js');
HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JLoader::register('Tkdclubmodelexamparts', JPATH_COMPONENT_ADMINISTRATOR . '/models/examparts.php');
?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'candidate.cancel' || document.formvalidator.isValid(document.id('candidate-form'))) {
            Joomla.submitform(task, document.getElementById('candidate-form'));
        }
    }
</script>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="candidate-form" class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <fieldset>
                <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'exampart_data')); ?>
                <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'candidate_data', empty($this->item->id) ? Text::_('COM_TKDCLUB_CANDIDATE_ADD_TAB', true) : Text::sprintf('COM_TKDCLUB_CANDIDATE_EDIT_TAB', $this->item->id, true)); ?>
                <?php echo $this->form->renderField('test_state'); ?>
                <?php echo $this->form->renderField('id_promotion'); ?>
                <?php echo $this->form->renderField('id_candidate'); ?>
                <?php echo $this->form->renderField('lastpromotion'); ?>
                <?php echo $this->form->renderField('grade'); ?>
                <?php echo $this->form->renderField('grade_achieve'); ?>
                <?php echo $this->form->renderField('notes'); ?>

                <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

                <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                <?php if (empty($this->item->id)) : ?>
                    <div class="alert alert-no-items">
                        <?php echo Text::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                    </div>
                <?php else : ?>
                    <div>
                        <?php foreach ($this->form->getFieldset('item_data') as $field) : ?>
                            <?php echo $field->renderField(); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php echo HTMLHelper::_('bootstrap.endTab'); ?>
                <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
            </fieldset>
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <div id="waittext" class="hidden"><?php echo Text::_('COM_TKDCLUB_WAIT_FOR_AJAX_RESPONSE'); ?></div>
        <div id="errortext" class="hidden"><?php echo Text::_('COM_TKDCLUB_ERROR_CHECK_MESSAGES'); ?></div>
</form>