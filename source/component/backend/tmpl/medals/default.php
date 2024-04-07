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
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

HTMLHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry();
$wa->useStyle('com_tkdclub.tkdclub-admin')
        ->useScript('com_tkdclub.taskhandling');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$gold = isset($this->medaldata['placings']['1']) ? $this->medaldata['placings']['1'] : 0;
$silver = isset($this->medaldata['placings']['2']) ? $this->medaldata['placings']['2'] : 0;
$bronce = isset($this->medaldata['placings']['3']) ? $this->medaldata['placings']['3'] : 0;
?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=medals'); ?>" method="post" name="adminForm" id="adminForm">
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
                    <div id="medalsstats" class="d-none container">
                        <div class="p-3 row text-center">
                            <div class="col-md-3">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <span class="fas fa-medal fa-3x gold"></span>
                                        <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?></h5>
                                        <div><?php echo $gold; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <span class="fas fa-medal fa-3x silver"></span>
                                        <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?></h5>
                                        <div><?php echo $silver ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <span class="fas fa-medal fa-3x bronce"></span>
                                        <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?></h5>
                                        <div><?php echo $bronce ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col"><span class="fas fa-medal fa-3x gold"></span></div>
                                            <div class="col"><span class="fas fa-medal fa-3x silver"></span></div>
                                            <div class="col"><span class="fas fa-medal fa-3x bronce"></span></div>
                                        </div>
                                        
                                        <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY') ?></h5>
                                        <div><?php echo $this->medaldata['sum'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HtmlHelper::_('grid.checkall'); ?>
                                </td>
                                <th  class="w-1 text-center"><?php echo Text::_('JSTATUS'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_DATE'); ?></th>
                                <th scope="col" class="w-25 text-start d-none d-md-table-cell"><?php echo Text::_('COM_TKDCLUB_ATHLETS'); ?></th>
                                <th scope="col" class="text-center d-none d-md-table-cell"><?php echo JText::_('COM_TKDCLUB_MEDAL_PLACING'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP_TYPE'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEDAL_CLASS'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></th>
                                <th scope="col" class="text-center d-none d-md-table-cell"><?php echo Text::_('COM_TKDCLUB_TRAINING_ID'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>

                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                        $canEdit    = $user->authorise('core.edit',       'com_tkdclub.medal.' . $item->id);
                                        $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                        $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.medal.' . $item->id) && $item->created_by == $userId;
                                        $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.medal.' . $item->id) && $canCheckin;
                                        ?>

                                    <tr class="row<?php echo $i % 2; ?>">
                                        <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        <td>
                                            <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'medals.', TRUE); ?>
                                        </td>
                                        <th scope="row">
                                            <?php if ($item->checked_out) : ?>
                                                <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'medals.', $canCheckin); ?>
                                            <?php endif; ?>
                                            <?php
                                                    $mylink = Route::_("index.php?option=com_tkdclub&task=medal.edit&id=" . $item->id);
                                                    echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')) . '</a>';
                                                    ?>
                                        </th>
                                        <?php $winner_ids = array($item->winner_1, $item->winner_2, $item->winner_3) ?>
                                        <td width=""><?php echo TkdclubHelper::getMembersNames($winner_ids, $this->memberlist); ?></td>
                                        <?php $medalclass = array('1' => 'goldmedal', '2' => 'silbermedal', '3' => 'bronzemedal'); ?>
                                        <td class="text-center">
                                            <span class="<?php echo $medalclass[(int) $item->placing] ?>">
                                                <?php echo $this->escape($item->placing); ?>
                                            </span>
                                        </td>
                                        <td width=""><?php echo $this->escape($item->championship); ?></td>
                                        <td width=""><?php echo $this->escape($item->type); ?></td>
                                        <td width=""><?php echo $this->escape($item->class); ?></td>
                                        <td><?php echo $this->escape($item->notes); ?></td>
                                        <td class="center" width="10"><?php echo (int) $item->id; ?></td>
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