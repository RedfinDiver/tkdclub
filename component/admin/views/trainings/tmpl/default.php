<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');

/**
 * initilise some variables
 */
$currency = JComponentHelper::getParams('com_tkdclub')->get('currency', '€');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$listLimit = $this->state->get('list.limit');

$filter_trainer = $this->state->get('filter.trainer');
$assist_name    = TkdclubModelTrainings::getTrainerName($filter_trainer);
$trainer_name   = TkdclubModelTrainings::getTrainerName($filter_trainer);

$filter_type   = $this->state->get('filter.type');
$filter_year   = $this->state->get('filter.year');
$filter_search = $this->state->get('filter.search');
$filter_payment_state  = $this->state->get('filter.payment_state');
?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=trainings'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">  
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
        <!-- filters and sorting tools -->
        <?php
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>            
        <div id="filter-bar" class="btn-toolbar">
            <div class="btn-group pull-left hidden-phone">
                <label>
                    <b><?php echo $this->total; ?></b> <?php echo JText::_('COM_TKDCLUB_TRAINING_FROM'); ?>
                        <b><?php echo $this->allrows; ?></b>
                    <?php echo JText::_('COM_TKDCLUB_TRAINING_ENTRIES'); ?>
                </label>
            </div>  
        </div>
    
        <div class="clearfix"> </div>
        
        <div class="alert alert-info">
            <?php  
                include_once(JPATH_COMPONENT . '/includes/trainersalary.php');
            ?>
        </div>
    
        <!-- Beginn der Tabelle mit den Einträgen--> 
        <table class="table table-condensed">
            <!-- Überschriften der Tabelle-->
            <thead>
                <tr>
                    <th width="1%">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th class="center" width="20"><?php echo JText::_('COM_TKDCLUB_TRAINING_PAID'); ?></th>
                    <th width="">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TKDCLUB_DATE', 'date', $listDirn, $listOrder); ?>
                    </th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_TRAINER'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT1'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT2'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_ASSISTENT3'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_TYPE'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_PARTICIPANTS'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_TRAINING_NOTES'); ?></th>
                    <th width="1%"><?php echo JText::_('COM_TKDCLUB_TRAINING_ID'); ?>
                    </th>
                </tr>
            </thead>
            <!--footer -->
            <tfoot><tr><td colspan="18">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td></tr>
            </tfoot>
            <!-- table body-->
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->training_id); ?>
                    <td class="center hasTooltip">
                        <?php 
                            //creating array for alernatives tooltip text on training_state coloum
                            $states = array(1 => array('unpublish', 'COM_TKDCLUB_TRAINING_PAID', 'COM_TKDCLUB_TRAINING_UNPAY', 'JPUBLISHED', 'COM_TKDCLUB_TRAINING_PAID', 'publish', 'publish'),
                                            0 => array('publish', 'COM_TKDCLUB_TRAINING_NOT_PAID', 'COM_TKDCLUB_TRAINING_PAY', 'JUNPUBLISHED', 'COM_TKDCLUB_TRAINING_NOT_PAID', 'unpublish', 'unpublish'));

                            echo JHtml::_('jgrid.state', $states, $item->payment_state, $i, 'trainings.', true);
                        ?>

                    </td>         
                    
                    <td width="60" class="title"><?php
                        $mylink = JRoute::_("index.php?option=com_tkdclub&task=training.edit&training_id=".$item->training_id);
                        echo '<a href="'.$mylink.'">'.JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC4')).'</a>';
                        ?>
                    </td>
                    <td width="" class="left"><?php echo $this->escape($item->trainer_firstname) . ' ' . $this->escape($item->trainer_lastname); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->assist1_firstname) . ' ' . $this->escape($item->assist1_lastname); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->assist2_firstname) . ' ' . $this->escape($item->assist2_lastname); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->assist3_firstname) . ' ' . $this->escape($item->assist3_lastname); ?></td>
                    <td width="" class="left"><?php echo $this->escape($item->type); ?></td>
                    <td width="" class="center"><?php echo $this->escape($item->participants); ?></td>                     
                    <td width="250px" class="left"><?php echo $this->escape($item->notes); ?></td>
                    <td width="10" class="center"><?php echo (int) $item->training_id; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<!--Joomla security tasks-->
    <div>
       <input type="hidden" name="task" value="" />
       <input type="hidden" name="boxchecked" value="0" />
       <?php echo JHtml::_('form.token'); ?>
    </div>
</div>
</form>