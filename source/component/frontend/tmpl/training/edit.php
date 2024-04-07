<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('com_tkdclub.training-edit')
	->useScript('com_tkdclub.trainer-select')
	->useStyle('com_tkdclub.tkdclub-site');

$this->tab_name = 'com-tkdclub-form';

$menuItem = Factory::getApplication()->getMenu()->getActive();
$params = $menuItem->getParams();
?>

<div class="edit item-page">
	<?php if ($params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo  $params->get('page_heading') ? $params->get('page_heading') : Factory::getApplication()->getMenu()->getActive()->title; ?>
		</h1>
	</div>
	<?php endif; ?>
	<form action="<?php echo Route::_('index.php?option=com_tkdclub') ?>" method="post" name="training-form" id="adminForm" class="form-validate">
		<fieldset>
			<?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, array('active' => 'editor')); ?>
				<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'training', Text::_('COM_TKDCLUB_TRAINING_NEW_TAB')); ?>
				<div class="row form-vertical">
					<div class="col-12 col-md-4">
						<?php echo $this->form->renderField('date'); ?>
					</div>
					<div class="col-12 col-md-4">
						<?php echo $this->form->renderField('type'); ?>
					</div>
					<div class="col-12 col-md-4">
						<?php echo $this->form->renderField('participants'); ?>
					</div>
				</div>
				<div class="row">
					<fieldset id="fieldset-trainer_data" class="options-form">
						<legend><?php echo Text::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'); ?></legend>
						<div>
							<?php echo $this->form->renderField('participant_ids'); ?>
						</div>
					</fieldset>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<fieldset id="fieldset-trainer_data" class="options-form">
							<legend><?php echo Text::_('COM_TKDCLUB_TRAINING_TRAINER'); ?></legend>
							<div>
								<?php echo $this->form->renderFieldset('class_lead'); ?>
							</div>
						</fieldset>
						<fieldset id="fieldset-trainer_data" class="options-form">
							<legend><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></legend>
							<div>
								<?php echo $this->form->renderField('notes'); ?>
							</div>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset id="fieldset-trainer_data" class="options-form">
							<legend><?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT'); ?></legend>
							<div>
								<?php echo $this->form->renderFieldset('assistents'); ?>
							</div>
						</fieldset>
					</div>
				</div>

				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

			<input type="hidden" name="task" value="">
			<input type="hidden" name="return" value="<?php echo $menuItem->id ?>">
			<?php echo HTMLHelper::_('form.token'); ?>
		</fieldset>
		<div class="mb-2">
			<button type="button" class="btn btn-primary" data-submit-task="training.save">
				<span class="icon-check" aria-hidden="true"></span>
				<?php echo Text::_('JSAVE'); ?>
			</button>
			<button type="button" class="btn btn-danger" data-submit-task="training.cancel">
				<span class="icon-times" aria-hidden="true"></span>
				<?php echo Text::_('JCANCEL'); ?>
			</button>
		</div>
	</form>
</div>