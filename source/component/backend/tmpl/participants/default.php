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

HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_tkdclub.tkdclub-admin')
    ->useScript('com_tkdclub.taskhandling');

$user      = Factory::getUser();
$userId    = $user->get('id');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=participants'); ?>" method="post" name="adminForm" id="adminForm">
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
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </th>
                                <th class="center" width="20"><?php echo Text::_('JSTATUS'); ?></th>
                                <th width="" class="nowrap center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'b.date', $this->sortDirection, $this->sortColumn); ?>
                                </th>
                                <th width="" class="nowrap left">
                                    <?php echo Text::_('COM_TKDCLUB_PARTICIPANT_EVENT_TITLE'); ?>
                                </th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_FIRSTNAME'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_LASTNAME'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_CLUB'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_EMAIL'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_REGISTERED'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_GRADE'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_AGE'); ?></th>
                                <th width="15%"><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_NOTES'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_STORE_DATA'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_PARTICIPANT_AGREED_PRIVACY_TERMS'); ?></th>
                                <th width="1%"><?php echo Text::_('COM_TKDCLUB_PARTICIPANTS_ID'); ?></th>
                            </tr>
                        </thead>
                        <!-- table footer -->
                        <tfoot>
                            <tr>
                            </tr>
                        </tfoot>
                        <!-- table body -->
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.subscriber.' . $item->id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.subscriber.' . $item->id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.subscriber.' . $item->id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                                    <td class="center">
                                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'participants.', TRUE); ?>
                                    </td>
                                    <td class="left">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'participants.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <a href="<?php echo Route::_('index.php?option=com_tkdclub&task=participant.edit&id=' . (int) $item->id); ?>">
                                            <?php echo HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')); ?>
                                    </td>
                                    <td class="left hasTooltip">
                                        <a href="<?php echo Route::_('index.php?option=com_tkdclub&task=participant.edit&id=' . (int) $item->id); ?>" title="<?php echo $item->title; ?>">
                                            <?php echo substr($item->title, 0, 8) . '...' ?></a>
                                    </td>
                                    <td class="left"><?php echo $this->escape($item->firstname); ?></td>
                                    <td class="left"><?php echo $this->escape($item->lastname); ?></td>
                                    <td class="left"><?php echo $this->escape($item->clubname); ?></td>
                                    <td class="left"><?php echo $this->escape($item->email); ?></td>
                                    <td class="left"><?php echo $this->escape($item->registered); ?></td>
                                    <td class="left"><?php echo $this->escape($item->grade); ?></td>
                                    <td class="left"><?php echo $this->escape($item->age); ?></td>
                                    <td class="left"><?php echo $this->escape($item->notes); ?></td>
                                    <td>
                                        <?php
                                                // echo $consent = $this->escape($item->store_data) ? Text::_('JYES') : Text::_('JNO');
                                                echo $consent = $this->escape($item->store_data) ? '<span class="icon-publish"></span>' : '<span class="icon-unpublish"></span>';
                                                ?>
                                    </td>
                                    <td>
                                        <?php
                                                // echo $consent = $this->escape($item->store_data) ? Text::_('JYES') : Text::_('JNO');
                                                echo $consent = $this->escape($item->privacy_agreed) ? '<span class="icon-publish"></span>' : '<span class="icon-unpublish"></span>';
                                                ?>
                                    </td>
                                    <td width="10" class="center"><?php echo (int) $item->id; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <?php echo $this->pagination->getListFooter(); ?>
                <div>
                    <input type="hidden" id="task" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <?php echo HtmlHelper::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>
</form>