<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'medal.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			Joomla.submitform(task);
		}
	}
");
?>

<div class="tkdclub addmedal">
    <form action="<?php echo Route::_('index.php?option=com_tkdclub') ?>" method="post" name="medal-form" id="adminForm" class="form-validate form-vertical">
        <fieldset> 
            <?php echo HTMLHelper::_('bootstrap.startTabSet', 'com-tkdclub-medal', array('active' => 'medal-data')); ?>

            <?php echo HTMLHelper::_('bootstrap.addTab', 'com-tkdclub-medal', 'medal-data', JText::_('COM_TKDCLUB_MEDAL_NEW_TAB')); ?>
                <?php echo $this->form->renderField('date'); ?>
                <?php echo $this->form->renderField('championship'); ?>
                <?php echo $this->form->renderField('type'); ?>
                <?php echo $this->form->renderField('class'); ?>
                <?php echo $this->form->renderField('placing'); ?>
                <?php echo $this->form->renderField('winner_ids'); ?>
                <?php echo $this->form->renderField('notes'); ?>
                <?php echo $this->form->renderField('captcha'); ?>

            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <!-- TODO use form also for frontend editing -->
            <?php echo HTMLHelper::_('bootstrap.addTab', 'com-tkdclub-medal', 'item-data', JText::_('COM_TKDCLUB_ITEM_DATA')); ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                </div>

            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
            <input type="hidden" name="task" value="" />
			<?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
        <div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('medal.save')">
					<span class="icon-ok"></span><?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('medal.cancel')">
					<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>
    </form>
</div>