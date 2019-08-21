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

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$columns = 10;
?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=participants'); ?>"
      method="post"
      name="adminForm"
      id="adminForm">
    <!-- sidebar -->
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
    <!-- filterbar and list sorting -->
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
    <div class="clearfix"> </div>
    <!--start of table participants -->
    <table class="table table-striped">
        <!-- table head -->
        <thead>
            <tr>
                <th width="1%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th class="center" width="20"><?php echo JText::_('JSTATUS'); ?></th>
                <th width="" style="" class="nowrap center">
		        <?php echo JHTML::_( 'searchtools.sort', 'COM_TKDCLUB_DATE', 'b.date', $this->sortDirection, $this->sortColumn); ?>
                </th>  
                <th width="" style="" class="nowrap left">
                    <?php echo JText::_('COM_TKDCLUB_PARTICIPANT_EVENT_TITLE'); ?>
                </th> 
                <th width=""><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_FIRSTNAME'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_LASTNAME'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_CLUB'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_EMAIL'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_REGISTERED'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_GRADE'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_AGE'); ?></th>
                <th width="15%"><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_NOTES'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_STORE_DATA'); ?></th>
                <th><?php echo JText::_('COM_TKDCLUB_PARTICIPANT_AGREED_PRIVACY_TERMS'); ?></th>
                <th width="1%"><?php echo JText::_('COM_TKDCLUB_PARTICIPANTS_ID'); ?></th>
            </tr>
        </thead>
        <!-- table footer -->
        <tfoot>
            <tr>
                <td colspan="<?php echo $columns; ?>"></td>
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
                    <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'participants.', TRUE); ?>
                    </td>
                    <td class="left">
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'participants.', $canCheckin); ?>
                        <?php endif; ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_tkdclub&task=participant.edit&id=' . (int) $item->id); ?>">
                        <?php echo JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')); ?>
                    </td>
                    <td class="left hasTooltip">
                        <a href="<?php echo JRoute::_('index.php?option=com_tkdclub&task=participant.edit&id=' . (int) $item->id); ?>"
                            title="<?php echo $item->title; ?>">
                        <?php  echo substr($item->title, 0, 8) . '...' ?></a>
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
                           // echo $consent = $this->escape($item->store_data) ? JText::_('JYES') : JText::_('JNO');
                           echo $consent = $this->escape($item->store_data) ? '<span class="icon-publish"></span>' : '<span class="icon-unpublish"></span>';
                        ?>
                    </td>
                    <td>
                        <?php
                           // echo $consent = $this->escape($item->store_data) ? JText::_('JYES') : JText::_('JNO');
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
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>