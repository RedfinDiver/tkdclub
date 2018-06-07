<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperAge', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/agecalc.php');

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

$params = JComponentHelper::getParams('com_tkdclub');
$currency = $params->get('currency', '€');
$costs = $params->get('badge_cost', 0) + $params->get('examiner_cost', 0) + $params->get('club_cost', 0);

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=candidates'); ?>" method="post" name="adminForm" id="adminForm">
    
<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
    <?php
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
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
    <?php if($this->togglestats) :  ?>
    <?php  
        include_once(JPATH_COMPONENT . '/includes/candidatesstats.php');
    ?>
    <?php endif; ?>
    <div class="clearfix"> </div>

    <table class="table table-condensed table-striped"> 
        <thead>
            <th width="1%" class="hidden-phone">
                <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th class="center" width="20"><?php echo JText::_('JSTATUS'); ?></th>
            <th class="center">
                <?php echo JHtml::_('searchtools.sort', 'COM_TKDCLUB_CANDIDATE_PROMOTION_DATE', 'c.date', $listDirn, $listOrder); ?>
            </th>
            <th class="center"><?php echo JText::_('COM_TKDCLUB_PROMOTION_TYPE') ?></th>
            <th class="center"><?php echo JText::_('COM_TKDCLUB_MEMBER_PASS') ?></th>
            <th><?php echo JText::_('COM_TKDCLUB_MEMBER_FIRSTNAME') ?></th>
            <th><?php echo JText::_('COM_TKDCLUB_MEMBER_LASTNAME') ?></th>
            <th>
                <?php echo JHtml::tooltip(JText::_('COM_TKDCLUB_MEMBER_AGE_AT_PROMOTION'), JText::_('COM_TKDCLUB_MEMBER_AGE'), '', JText::_('COM_TKDCLUB_MEMBER_AGE')); ?> 
            </th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_CANDIDATE_PROMOTION_GRADE_ACHIEVE'); ?></th>
            <th class="center"><?php echo JText::_('COM_TKDCLUB_CANDIDATE_PROMOTION_COSTS'); ?></th>
            <th width="25%"><?php echo JText::_('COM_TKDCLUB_NOTES'); ?></th>
        </thead>

        <tfoot>
            <tr>
                <td colspan="18"><?php echo $this->pagination->getListFooter(); ?></td>
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
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    <td class="center hasTooltip">
                        <div class="btn-group">
                            <?php 
                                // creating array for alternatives tooltip text on test_state column
                                $states = array(1 => array('unpublish', 'COM_TKDCLUB_CANDIDATE_PASSED', 'COM_TKDCLUB_CANDIDATE_MARK_NOT_PASSED', 'JPUBLISHED', 'COM_TKDCLUB_CANDIDATE_PASSED', 'publish', 'publish'),
                                                0 => array('publish', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', 'COM_TKDCLUB_CANDIDATE_MARK_PASSED', 'JUNPUBLISHED', 'COM_TKDCLUB_CANDIDATE_NOT_PASSED', 'unpublish', 'unpublish'),
                                                2 => array('publish', 'COM_TKDCLUB_CANDIDATE_SUBSCRIBED', 'COM_TKDCLUB_CANDIDATE_MARK_PASSED', 'JARCHIVED', true, 'archive', 'archive'));
                                echo JHtml::_('jgrid.state', $states, $item->test_state, $i, 'candidates.', true);
                            ?>
                        </div>
                    </td>
                    <td class="title center">
                        <?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'candidates.', $canCheckin); ?>
						<?php endif; ?>
                        <?php
                            $mylink = JRoute::_("index.php?option=com_tkdclub&task=candidate.edit&form=candidate_edit&id=".$item->id);
                            echo '<a href="'.$mylink.'">'.JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')) . '</a>';
                        ?>
                        <span class="small"><?php echo '(' . $this->escape($item->city) . ')'?></span>      
                    </td>
                    <?php $type = array('kup' => JText::_('COM_TKDCLUB_KUP'), 'dan' => JText::_('COM_TKDCLUB_DAN')) ?>
                    <td class="center"><?php echo $type[$this->escape($item->type)]; ?></td>
                    <td class="center">
                        <?php echo $this->escape($item->memberpass) > 0 ? $this->escape($item->memberpass): ''; ?>
                    </td>
                    <td><?php echo $this->escape($item->firstname); ?></td>
                    <td><?php echo $this->escape($item->lastname); ?></td>
                    <td class="center">
                        <?php $age = TkdClubHelperAge::getAgetoDate($item->date, $item->birthdate); echo $age; ?>
                    </td>
                    <td class="center"><?php echo $this->escape($item->grade_achieve); ?></td>
                    <td class="center">
                        <?php
                        if ($item->type == 'kup')
                        {
                            $age < 15 ? $age_costs = $params->get('fed_cost_under_15', 0) : $age_costs = $params->get('fed_cost_from_15', 0);
                            echo $costs + $age_costs . ' ' . $currency;
                        }
                        if ($item->type == 'dan')
                        {
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
    </div>
    <div>
       <input type="hidden" name="task" value="" />
       <input type="hidden" name="boxchecked" value="0" />
       <?php echo JHtml::_('form.token'); ?>   
    </div>
</form>