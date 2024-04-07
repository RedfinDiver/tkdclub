<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry();
$wa->useStyle('com_tkdclub.tkdclub-admin')
    ->useScript('com_tkdclub.taskhandling');

/**
 * initilise some variables
 */
$user      = Factory::getUser();
$userId    = $user->get('id');
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=subscribers'); ?>" method="post" name="adminForm" id="adminForm">
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
                            <th width="1%">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_EMAIL'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_FIRSTNAME'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_LASTNAME'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_SUBSCRIBED'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN'); ?></th>
                            <th width=""><?php echo Text::_('COM_TKDCLUB_SUBSCRIBER_ID'); ?></th>
                        </thead>
                        <tfoot>
                            <tr>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.subscriber.' . $item->id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.subscriber.' . $item->id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.subscriber.' . $item->id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    <td width="200" class="title">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'subscribers.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php
                                                $mylink = Route::_("index.php?option=com_tkdclub&task=subscriber.edit&id=" . $item->id);
                                                echo '<a href="' . $mylink . '">' . $item->email . '</a>';
                                                ?>
                                    </td>
                                    <td><?php echo $this->escape($item->firstname) ?></td>
                                    <td><?php echo $this->escape($item->lastname) ?></td>
                                    <td><?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) ?></td>
                                    <td>
                                        <?php if ($item->origin == 2) {
                                                    echo Text::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN_FORM');
                                                } else {
                                                    echo Text::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN_MANUAL');
                                                }
                                                ?>
                                    </td>
                                    <td><?php echo $this->escape($item->id) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <?php echo $this->pagination->getListFooter(); ?>
                <div>
                    <input type="hidden" id="task" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>
</form>