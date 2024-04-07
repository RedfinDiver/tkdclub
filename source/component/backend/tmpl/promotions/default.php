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
$this->document->getWebAssetManager()
    ->useStyle('com_tkdclub.tkdclub-admin')
    ->useScript('com_tkdclub.taskhandling');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$columns   = 10;

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=promotions'); ?>" method="post" name="adminForm" id="adminForm">
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
                                <th width="">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                                </th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PROMOTION_CITY'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PROMOTION_TYPE'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PROMOTION_EXAMINER'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PROMOTION_EXAMINER_ADDRESS'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_PROMOTION_EXAMINER_EMAIL'); ?></th>
                                <th width=""><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></th>
                                <th width="1%"><?php echo Text::_('COM_TKDCLUB_PROMOTION_ID'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>

                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.promotion.' . $item->id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.promotion.' . $item->id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.promotion.' . $item->id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    <td class="center hasTooltip">
                                        <?php
                                                //creating array for alternatives tooltip text on promotion_state coloum
                                                $states = array(
                                                    1 => array('unpublish', 'COM_TKDCLUB_PROMOTION_ACTIVE', 'COM_TKDCLUB_PROMOTION_UNPUBLISH', 'JPUBLISHED', 'COM_TKDCLUB_PROMOTION_ACTIVE', 'publish', 'publish'),
                                                    0 => array('publish', 'COM_TKDCLUB_PROMOTION_INACTIVE', 'COM_TKDCLUB_PROMOTION_PUBLISH', 'JUNPUBLISHED', 'COM_TKDCLUB_PROMOTION_INACTIVE', 'unpublish', 'unpublish')
                                                );

                                                echo HTMLHelper::_('jgrid.state', $states, $item->promotion_state, $i, 'promotions.', true);
                                                ?>
                                    </td>
                                    <td width="100" class="title">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'promotions.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php
                                                $mylink = Route::_("index.php?option=com_tkdclub&task=promotion.edit&id=" . $item->id);
                                                echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')) . '</a>';
                                                ?>
                                    </td>
                                    <td width="" class="left"><?php echo $this->escape($item->city); ?></td>
                                    <?php $types = array(
                                                'kup' => 'COM_TKDCLUB_KUP',
                                                'dan' => 'COM_TKDCLUB_DAN'
                                            );
                                            $string_to_take = $types[$item->type];
                                            ?>
                                    <td width="" class="left"><?php echo Text::_($string_to_take); ?></td>
                                    <td width="" class="left"><?php echo $this->escape($item->examiner_name); ?></td>
                                    <td width="" class="left"><?php echo $this->escape($item->examiner_address); ?></td>
                                    <td width="" class="left"><?php echo $this->escape($item->examiner_email); ?></td>
                                    <td width="250px" class="left"><?php echo $this->escape($item->notes); ?></td>
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
                    <?php echo HTMLHelper::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>          
</form>