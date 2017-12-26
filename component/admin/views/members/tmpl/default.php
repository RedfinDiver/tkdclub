<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/rawsubmitbutton.js');

/**
 * Get some variables
 */
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tkdclub&view=members'); ?>"
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

    <!-- Filterbar und Listensortierung -->
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
            include_once(JPATH_COMPONENT . '/includes/membersstats.php');
        ?>
    <?php endif; ?>
                
    <div class="clearfix"> </div>

        <table class="table table-striped table-condensed">
            <thead>
                <tr>         
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%">
                    <!-- for attachment icon -->
                    <i class="icon-attachment hasTooltip" title="<?php echo JHtml::tooltipText('COM_TKDCLUB_MEMBER_ATTACHMENTS'); ?>"></i> 
                    </th>
                    
                    <th width="1%">
                    <!-- for info- icon about infos in data-->
                    <i class="icon-info-2 hasTooltip" title="<?php echo JHtml::tooltipText('COM_TKDCLUB_MEMBER_COMMENTS'); ?>"></i>                 
                    </th>
    
                    <th width="1%"><?php echo JText::_('JSTATUS'); ?></th>
                    <th width="">
                        <?php echo JHtml::_('grid.sort', 'COM_TKDCLUB_MEMBER_LASTNAME', 'lastname', $listDirn, $listOrder); ?>
                    </th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_FIRSTNAME'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_BIRTHDATE'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_PHONE'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_EMAIL'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_PASS'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_GRADE'); ?></th>
                    <th width=""><?php echo JText::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION'); ?></th>

                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="18">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <?php foreach ($this->items as $i => $item) : 
					$canEdit    = $user->authorise('core.edit',       'com_tkdclub.member.' . $item->member_id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.member.' . $item->member_id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_tkdclub.member.' . $item->member_id) && $canCheckin;
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->member_id); ?></td>
                    
                    <!-- for attachment icon -->
                    <td class="center">
                        <i class="<?php if (!empty($item->attachments)){
                            echo 'icon-attachment';                  

                            }?> hasTooltip" title="<?php echo JHtml::tooltipText('COM_TKDCLUB_MEMBER_ATTACHMENTS_EXIST'); ?>">
                        </i>
                    </td>
                    
                    <!-- for info- icon about infos in data-->
                    <td class="center">
                        <i class="<?php if (!empty($item->notes_personel) || !empty($item->notes_taekwondo) || !empty($item->notes_clubdata)){
                            echo 'icon-info-2';                  

                            }?> hasTooltip" title="<?php echo JHtml::tooltipText('COM_TKDCLUB_MEMBER_COMMENTS_EXIST'); ?>">
                        </i>
                    </td>
                    <td class="center">
                        <?php 
                            //creating array for altnatives tooltip text on member_state column
                            $states = array('active' => array('COM_TKDCLUB_MEMBER_STATE_ACTIVE', 'publish'),
                                            'inactive' => array('COM_TKDCLUB_MEMBER_STATE_INACTIVE', 'unpublish'),
                                            'support' => array('COM_TKDCLUB_MEMBER_STATE_SUPPORTER','archive'));
                            $string_to_take = $states[$item->member_state]
                        ?>
                        <span class="btn btn-micro hasTooltip" data-original-title="<?php echo JText::_($string_to_take[0]) ?>">
                            <span class="icon-<?php echo $string_to_take[1] ?>"></span>
                        </span>
                    </td>
                    <td class="title">
                        <?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'members.', $canCheckin); ?>
						<?php endif; ?>
                        <?php
                            $mylink = JRoute::_("index.php?option=com_tkdclub&task=member.edit&member_id=" . $item->member_id);
                            echo '<a href="'.$mylink.'">'.$this->escape($item->lastname).'</a>';
                            if ($item->functions)
                            {
                                echo ' <span class="icon-star-empty hasTooltip" title="' .
                                JText::_('COM_TKDCLUB_MEMBER_FUNCTION_HINT') .
                                '"></span>';
                            }
                        ?>
                        
                    </td>
                    <td><?php echo $this->escape($item->firstname); ?></td>
                    <td><?php
                    if ($item->birthdate != '0000-00-00')
                        { 
                            echo JHtml::_('date', $item->birthdate, JText::_('DATE_FORMAT_LC4'));
                        } ?>
                    </td>
                    <td><?php echo $this->escape($item->phone); ?></td>
                    <td><?php echo $this->escape($item->email); ?></td>
                    <td><?php if ($item->memberpass != 0)
                            {
                                echo $this->escape($item->memberpass); 
                            } else {
                                JText::printf('COM_TKDCLUB_MEMBER_NO_PASS');
                            }?>
                    </td>
                    <td><?php
                                $grade = $this->escape($item->grade);
                                
                                if ($grade == '' || $grade == 0 )
                                    {echo JText::_('COM_TKDCLUB_NO_GRADE_LISTVIEW');} 

                                else  {echo $grade;}
                        ?>     
                    </td>
                    <td><?php
                        if ($item->lastpromotion != '0000-00-00')
                            { 
                            echo JHtml::_('date', $item->lastpromotion, JText::_('DATE_FORMAT_LC4'));
                            } ?>
                    </td>
                    
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            
    <?php endif; ?>  

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>

</form>