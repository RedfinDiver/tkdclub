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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'subscriber.cancel' || document.formvalidator.isValid(document.getElementById('subscriber-form'))) 
                {
			Joomla.submitform(task, document.getElementById('subscriber-form'));
		}
	};
");

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="subscriber-form" class="form-validate">

    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'subscriberdata')); ?>

            <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'participant', empty($this->item->id) ? Text::_('COM_TKDCLUB_SUBSCRIBER_NEW_TAB', true) : Text::_('COM_TKDCLUB_SUBSCRIBER_EDIT_TAB', true)); ?>
            <?php echo $this->form->renderFieldset('subscriberdata') ?>
            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'item_data', Text::_('COM_TKDCLUB_ITEM_DATA', true)); ?>
            <?php if (empty($this->item->id)) : ?>
                <div class="alert alert-no-items">
                    <?php echo Text::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                </div>
            <?php else : ?>
                <?php echo $this->form->renderFieldset('item_data') ?>
            <?php endif; ?>
            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <?php echo HTMLHelper::_('bootstrap.endTabset'); ?>
        </div>
    </div>
    <div>
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>