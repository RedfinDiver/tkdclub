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

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');
HTMLHelper::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$columns   = 10;
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=events'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <?php
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
            <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>
                <div class="tkdclub-numbers">
                    <b><?php echo $this->total; ?></b> <?php echo Text::_('COM_TKDCLUB_FROM'); ?>
                    <b><?php echo $this->allrows; ?></b>
                    <?php echo Text::_('COM_TKDCLUB_ENTRIES'); ?>
                </div>
                <!-- TODO statistics for events -->
            <?php endif; ?>
            <table class="table table-striped table-condensed" id="eventList">
                <thead>
                    <tr>
                        <th width="1%" class="center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th width="1%">
                            <?php echo Text::_('JSTATUS'); ?>
                        </th>
                        <th class="nowrap">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_DEADLINE') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_TITLE') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_MINIMUM_PARTICIPANTS') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_MAXIMUM_PARTICIPANTS') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_NOTES') ?>
                        </th>
                        <th>
                            <?php echo Text::_('COM_TKDCLUB_EVENT_ID') ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="<?php echo $columns; ?>"></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($this->items as $i => $item) :
                        $canEdit    = $user->authorise('core.edit',       'com_tkdclub.event.' . $item->event_id);
                        $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.event.' . $item->event_id) && $item->created_by == $userId;
                        $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.event.' . $item->event_id) && $canCheckin;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->event_id); ?>
                            </td>
                            <td>
                                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'events.', TRUE); ?>
                            </td>
                            <td class="title">
                                <?php if ($item->checked_out) : ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php
                                    $mylink = Route::_("index.php?option=com_tkdclub&task=event.edit&event_id=" . $item->event_id);
                                    echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')) . '</a>';
                                    ?>
                            </td>
                            <td>
                                <?php echo HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')); ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item->title) ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item->min) ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item->max) ?>
                            </td>
                            <td>
                                <?php echo Helper::getEventparts($item->event_id); ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item->notes) ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item->event_id) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php echo $this->pagination->getListFooter(); ?>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo HTMLHelper::_('form.token'); ?>
            </div>
</form>