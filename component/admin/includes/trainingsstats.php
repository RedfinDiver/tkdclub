<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

$start = microtime(true);
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
    
    echo JText::_('COM_TKDCLUB_TRAINING_NOT_PAID_TRAININGS').'<b>'. \number_format($sum, 2, ',', ' ') .' '. $currency .'</b>'. '</br>';
}

// Calculation of the sum of unpaid trainings for SELECTED TRAINER when trainer/assistent filter is set and preparing text            
if ($filter_trainer)
{    
    foreach ($this->trainerdata as $trainer)
    {
       if ($trainer->trainer_id == $filter_trainer)
       {
           $sum = $trainer->sums['unpaid_sum'];
           $trainer_data = $trainer; // this is the data for the selected trainer used latre in the script
       }
    }

    // text is used later in the script
    $sum_text_trainer = JText::_('COM_TKDCLUB_TRAINING_NOT_PAID_TRAININGS_TRAINER').$trainer_data->trainer_name.': '.'<b>'. $sum . ' ' . $currency . '</b>'. '</br>';
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
    echo 'Mittlere Teilnehmerzahl' . $type . $year .': ' .'<b>' . \number_format($average, 1, ',', ' ') . ' Personen'. '</b>' .'</br>'; 
}
         
/**********************************************************************
* Calcultions of roles for trainers
***********************************************************************/
         
// wenn nur der Trainer ausgewählt ist
if ($filter_trainer && !$filter_type && !$filter_year)
{
    $as_trainer = $trainer_data->sums['trainer'];
    $as_assistent = $trainer_data->sums['assistent'];

    echo '<b>'. $trainer_data->trainer_name .'</b>'. '</br>';
    echo 'Insgesamt '.$this->total . ' Trainings von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '.$as_trainer . ' als Trainer' .' und '. $as_assistent .' als Assistent'.'</br>';
    echo $sum_text_trainer;
}

// wenn Trainer und Type ausgewählt ist
if ($filter_trainer && $filter_type && !$filter_year)
{
    $as_trainer = $trainer_data->sums['types'][$filter_type]['trainer'];
    $as_assistent = $trainer_data->sums['types'][$filter_type]['assistent'];

    echo '<b>'. $trainer_data->trainer_name .'</b>'. '</br>';
    echo 'Insgesamt '.$this->total .'x '.$filter_type.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '.$as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}
    
// wenn Trainer und Jahr ausgewählt sind 
if ($filter_trainer && !$filter_type && $filter_year)
{
    $as_trainer = $trainer_data->$filter_year['trainer'];
    $as_assistent = $trainer_data->$filter_year['assistent'];

    echo '<b>'. $trainer_data->trainer_name .'</b>'. '</br>';
    echo 'Insgesamt '.$this->total .'x '.' Training in '.$filter_year.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '. $as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}

// wenn Trainer, Jahr und Type ausgewählt sind
if ($filter_trainer && $filter_type && $filter_year) 
{
    $as_trainer = $trainer_data->$filter_year['trainer'];
    $as_assistent = $trainer_data->$filter_year['assistent'];

    echo '<b>'. $trainer_data->trainer_name .'</b>'. '</br>';
    echo 'Insgesamt '.$this->total .'x '.$filter_type .' in '.$filter_year.' von '. $trainer_data->trainer_name. ' in der Datenbank'. ', davon '. $as_trainer . 'x als Trainer'.' und '. $as_assistent .'x als Assistent'.'</br>';
}

// wenn kein Filter ausgewählt ist
if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == '')
{  
    echo 'Gesamtzahl der Trainings in der Datenbank: ' .'<b>'.$this->trainingsdata->sums['trainings'] .'</b>';
}

// wenn nur State ausgwählt ist
if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == 1)
{ 
    echo 'Gesamtzahl der abgerechneten Trainings in der Datenbank: ' .'<b>'.$this->total .'</b>';
}

$end = microtime(true);
$diff = $end - $start;
$hold = 1;
?>