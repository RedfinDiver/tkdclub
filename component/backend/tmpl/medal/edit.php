<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&medal_id=' . (int) $this->item->medal_id); ?>" method="post" name="adminForm" id="medal-form" class="form-validate">

    <div class="row-fluid">
        <!-- Begin Medals -->
        <div class="form-horizontal">
            <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'medal')); ?>
            <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'medal', empty($this->item->medal_id) ? Text::_('COM_TKDCLUB_MEDAL_NEW_TAB', true) : Text::sprintf('COM_TKDCLUB_MEDAL_EDIT', $this->item->medal_id, true)); ?>
            <?php foreach ($this->form->getFieldset('medal_data') as $field) : ?>
                <?php echo $field->renderField(); ?>
            <?php endforeach; ?>
            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
            <?php if (empty($this->item->medal_id)) : ?>
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

            <div>
                <input type="hidden" name="task" value="" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
</form>