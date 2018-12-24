<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');
JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

JLoader::register('TkdclubHelperGetEventParts', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/geteventparts.php');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$columns   = 10;
?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=events'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
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
            // TODO for statistic: include_once(JPATH_COMPONENT . '/includes/eventsstats.php');
        ?>
    <?php endif; ?>
        <table class="table table-striped table-condensed" id="eventList">
            <thead>
                <tr>
                    <th width="1%" class="center">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
                    <th width="1%">
                        <?php echo JText::_('JSTATUS'); ?>
                    </th>
                    <th class="nowrap">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_DEADLINE') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_TITLE') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_MINIMUM_PARTICIPANTS') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_MAXIMUM_PARTICIPANTS') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_SUBSCRIBED_PARTICIPANTS') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_NOTES') ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_TKDCLUB_EVENT_ID') ?>
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
						<?php echo JHtml::_('grid.id', $i, $item->event_id); ?>
					</td>
                    <td>
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'events.', TRUE); ?>
                    </td>
                    <td class="title">
                        <?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin); ?>
						<?php endif; ?>
                        <?php
                            $mylink = JRoute::_("index.php?option=com_tkdclub&task=event.edit&event_id=".$item->event_id);
                            echo '<a href="'.$mylink.'">'.JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')).'</a>';
                        ?>
                    </td>
                    <td>
                        <?php echo JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')); ?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->title)?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->min)?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->max)?>
                    </td>
                    <td>
                        <?php echo TkdclubHelperGetEventParts::getEventparts($item->event_id); ?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->notes)?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->event_id)?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php echo $this->pagination->getListFooter(); ?>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
    </div>
</form>