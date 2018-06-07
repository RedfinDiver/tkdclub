<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::stylesheet('administrator/components/com_tkdclub/assets/css/tkdclub.css');
$this->document->addScript('https://www.gstatic.com/charts/loader.js');

JHtml::_('behavior.tooltip');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/statistics.js');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/membercharts.js');
JHtml::_('script', 'administrator/components/com_tkdclub/assets/js/trainingcharts.js');
?>

<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>

<div id="tkdclub-loader" class="tkdclub-loader"></div>

<form enctype="multipart/form-data" action="index.php" method="post" id="adminForm" class="form-horizontal">
    <div id="tkdclub-chartcontainer" class="hidden">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'memberstats')); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'memberstats', JText::_('COM_TKDCLUB_SIDEBAR_MEMBERS', true)); ?>
                
                <div class="row-fluid">
                    <div class="span4 tkd-club-chart">
                        <div id="chart_state"></div>
                    </div>
                    <div class="span4 tkd-club-chart">
                        <div id="chart_genderdist"></div>
                    </div>
                    <div class="span4 tkd-club-chart">
                        <div id="chart_agedist"></div>
                    </div>
                </div>
                
                <div class="row-fluid">
                    <div class="span4">
                        <div id="tkdclub-members-active" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE') . ':'; ?>
                        </div>
                        <div id="tkdclub-members-support" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER') . ':';?>
                        </div>
                        <div id="tkdclub-members-inactive" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE') . ':';?>
                        </div>
                        <div id="tkdclub-members-allrows" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_ALL') . ':';?>
                        </div>
                    </div>
                    <div class="span4">
                        <div id="tkdclub-members-female" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') . ':'; ?>
                        </div>
                        <div id="tkdclub-members-male" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_MEMBER_SEX_MALE') . ':'; ?>
                        </div>
                    </div>
                    <div class="span4">
                        <div id="tkdclub-members-average" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_AVERAGE_AGE') . ':'; ?>
                        </div>
                        <div id="tkdclub-members-oldest" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_OLDEST_ACTIVE') . ':'; ?>
                        </div>
                        <div id="tkdclub-members-youngest" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_YOUNGEST_ACTIVE') . ':'; ?>
                        </div>
                    </div>

                </div>                                  
            <?php echo JHtml::_('bootstrap.endTab'); ?>
                
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'trainingsstats', JText::_('COM_TKDCLUB_SIDEBAR_TRAININGS', true)); ?>
                
                <div class="row-fluid">
                    <div class="span4 tkd-club-chart">
                        <div id="chart_trainingstypes"></div>
                    </div>
                    <div class="span4 tkd-club-chart">
                        <div id="chart_trainingyears"></div>
                    </div>
                    <div class="span4 tkd-club-chart">
                        <div id="chart_participants"></div>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <div id="tkdclub-unpaidtrainings" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS') . ':';?>
                        </div>
                        <div id="tkdclub-unpaidsum" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_UNPAID_SUM') . ':';?>
                        </div>
                    </div>
                    <div class="span4">
                        <div id="tkdclub-alltrainings" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_ALL_TRAININGS') . ':';?>
                        </div>
                    </div>
                    <div class="span4">
                        <div id="tkdclub-averageparts" class="tkdclub-statistics">
                            <?php echo JText::_('COM_TKDCLUB_STATISTIC_AVERAGE_PARTS') . ':';?>
                        </div>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class=span12" id="trainings-table">
                </div>
            
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
</form>