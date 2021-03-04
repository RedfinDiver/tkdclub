<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Component\ComponentHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var \Redfindiver\Component\Tkdclub\Administrator\View\Members\HtmlView $this */
HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry();
$wa->useStyle('com_tkdclub.tkdclub-css');


/**
 * initilise some variables
 */
$user      = Factory::getUser();
$userId    = $user->get('id');
$currency = ComponentHelper::getParams('com_tkdclub')->get('currency', '€');
$salaryparams = $this->salaryparams;
$togglestats = $this->togglestats;
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$listLimit = $this->state->get('list.limit');
$columns = 10;

$filter_trainer = $this->state->get('filter.trainer');

$filter_type   = $this->state->get('filter.type');
$filter_year   = $this->state->get('filter.year');
$filter_search = $this->state->get('filter.search');
$filter_payment_state  = $this->state->get('filter.payment_state');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=trainings'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                 <?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
                    <div class="m-2">
                        <?php echo Text::sprintf('COM_TKDCLUB_ENTRIES', $this->total, $this->allrows); ?>
                    </div>
                    <?php if ($this->togglestats) :  ?>
                        <?php include_once(JPATH_COMPONENT . '/includes/trainingsstats.php'); ?>
                    <?php endif; ?>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HtmlHelper::_('grid.checkall'); ?>
                                </td>
                                <th  class="w-1 text-center"><?php echo Text::_('COM_TKDCLUB_TRAINING_PAID'); ?></th>
                                <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_TRAINER'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT1'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT2'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_ASSISTENT3'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_TYPE'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_TRAINING_NOTES'); ?></th>
                                <th width="1%"><?php echo Text::_('COM_TKDCLUB_TRAINING_ID'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo $columns; ?>"></td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.training.' . $item->training_id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.training.' . $item->training_id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.training.' . $item->training_id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->training_id); ?>
                                    <td class="tbody-icon">
                                        <?php
                                                $state = TkdclubHelper::getpaystate(
                                                    $item->trainer_paid,
                                                    $item->assist1,
                                                    $item->assist2,
                                                    $item->assist3,
                                                    $item->assist1_paid,
                                                    $item->assist2_paid,
                                                    $item->assist3_paid
                                                );

                                                $classes = array(0 => 'icon-remove', 1 => 'icon-publish', 2 => 'icon-plus-2 tkdclub-icon-orange');
                                                $tooltip = array(
                                                    0 => Text::_('COM_TKDCLUB_TRAINING_NOT_PAID'),
                                                    1 => Text::_('COM_TKDCLUB_TRAINING_PAID'),
                                                    2 => Text::_('COM_TKDCLUB_TRAINING_PARTLY_PAID')
                                                );
                                                ?>
                                        <span class="<?php echo $classes[$state]; ?> hasTooltip" title="" data-original-title="<?php echo $tooltip[$state]; ?>"></span>

                                    </td>

                                    <td width="" class="title">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'trainings.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php
                                                $mylink = Route::_("index.php?option=com_tkdclub&task=training.edit&training_id=" . $item->training_id);
                                                echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, JText::_('DATE_FORMAT_LC4')) . '</a>';
                                                ?>
                                    </td>
                                    <td><?php echo $this->escape($item->trainer_firstname) . ' ' . $this->escape($item->trainer_lastname); ?></td>
                                    <td><?php echo $this->escape($item->assist1_firstname) . ' ' . $this->escape($item->assist1_lastname); ?></td>
                                    <td><?php echo $this->escape($item->assist2_firstname) . ' ' . $this->escape($item->assist2_lastname); ?></td>
                                    <td><?php echo $this->escape($item->assist3_firstname) . ' ' . $this->escape($item->assist3_lastname); ?></td>
                                    <td><?php echo $this->escape($item->type); ?></td>
                                    <td><?php echo $this->escape($item->participants); ?></td>
                                    <td><?php echo $this->escape($item->notes); ?></td>
                                    <td><?php echo (int) $item->training_id; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo $this->pagination->getListFooter(); ?>

                <div>
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <?php echo HtmlHelper::_('form.token'); ?>
                </div>
            </div>
        </div>    
    </div>
</form>