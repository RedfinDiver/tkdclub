<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$msg = '';
$trainer = null;

// No filter selected
if (!$filter_trainer && !$filter_type && !$filter_year) {
    if ($salaryparams == false) {
        $msg =  '<b>' . Text::_('COM_TKDCLUB_TRAINING_SALARY_CALC_NOT_POSSIBLE') . '</b>' . '</br>';
    } else {
        $sum = 0;
        foreach ($this->trainerdata as $trainer) {$sum += $trainer->sums['unpaid_sum'];}
    }
    if ($salaryparams == true && $sum > 0) {
        $msg = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID_TRAININGS') . '<b>' . \number_format($sum, 2, ',', ' ') .' '. $currency . '</b>' . '</br>';
        $unpaid_count = 0;
        foreach ($this->trainerdata as $trainer) {$unpaid_count += $trainer->sums['unpaid'];}
        $msg .= 'Anzahl nicht abgerechneter Trainings: ' . '<b>' . $unpaid_count . '</b>' . '</br>';
    } elseif ($salaryparams == true && $sum == 0) {
        $msg = Text::_('COM_TKDCLUB_TRAINING_ALL_PAID') . '</br>';
    }

    $msg .= Text::_('COM_TKDCLUB_TRAINING_IN_DATABASE') . '<b>'. $this->trainingsdata->sums['trainings'] . '</b>' . '</br>';
    $msg .= Text::_('COM_TKDCLUB_TRAINING_AVERAGE_PRESENCE') .'<b>' . \number_format($this->trainingsdata->sums['average'], 1, ',', ' ') . '</b>' . '</br>';
}

// Trainer is selected
if ($filter_trainer) {
    foreach ($this->trainerdata as $data) {
        $data->trainer_id == $this->state->get('filter.trainer') ? $trainer = $data : null;
    }
}

                


?>

<div class="m-2 row">
    <div class="card alert-info">
        
        <?php echo '<div class="card-body">' . $msg . '</div>'; ?>

    </div>
</div>