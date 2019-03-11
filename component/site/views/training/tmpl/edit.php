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
use Joomla\CMS\Language\Text;

JLoader::register('TkdclubHelperTrainer', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/trainer.php');

HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'training.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			Joomla.submitform(task);
		}
	}
");
?>

<div class="tkdclub addtraining">
    <form action="<?php echo Route::_('index.php?option=com_tkdclub') ?>" method="post" name="training-form" id="adminForm" class="form-validate form-horizontal">
        <fieldset> 
            <?php echo HTMLHelper::_('bootstrap.startTabSet', 'com-tkdclub-training', array('active' => 'training-data')); ?>

            <?php echo HTMLHelper::_('bootstrap.addTab', 'com-tkdclub-training', 'training-data', Text::_('COM_TKDCLUB_TRAINING_NEW_TAB')); ?>
				<legend>
					<?php echo Text::_('COM_TKDCLUB_TRAINING_TRAINING_DATA'); ?>
				</legend>
				<?php echo $this->form->renderFieldset('training_data'); ?>
				<legend>
					<?php echo Text::_('COM_TKDCLUB_TRAINING_TRAINER_DATA'); ?>
				</legend>
				<?php echo $this->form->renderFieldset('training_trainer'); ?>
				<legend>
					<?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT_DATA'); ?>
				</legend>
				<?php echo $this->form->renderFieldset('training_assistents'); ?>

            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <!-- TODO use form also for frontend editing -->
            <?php echo HTMLHelper::_('bootstrap.addTab', 'com-tkdclub-training', 'item-data', Text::_('COM_TKDCLUB_ITEM_DATA')); ?>
                <div class="alert alert-no-items">
                    <?php echo Text::_('COM_TKDCLUB_NO_ITEM_DATA'); ?>
                </div>

            <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

            <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
            <input type="hidden" name="task" value="" />
			<?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
        <div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('training.save')">
					<span class="icon-ok"></span><?php echo Text::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('training.cancel')">
					<span class="icon-cancel"></span><?php echo Text::_('JCANCEL') ?>
				</button>
			</div>
		</div>
	</form>
</div>