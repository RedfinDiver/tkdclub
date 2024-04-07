<?php

/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_tkdclub.gstatic')
    ->useScript('com_tkdclub.statistics')
    ->useScript('com_tkdclub.membercharts')
    ->useScript('com_tkdclub.trainingcharts')
    ->useStyle('com_tkdclub.tkdclub-admin');

?>

<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'memberstats')); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'memberstats', Text::_('COM_TKDCLUB_SIDEBAR_MEMBERS', true)); ?>
        <div id="memberdata-loader" class="tkdclub-loader"></div>
        <div id="memberdata">
            <div class="row">
                    <div class="col-lg-4">                      
                        <div id="chart_state"></div>
                    </div>
                    <div class="col-lg-4">
                        <div id="chart_genderdist"></div>
                    </div>
                    <div class="col-lg-4">
                        <div id="chart_agedist"></div>
                    </div>
            </div>
            <div class="row stats-member-row2">
                <div class="col-lg-4">
                    <div id="tkdclub-members-active" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-support" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-inactive" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-allrows" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_ALL') . ':'; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div id="tkdclub-members-female" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-male" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_MEMBER_SEX_MALE') . ':'; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div id="tkdclub-members-average" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_AVERAGE_AGE') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-oldest" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_OLDEST_ACTIVE') . ':'; ?>
                    </div>
                    <div id="tkdclub-members-youngest" class="tkdclub-statistics">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_YOUNGEST_ACTIVE') . ':'; ?>
                    </div>
                </div>
            </div>
        </div>
       
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'trainingsstats', Text::_('COM_TKDCLUB_SIDEBAR_TRAININGS', true)); ?>
        <div id="trainingsdata-loader" class="tkdclub-loader"></div>
        <div id="trainingsdata">
            <div class="row">
                <div class="col-lg-4">
                    <div id="chart_trainingstypes"></div>
                </div>
                <div class="col-lg-4">
                    <div id="chart_trainingyears"></div>
                </div>
                <div class="col-lg-4">
                    <div id="chart_participants"></div>
                </div>
            </div>
            <div class="row stats-member-row2">
                <div class="col-lg-4">
                    <div id="tkdclub-unpaidtrainings">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS') . ':'; ?>
                    </div>
                    <div id="tkdclub-unpaidsum">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_UNPAID_SUM') . ':'; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div id="tkdclub-alltrainings">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_ALL_TRAININGS') . ':'; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div id="tkdclub-averageparts">
                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_AVERAGE_PARTS') . ':'; ?>
                    </div>
                </div>
            </div>
            <div class="row stats-member-row2">
                <div class=col-lg-12" id="trainings-table"></div>
            </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
