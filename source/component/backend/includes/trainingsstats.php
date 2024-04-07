<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

// TODO: Rework trainingsstats

/********************************************************************************
 * Calculation of unpaid trainings and getting the data for the selected trainer
 ********************************************************************************/

// Calculation of the sum of ALL unpaid Trainings and displaying if no filter is set 
if (!$filter_trainer && !$filter_type && !$filter_year)
{   
    $sum = 0;

    foreach ($this->trainerdata as $trainer)
    {
        $sum += $trainer->sums['unpaid_sum'];
    }
    
    if ($salaryparams == true)
    {
        echo Text::_('COM_TKDCLUB_TRAINING_NOT_PAID_TRAININGS') . \number_format($sum, 2, ',', ' ') .' '. $currency . '</br>';
    }
    else
    {
        echo Text::_('COM_TKDCLUB_TRAINING_SALARY_CALC_NOT_POSSIBLE') . '</br>';
    }
}

// Calculation of the sum of unpaid trainings for SELECTED TRAINER when trainer/assistent filter is set and preparing text            
if ($filter_trainer)
{    
    foreach ($this->trainerdata as $trainer)
    {
       if ($trainer->trainer_id == $filter_trainer)
       {
           $sum = $trainer->sums['unpaid_sum'];
           $trainer_data = $trainer; // this is the data for the selected trainer used later in the script
       }
    }

    // Text is used later in the script
    if ($salaryparams == true)
    {
        $sum_text_trainer = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID_TRAININGS_TRAINER').$trainer_data->trainer_name.': '. $sum . ' ' . $currency . '</br>';
    }
    else
    {
        $sum_text_trainer = Text::_('COM_TKDCLUB_TRAINING_SALARY_CALC_NOT_POSSIBLE');
    }
}
         
/***************************************************
 * Calculation of average participants
 ***************************************************/
    
// Texte zusammenbauen
// wenn kein Filter in Type und Jahr gesetzt ist
if (!$filter_type && !$filter_year)
{
    $type = ' aller Trainings in der Datenbank';
}

// Wenn der Jahresfilter gesetzt und der Typfilter nicht gesetzt sind 
elseif ($filter_year && !$filter_type)
{
    $type = ' aller Trainings';
}

// alle anderen Fälle
else
{
    $type = ' pro '.$this->state->get('filter.type');
}

// build year variable
!$this->state->get('filter.year') ? $year = '' : $year = ' in '. $this->state->get('filter.year');

//wenn kein Filter gesetzt ist, Summe der Teilnehmer aller Datensätze in der Datenbank holen
if (!$filter_search && !$filter_payment_state && !$filter_trainer && !$filter_type && !$filter_year)
{
    $average = $this->trainingsdata->sums['average'];
}

// wenn der Jahresfilter gesetzt ist
$filter_year ? $average = $this->trainingsdata->$filter_year['average'] : null;

// wenn der Trainingsttypfilter gesetzt ist
$filter_type ? $average = $this->trainingsdata->sums['parts'][$filter_type] : null;

if (!$filter_trainer && $filter_payment_state === '')
{
    echo 'Mittlere Teilnehmerzahl' . $type . $year .': ' . \number_format($average, 1, ',', ' ') . ' Personen ' .'</br>'; 
}
         
/**********************************************************************
* Calcultions of roles for trainers
***********************************************************************/
         
// wenn nur der Trainer ausgewählt ist
if ($filter_trainer && !$filter_type && !$filter_year)
{
    $as_trainer = $trainer_data->sums['trainer'];
    $as_assistent = $trainer_data->sums['assistent'];
    
    echo '<h4>' . $trainer_data->trainer_name . '</h4>';
    echo 'Insgesamt '.$this->total . ' Trainings von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '.$as_trainer . ' als Trainer' .' und '. $as_assistent .' als Assistent'.'</br>';
    echo $sum_text_trainer;

    if ($trainer_data->sums['unpaid'] > 0)
    {
        $url = Route::_( 'index.php?option=com_tkdclub&task=trainings.paytrainings&member_id=' . $trainer_data->trainer_id . '&name=' . $trainer_data->trainer_name . '&' . Session::getFormToken() .'=1');
        $button = '<a class="btn btn-success mt-3" target="_self" href="' . $url .'" ><span class="icon-publish"></span> alle Trainings als bezahlt speichern</a>';
        echo $button;
    }
}

// wenn Trainer und Type ausgewählt ist
if ($filter_trainer && $filter_type && !$filter_year)
{
    $as_trainer = $trainer_data->sums['types'][$filter_type]['trainer'];
    $as_assistent = $trainer_data->sums['types'][$filter_type]['assistent'];

    echo '<h4>'. $trainer_data->trainer_name .'</h4>';
    echo 'Insgesamt '.$this->total .'x '.$filter_type.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '.$as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}
    
// wenn Trainer und Jahr ausgewählt sind 
if ($filter_trainer && !$filter_type && $filter_year)
{
    $as_trainer = $trainer_data->$filter_year['trainer'];
    $as_assistent = $trainer_data->$filter_year['assistent'];

    echo '<h4>'. $trainer_data->trainer_name .'</h4>';
    echo 'Insgesamt '.$this->total .'x '.' Training in '.$filter_year.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '. $as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}

// wenn Trainer, Jahr und Type ausgewählt sind
if ($filter_trainer && $filter_type && $filter_year) 
{
    $as_trainer = $trainer_data->$filter_year['trainer'];
    $as_assistent = $trainer_data->$filter_year['assistent'];

    echo '<h4>'. $trainer_data->trainer_name .'</h4>';
    echo 'Insgesamt '.$this->total .'x '.$filter_type .' in '.$filter_year.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '. $as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}

// wenn kein Filter ausgewählt ist
if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == '')
{  
    echo 'Gesamtzahl der Trainings in der Datenbank: ' . $this->trainingsdata->sums['trainings'];
}

// wenn nur State ausgewählt ist
if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == 1)
{ 
    echo 'Gesamtzahl der abgerechneten Trainings in der Datenbank: ' .'<b>'.$this->total .'</b>';
}

?>