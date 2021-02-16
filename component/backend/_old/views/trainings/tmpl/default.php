<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');

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
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <!-- filters and sorting tools -->
            <?php
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
            <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>

                <div class="tkdclub-numbers">
                    <b><?php echo $this->total; ?></b> <?php echo JText::_('COM_TKDCLUB_FROM'); ?>
                    <b><?php echo $this->allrows; ?></b>
                    <?php echo JText::_('COM_TKDCLUB_ENTRIES'); ?>
                </div>

                <div class="clearfix"> </div>
                <?php if ($this->togglestats) :  ?>
                    <div class="alert alert-info">
                        <?php
                                include_once(JPATH_COMPONENT . '/includes/trainingsstats.php');
                                ?>
                    </div>
                <?php endif; ?>
                <!-- Beginn der Tabelle mit den Einträgen-->
                <table class="table table-condensed">
                    <!-- Überschriften der Tabelle-->
                    <thead>
                        <tr>
                            <th width="1%">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th class="center" width="20"><?php echo JText::_('COM_TKDCLUB_TRAINING_PAID'); ?></th>
                            <th width="">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                            </th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_TRAINER'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_TYPE'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'); ?></th>
                            <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_NOTES'); ?></th>
                            <th width="1%"><?php echo JText::_('COM_TKDCLUB_TRAINING_ID'); ?>
                            </th>
                        </tr>
                    </thead>
                    <!--footer -->
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo $columns; ?>"></td>
                        </tr>
                    </tfoot>
                    <!-- table body-->
                    <tbody>
                        <?php foreach ($this->items as $i => $item) :
                                $canEdit    = $user->authorise('core.edit',       'com_tkdclub.training.' . $item->training_id);
                                $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.training.' . $item->training_id) && $item->created_by == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.training.' . $item->training_id) && $canCheckin;
                                ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->training_id); ?>
                                <td class="center hasTooltip">
                                    <?php
                                            $state = Helper::getpaystate(
                                                $item->trainer_paid,
                                                $item->assist1,
                                                $item->assist2,
                                                $item->assist3,
                                                $item->assist1_paid,
                                                $item->assist2_paid,
                                                $item->assist3_paid
                                            );

                                            $classes = array(0 => 'icon-remove tkdclub-icon-red', 1 => 'icon-publish', 2 => 'icon-plus-2 tkdclub-icon-orange');
                                            $tooltip = array(
                                                0 => JText::_('COM_TKDCLUB_TRAINING_NOT_PAID'),
                                                1 => JText::_('COM_TKDCLUB_TRAINING_PAID'),
                                                2 => JText::_('COM_TKDCLUB_TRAINING_PARTLY_PAID')
                                            );
                                            ?>
                                    <i class="btn btn-micro <?php echo $classes[$state]; ?> hasTooltip" title="" data-original-title="<?php echo $tooltip[$state]; ?>"></i>

                                </td>

                                <td width="" class="title">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'trainings.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                            $mylink = JRoute::_("index.php?option=com_tkdclub&task=training.edit&training_id=" . $item->training_id);
                                            echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, JText::_('DATE_FORMAT_LC4')) . '</a>';
                                            ?>
                                </td>
                                <td width="" class="left"><?php echo $this->escape($item->trainer_firstname) . ' ' . $this->escape($item->trainer_lastname); ?></td>
                                <td width="" class="left"><?php echo $this->escape($item->assist1_firstname) . ' ' . $this->escape($item->assist1_lastname); ?></td>
                                <td width="" class="left"><?php echo $this->escape($item->assist2_firstname) . ' ' . $this->escape($item->assist2_lastname); ?></td>
                                <td width="" class="left"><?php echo $this->escape($item->assist3_firstname) . ' ' . $this->escape($item->assist3_lastname); ?></td>
                                <td width="" class="left"><?php echo $this->escape($item->type); ?></td>
                                <td width="" class="center"><?php echo $this->escape($item->participants); ?></td>
                                <td width="250px" class="left"><?php echo $this->escape($item->notes); ?></td>
                                <td width="10" class="center"><?php echo (int) $item->training_id; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php echo $this->pagination->getListFooter(); ?>

            <!--Joomla security tasks-->
            <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
            </div>
</form>