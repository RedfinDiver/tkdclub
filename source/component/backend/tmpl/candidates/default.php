<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var \Redfindiver\Component\Tkdclub\Administrator\View\Members\HtmlView $this */
HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$this->document->getWebAssetManager()
    ->useScript('com_tkdclub.iban')
    ->useStyle('com_tkdclub.tkdclub-admin')
    ->useScript('com_tkdclub.taskhandling');

$params = ComponentHelper::getParams('com_tkdclub');
$currency = $params->get('currency', '€');
$costs = $params->get('badge_cost', 0) + $params->get('examiner_cost', 0) + $params->get('club_cost', 0);

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=candidates'); ?>" method="post" name="adminForm" id="adminForm">
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
                        <th width="1%" class="hidden-phone">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th class="center" width="20"><?php echo Text::_('JSTATUS'); ?></th>
                        <th class="center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TKDCLUB_CANDIDATE_PROMOTION_DATE', 'c.date', $listDirn, $listOrder); ?>
                        </th>
                        <th class="center"><?php echo Text::_('COM_TKDCLUB_PROMOTION_TYPE') ?></th>
                        <th class="center"><?php echo Text::_('COM_TKDCLUB_MEMBER_PASS') ?></th>
                        <th><?php echo Text::_('COM_TKDCLUB_MEMBER_FIRSTNAME') ?></th>
                        <th><?php echo Text::_('COM_TKDCLUB_MEMBER_LASTNAME') ?></th>
                        <th>
                            <?php echo HTMLHelper::tooltip(Text::_('COM_TKDCLUB_MEMBER_AGE_AT_PROMOTION'), Text::_('COM_TKDCLUB_MEMBER_AGE'), '', Text::_('COM_TKDCLUB_MEMBER_AGE')); ?>
                        </th>
                        <th width=""><?php echo Text::_('COM_TKDCLUB_CANDIDATE_PROMOTION_GRADE_ACHIEVE'); ?></th>
                        <th class="center"><?php echo Text::_('COM_TKDCLUB_CANDIDATE_PROMOTION_COSTS'); ?></th>
                        <th width="25%"><?php echo Text::_('COM_TKDCLUB_NOTES'); ?></th>
                    </thead>

                    <tfoot>
                        <tr>
  
                        </tr>
                    </tfoot>

                    <tbody>
                        <?php foreach ($this->items as $i => $item) :
                                $canEdit    = $user->authorise('core.edit',       'com_tkdclub.candidate.' . $item->id);
                                $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.candidate.' . $item->id) && $item->created_by == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.candidate.' . $item->id) && $canCheckin;
                                ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                <td class="center hasTooltip">
                                    <div class="btn-group">
                                        <?php
                                                // creating array for alternatives tooltip text on test_state column
                                                $states = array(
                                                    1 => array('unpublish', 'COM_TKDCLUB_CANDIDATE_PASSED', 'COM_TKDCLUB_CANDIDATE_MARK_NOT_PASSED', 'JPUBLISHED', 'COM_TKDCLUB_CANDIDATE_PASSED', 'publish', 'publish'),
                                                    0 => array('publish', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', 'COM_TKDCLUB_CANDIDATE_MARK_PASSED', 'JUNPUBLISHED', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', 'unpublish', 'unpublish'),
                                                    2 => array('publish', 'COM_TKDCLUB_CANDIDATE_SUBSCRIBED', 'COM_TKDCLUB_CANDIDATE_MARK_PASSED', 'JARCHIVED', true, 'archive', 'archive')
                                                );
                                                echo HTMLHelper::_('jgrid.state', $states, $item->test_state, $i, 'candidates.', true);
                                                ?>
                                    </div>
                                </td>
                                <td class="title center">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'candidates.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                            $mylink = Route::_("index.php?option=com_tkdclub&task=candidate.edit&form=candidate_edit&id=" . $item->id);
                                            echo '<a href="' . $mylink . '">' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')) . '</a>';
                                            ?>
                                    <span class="small"><?php echo '(' . $this->escape($item->city) . ')' ?></span>
                                </td>
                                <?php $type = array('kup' => Text::_('COM_TKDCLUB_KUP'), 'dan' => Text::_('COM_TKDCLUB_DAN')) ?>
                                <td class="center"><?php echo $type[$this->escape($item->type)]; ?></td>
                                <td class="center">
                                    <?php echo $this->escape($item->memberpass) > 0 ? $this->escape($item->memberpass) : ''; ?>
                                </td>
                                <td><?php echo $this->escape($item->firstname); ?></td>
                                <td><?php echo $this->escape($item->lastname); ?></td>
                                <td class="center">
                                    <?php $age = TkdclubHelper::getAgetoDate($item->date, $item->birthdate);
                                            echo $age; ?>
                                </td>
                                <td class="center"><?php echo $this->escape($item->grade_achieve); ?></td>
                                <td class="center">
                                    <?php
                                            if ($item->type == 'kup') {
                                                $age < 15 ? $age_costs = $params->get('fed_cost_under_15', 0) : $age_costs = $params->get('fed_cost_from_15', 0);
                                                echo $costs + $age_costs . ' ' . $currency;
                                            }
                                            if ($item->type == 'dan') {
                                                $dan = $item->grade_achieve[0];
                                                echo $params->get('dan_' . $dan, 0) . ' ' . $currency;
                                            }
                                            ?>
                                </td>
                                <td class="left"><?php echo $this->escape($item->notes); ?></td>
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