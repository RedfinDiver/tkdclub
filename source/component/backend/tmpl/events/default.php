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
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$this->document->getWebAssetManager()
    ->useStyle('com_tkdclub.tkdclub-admin')
    ->useScript('com_tkdclub.taskhandling');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$columns   = 10;
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=events'); ?>" method="post" name="adminForm" id="adminForm">
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
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.event.' . $item->id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.event.' . $item->id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.event.' . $item->id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    </td>
                                    <td>
                                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'events.', TRUE); ?>
                                    </td>
                                    <td class="title">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php
                                                $mylink = Route::_("index.php?option=com_tkdclub&task=event.edit&id=" . $item->id);
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
                                        <?php echo TkdclubHelper::getEventparts($item->id); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($item->notes) ?>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($item->id) ?>
                                    </td>
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
