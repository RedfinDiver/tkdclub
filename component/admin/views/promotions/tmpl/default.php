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
JLoader::register('TkdClubHelper', JPATH_COMPONENT_ADMINISTRATOR. 'helpers/tkdclub.php');
JLoader::register('TkdClubModelExams', JPATH_COMPONENT_ADMINISTRATOR. 'models/exams.php');


$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=exams'); ?>"
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
            
            <div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_TKDCLUB_SEARCH_IN_COLOUMS_MEDALS');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_TKDCLUB_SEARCH_IN_COLOUMS_MEDALS'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
                        <div class="btn-group pull-left hidden-phone">
				<label style="font-weight: bold;color: green;" class="hasTooltip" title="<p>In dieser Liste werden die Prüfungen angelegt und verwaltet.<br>
                                       Nach dem Anlegen der gewünschten Prüfung können Prüflinge<br> in der Teilnehmerliste zu dieser angelegten Prüfung hinzugefügt werden.
                                       <br>Bitte dafür den Button 'zur Teinehmerliste' in der Toolbar klicken.</p>">Prüfungen anlegen und verwalten!</label>      
			</div>
            </div>
   <div class="clearfix"> </div>
    
<!--Beginn der Tabelle Erfolge-->
<table class="table table-condensed">
    <!--Beginn des Tabellenkopfes-->
    <thead>
        <tr>
            <th width="1%">
                <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_TKDCLUB_EXAMLIST_ID', 'id', $listDirn, $listOrder); ?>
            </th>
            <th class="center" width="">
                <?php echo JHtml::_('grid.sort', 'COM_TKDCLUB_EXAMLIST_DATEEXAM', 'date_exam', $listDirn, $listOrder); ?>
            </th>
            <th width="" class="center"><?php echo JText::_('COM_TKDCLUB_EXAMLIST_CITY'); ?></th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_EXAMLIST_TYPE'); ?></th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_EXAMLIST_EXAMINER_NAME'); ?></th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_EXAMLIST_EXAMINER_ADRESS'); ?></th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_EXAMLIST_EXAMINER_EMAIL'); ?></th>
            <th width=""><?php echo JText::_('COM_TKDCLUB_EXAMLIST_EXAM_STATE'); ?></th>
            
         </tr>
    </thead>
    <!--Ende Tabellenkopf-->
    
    <!--Beginn Fußzeile-->
    <tfoot><tr><td colspan="18">
                <?php echo $this->pagination->getListFooter(); ?>
            </td></tr>
    </tfoot>
    <!--Ende Fußzeile-->
    
    <!--Start Tabellenkörper-->
    <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
        
        <tr class="row<?php echo $i % 2; ?>">
            <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
            <td class="center" width=""><?php echo (int) $item->id; ?></td>
            <td class="title center" width="">
            <?php
            $mylink = JRoute::_("index.php?option=com_tkdclub&task=exam.edit&id=".$item->id);
            echo '<a href="'.$mylink.'">'.JHtml::_('date', $item->date_exam, JText::_('DATE_FORMAT_LC4')).'</a>';
            ?>
            </td>
            <td width="" class="center"><?php echo $this->escape($item->city_exam); ?></td>
            <td width="" class="center"><?php $type = $this->escape($item->exam_type);
                    if ($type == 1){
                        echo JText::_('COM_TKDCLUB_EXAMLIST_SELECT_KUP');
                    } elseif ($type == 2) {
                        echo JText::_('COM_TKDCLUB_EXAMLIST_SELECT_DAN');   
                }
            ?></td>
            <td width=""><?php echo $this->escape($item->examiner_name); ?></td>
            <td width=""><?php echo $this->escape($item->examiner_adress); ?></td>
            <td width="" class="center"><?php echo $this->escape($item->examiner_email); ?></td>
            
            <td width="" class="center">
                <?php  
                    //hier wird das Array für die alternativen Tips der Publish-Spalte angelegt und danach in der JHtmlJGrid Methode verwendet
                    $states = array(1 => array('archive', 'COM_TKDCLUB_EXAMS_ACTIVE', 'COM_TKDCLUB_TOOLBAR_EXAM_ARCHIVE', 'JPUBLISHED', false, 'publish', 'publish'),
                                    2 => array('publish', 'COM_TKDCLUB_EXAMS_ARCHIVED', 'COM_TKDCLUB_TOOLBAR_EXAM_PUBLISH', 'JARCHIVED', false, 'archive', 'archive'),
                                    0 => array('publish', 'COM_TKDCLUB_UNPAID', 'COM_TKDCLUB_PAY_ITEM', 'JUNPUBLISHED', false, 'unpublish', 'unpublish'));
                    
                    echo JHtml::_('jgrid.state', $states, $item->published, $i, 'exams.', TRUE);
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <!--Ende Tabellenkörper-->
    
</table>
<!--Ende der Tabelle-->
    <div>
       <input type="hidden" name="task" value="" />
       <input type="hidden" name="boxchecked" value="0" />
       <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
       <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
       <?php echo JHtml::_('form.token'); ?>   
    </div>
        </div>
</form>