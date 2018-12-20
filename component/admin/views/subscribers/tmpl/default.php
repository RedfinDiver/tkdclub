<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');

/**
 * initilise some variables
 */
$user      = JFactory::getUser();
$userId    = $user->get('id');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=subscibers'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">  
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
    <!-- start of table with items --> 
    <table class="table table-condensed">
            <!-- table headings -->
            <thead>
                <th width="1%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_EMAIL'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_FIRSTNAME'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_LASTNAME'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_SUBSCRIBED'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_SUBSCRIBER_ID'); ?></th>
            </thead>
            <!--footer -->
            <tfoot>
                <tr></tr>
            </tfoot>
            <!-- table body-->
            <tbody>
                <?php foreach ($this->items as $i => $item) :
                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.subscriber.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.subscriber.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_tkdclub.subscriber.' . $item->id) && $canCheckin;
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    <td width="150" class="title">
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'trainings.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php
                            $mylink = JRoute::_("index.php?option=com_tkdclub&task=subscriber.edit&id=".$item->id);
                            echo '<a href="'.$mylink.'">'. $item->email . '</a>';
                        ?>
                    </td>
                    <td><?php echo $this->escape($item->firstname) ?></td>
                    <td><?php echo $this->escape($item->lastname) ?></td>
                    <td><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')) ?></td>
                    <td>
                        <?php if ($item->origin == 1)
                              {
                                  echo JText::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN_FORM');
                              }
                              else
                              {
                                  echo JText::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN_MANUAL');
                              }
                        ?>
                    </td>
                    <td><?php echo $this->escape($item->id) ?></td>
                </tr>
                <?php endforeach; ?>
            
            </tbody>
        </table>
<!--Joomla security tasks-->
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</div>
<!-- end of main container -->
</div>
</form>