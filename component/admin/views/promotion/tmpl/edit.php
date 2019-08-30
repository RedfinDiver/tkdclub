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

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');

HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

Factory::getDocument()->addScriptDeclaration("
Joomla.submitbutton = function(task)
{
    if (task == 'promotion.cancel' || document.formvalidator.isValid(document.getElementById('promotion-form'))) 
            {
        Joomla.submitform(task, document.getElementById('promotion-form'));
    }
};
");

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&promotion_id=' . (int) $this->item->promotion_id); ?>" method="post" name="adminForm" id="promotion-form" class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <fieldset>
                <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'promotion')); ?>
                <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'promotion', empty($this->item->promotion_id) ? Text::_('COM_TKDCLUB_PROMOTION_NEW_TAB', true) : Text::sprintf('COM_TKDCLUB_PROMOTION_EDIT_TAB', $this->item->promotion_id, true)); ?>
                <?php
                foreach ($this->form->getFieldset('promotion_data') as $field) :
                    // If the field is hidden, render nothing
                    if ($field->hidden) :

                    else :
                        ?>

                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input ?></div>
                        </div>

                <?php
                    endif;
                endforeach;
                ?>

                <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

                <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
                <?php if (empty($this->item->promotion_id)) : ?>
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
    </div>

    <div>
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>

</form>