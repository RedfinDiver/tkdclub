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
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tkdclub.php');

HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
HTMLHelper::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$columns   = 10;
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=medals'); ?>" method="post" name="adminForm" id="adminForm">
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
                    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>
                <!-- statistics and numbers -->
                <div class="tkdclub-numbers">
                    <b><?php echo $this->total; ?></b> <?php echo Text::_('COM_TKDCLUB_FROM'); ?>
                    <b><?php echo $this->allrows; ?></b>
                    <?php echo Text::_('COM_TKDCLUB_ENTRIES'); ?>
                </div>
                <?php if ($this->togglestats) :  ?>
                    <?php
                            include_once(JPATH_COMPONENT . '/includes/medalsstats.php');
                            ?>
                <?php endif; ?>

                <div class="clearfix"> </div>

                <!-- begin of table -->
                <table class="table table-condensed">
                    <!-- begin of table head -->
                    <thead>
                        <tr>
                            <th width="1%">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th width="1%">
                                <?php echo Text::_('JSTATUS'); ?>
                            </th>
                            <th class="center" width="100"><?php echo Text::_('COM_TKDCLUB_DATE'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP_TYPE'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_MEDAL_CLASS'); ?></th>
                            <th class="center" width=""><?php echo JText::_('COM_TKDCLUB_MEDAL_PLACING'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_ATHLETS'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></th>
                            <th width="1%">
                                <?php echo Text::_('COM_TKDCLUB_MEDAL_ID'); ?>
                            </th>
                        </tr>
                    </thead>
                    <!-- beginn of table footer -->
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo $columns; ?>"></td>
                        </tr>
                    </tfoot>
                    <!-- start of table body-->
                    <tbody>
                        <?php foreach ($this->items as $i => $item) :
                                $canEdit    = $user->authorise('core.edit',       'com_tkdclub.medal.' . $item->medal_id);
                                $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.medal.' . $item->medal_id) && $item->created_by == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.medal.' . $item->medal_id) && $canCheckin;
                                ?>

                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->medal_id); ?>
                                <td>
                                    <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'medals.', TRUE); ?>
                                </td>
                                <td class="title" width="60">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'medals.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                            $mylink = Route::_("index.php?option=com_tkdclub&task=medal.edit&medal_id=" . $item->medal_id);
                                            echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')) . '</a>';
                                            ?>
                                </td>
                                <td width=""><?php echo $this->escape($item->championship); ?></td>
                                <td width=""><?php echo $this->escape($item->type); ?></td>
                                <td width=""><?php echo $this->escape($item->class); ?></td>
                                <?php $medalclass = array('1' => 'tkdclub-goldmedal', '2' => 'tkdclub-silbermedal', '3' => 'tkdclub-bronzemedal'); ?>
                                <td class="center">
                                    <span class="<?php echo $medalclass[(int) $item->placing] ?>">
                                        <?php echo $this->escape($item->placing); ?>
                                    </span>
                                </td>
                                <td width=""><?php echo Helper::getMembersNames($item->winner_ids, $this->memberlist); ?></td>
                                <td><?php echo $this->escape($item->notes); ?></td>
                                <td class="center" width="10"><?php echo (int) $item->medal_id; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- end of table body -->
                </table>
                <!-- end of table -->
            <?php endif; ?>

            <?php echo $this->pagination->getListFooter(); ?>

            <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
            </div>
</form>