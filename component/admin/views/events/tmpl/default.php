<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JLoader::register('TkdclubHelperGetEventparts', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/geteventparts.php');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=events'); ?>"
      method="post"
      name="adminForm"
      id="adminForm">
 
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
        <?php echo JText::_('COM_TKDCLUB_EVENTS_NOEVENTS'); ?>
    </div>
<?php else : ?>           
    <div class="clearfix"> </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width="1%" style="min-width:55px" class="nowrap center">
                    <?php echo JText::_('JSTATUS'); ?>
                </th>
                <th width="1%" style="" class="nowrap center">
                    <?php echo JHTML::_( 'grid.sort', 'COM_TKDCLUB_EVENT_DATE', 'date', $this->sortDirection, $this->sortColumn); ?>  
                </th>
                <th width="1%" style="" class="nowrap center">
                    <?php echo JTEXT::_('COM_TKDCLUB_EVENT_DEADLINE'); ?>  
                </th>  
                <th width="" style="" class="nowrap left">
                    <?php echo JText::_('COM_TKDCLUB_EVENT_TITLE'); ?>
                </th>            
                <th width=""><?php echo JText::_('COM_TKDCLUB_EVENT_MINPART'); ?></th>
                <th width=""><?php echo JText::_('COM_TKDCLUB_EVENT_MAXPART'); ?></th>
                <th width="1%">
                    <?php echo JText::_('COM_TKDCLUB_EVENT_SIGNEDUP'); ?>
                </th>
                <th width="1%">
                    <?php echo JText::_('COM_TKDCLUB_EVENT_ID'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr><td colspan="18"></td></tr>
        </tfoot>
        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
            <tr class="row<?php echo $i % 2; ?>">
                
                <td><?php echo JHtml::_('grid.id', $i, $item->id); ?> </td> 
                <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'events.', TRUE); ?></td>
                <td width="" class="left">
                    <a href="<?php echo JRoute::_('index.php?option=com_tkdclub&task=event.edit&id='.(int) $item->id); ?>">
                    <?php echo JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td width="" class="left">
                    <?php echo JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC4')); ?>
                </td>            
                <td width="" class="title">
                    <a href="<?php echo JRoute::_('index.php?option=com_tkdclub&task=event.edit&id='.(int) $item->id); ?>">
                    <?php echo $this->escape($item->title); ?></a>
                </td>
                <td width="" class="left"><?php echo $this->escape($item->min_parts); ?></td>
                <td width="" class="left"><?php echo $this->escape($item->max_parts); ?></td>
                <td class="center">
                    <?php
                        echo TkdClubHelperGetEventparts::getEventparts($this->escape($item->id));
                    ?>
                </td>
                <td width="10" class="center"><?php echo (int) $item->id; ?></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
      <?php endif; ?> 
    <div>
       <input type="hidden" name="task" value="" />
       <input type="hidden" name="boxchecked" value="0" />
       <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	   <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
       <?php echo JHtml::_('form.token'); ?>
    </div>
</div>
            
</form>
