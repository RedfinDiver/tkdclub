<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=promotions'); ?>"
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
    <?php endif; ?>
     
    <div class="clearfix"> </div>

    <!--start of table promotions -->
    <table class="table table-striped table-condensed">
        <!-- start of table head -->
        <thead>
            <tr>
                <th width="1%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th class="center" width="20"><?php echo JText::_('JSTATUS'); ?></th>
                <th width="">
                    <?php echo JHtml::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                </th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_PROMOTION_CITY'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_PROMOTION_TYPE'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_PROMOTION_EXAMINER'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_PROMOTION_EXAMINER_ADDRESS'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_PROMOTION_EXAMINER_EMAIL'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_NOTES'); ?></th>
                <th width="1%"><?php echo JText::_('COM_TKDCLUB_PROMOTION_ID'); ?>
                </th>
            </tr>
        </thead>        
        <!-- footer -->
        <tfoot>
            <tr>
                <td colspan="18"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
        <!-- table body -->
        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->promotion_id); ?>
                    <td class="center hasTooltip">
                        <?php 
                            //creating array for alernatives tooltip text on promotion_state coloum
                            $states = array(1 => array('unpublish', 'COM_TKDCLUB_PROMOTION_ACTIVE', 'COM_TKDCLUB_PROMOTION_UNPUBLISH', 'JPUBLISHED', 'COM_TKDCLUB_PROMOTION_ACTIVE', 'publish', 'publish'),
                                            0 => array('publish', 'COM_TKDCLUB_PROMOTION_INACTIVE', 'COM_TKDCLUB_PROMOTION_PUBLISH', 'JUNPUBLISHED', 'COM_TKDCLUB_PROMOTION_INACTIVE', 'unpublish', 'unpublish'));

                            echo JHtml::_('jgrid.state', $states, $item->promotion_state, $i, 'promotions.', true);
                        ?>
                    </td>         
                    <td width="60" class="title"><?php
                        $mylink = JRoute::_("index.php?option=com_tkdclub&task=promotion.edit&promotion_id=".$item->promotion_id);
                        echo '<a href="'.$mylink.'">'.JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')).'</a>';
                        ?>
                    </td>
                    <td width="" class="left"><?php echo $this->escape($item->city); ?></td>
                    <?php $types = array('kup' => 'COM_TKDCLUB_PROMOTION_KUP',
                                         'dan' => 'COM_TKDCLUB_PROMOTION_DAN');
                            $string_to_take = $types[$item->type];
                    ?>
                    <td width="" class="left"><?php echo JText::_($string_to_take); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->examiner_name); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->examiner_address); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->examiner_email); ?></td>                   
                    <td width="250px" class="left"><?php echo $this->escape($item->notes); ?></td>
                    <td width="10" class="center"><?php echo (int) $item->promotion_id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>