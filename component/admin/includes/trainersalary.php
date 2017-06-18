<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
 
//init variables
$sum = 0;
$sum_text = '';

//Berechne die Summe für alle Trainer und Assistententätigkeit wenn kein Filter gesetzt ist 
if (empty($filter_trainer) && empty($filter_type) && empty($filter_year) && $listLimit === 0)
{
    foreach ($this->items as $it)
    {   
        $ts = TkdClubModelTrainings::getTrainerSalary($this->escape($it->km_trainer));
        
        //salary as trainer
        if (!$it->payment_state)
        {
            $sum = $ts + $sum;
        }
        
        //salary as assistent 1
        if ($it->assist1 != "" && !$it->payment_state)
        {
            $as1 = TkdClubModelTrainings::getAssistentSalary($this->escape($it->km_assist1));
            $sum = $as1 + $sum;
        }

        //salary as assistent 2
        if ($it->assist2 != "" && !$it->payment_state)
        {
            $as2 = TkdClubModelTrainings::getAssistentSalary($this->escape($it->km_assist2));
            $sum = $as2 + $sum;
        }                                                    

        //salary as assistent 3
        if ($it->assist3 != ""  && !$it->payment_state)
        {
            $as3 = TkdClubModelTrainings::getAssistentSalary($this->escape($it->km_assist3));
            $sum = $as3 + $sum;
        }                                                     

    } 
    
    $sum_text = 'Entschädigung aller ' . 'nicht abgerechneten Trainings (Trainer und Assistenten): '.'<b>'. \number_format($sum, 2, ',', ' ') .' '. $currency .'</b>'. '</br>';

}

//Berechnung für Trainer und Assistent wenn Filter Trainer/Assistent gesetzt ist und es kein Listenlimit gibt             
elseif (!empty($filter_trainer) && $listLimit === 0)
{
    //init variables
    $summe_trainer = 0;
    $summe_assist = 0;
    
    //zuerst die Trainerentschädigung berechnen
    foreach ($this->items as $it)
    {      
        $ts = TkdClubModelTrainings::getTrainerSalary($this->escape($it->km_trainer));
            
        if (!$it->payment_state && $it->trainer === $filter_trainer)
        {
            $summe_trainer = $summe_trainer + $ts;
        }
    }
    
    //nun die Assistentenentschädigung hinzufügen     
    foreach ($this->items as $it)
    {     
        if ($it->assist1 === $filter_trainer)
        {
            $assistent_selected = $it->km_assist1;
        } 
        elseif ($it->assist2 === $filter_trainer)
        {
            $assistent_selected = $it->km_assist2;
        }
        elseif ($it->assist3 === $filter_trainer)
        {
            $assistent_selected = $it->km_assist3; 
        }
        else
        {
            $assistent_selected = '';
        }
        
        if ($assistent_selected != '' && !$it->payment_state )
        {
            $as = TkdClubModelTrainings::getAssistentSalary($assistent_selected);
            $summe_assist = $summe_assist + $as;
        }

    }
    
    $sum = $summe_trainer + $summe_assist;          
    $sum_text = 'Summe aller nicht abgerechneten Trainings als Trainer UND Assistent für '.$assist_name.': '.'<b>'. \number_format($sum, 2, ',', ' ') .' '. $currency . '</b>'. '</br>';
    
}

//Den User hinweisen daß das Listenlimit auf 0 gesetzt werden soll um die Berechnung zu erhalten
else
{
    $sum_text = '<i>Zur Anzeige der nicht abgerechneten Summen bitte oben rechts "Alle" auswählen, um alle Datensätze auszuwerten</i><br />';
}                         
        
        /*Anzeige der unbezahlten Training nur wenn alle Trainings oder die unbezahlten Trainings ausgewählt sind
         * sowie kein Jahresfilter und Trainingstyp-Filter aktiv ist
         */
        if ($this->state->get('filter.payment_state') == '0' 
            || $this->state->get('filter.payment_state') == '' 
            && $this->state->get('filter.year') == ''
            && $this->state->get('filter.type') == ''
            && $this->state->get('filter.trainer') == '')
            
        {
            echo $sum_text;
        }
         
        /**
         * Berechnung der Durchschnittlichen Anwesenheit
         */
         
        //um Division durch 0 zu vermeiden
        if ($this->total > 0)
        {
            $trainings = $this->total;
        }
        else
        {
            $trainings = 1;
        }
        
        //Teilnehmervariable init
         $sum_parts = 0;
         
        //Texte zusammenbauen
        //wenn kein Filter in Type und Jahr gesetzt ist
        if (!$filter_type && !$filter_year)
        {
            $type = ' aller Trainings in der Datenbank';
        }
        
        //Wenn der Jahresfilter gesetzt und der Typfilter nicht gesetzt sind 
        elseif ($filter_year && !$filter_type)
        {
            $type = ' aller Trainings';
        }
        
        //alle anderen Fälle
        else
        {
            $type = ' pro '.$this->state->get('filter.type');
        }

        if (!$this->state->get('filter.year'))
        {
            $year = '';
        }
        else 
        {
            $year = ' in '. $this->state->get('filter.year');
        }
        
        //wenn kein Filter gesetzt ist, Summe der Teilnehmer aller Datensätze in der Datenbank holen
        if (!$filter_search && !$filter_payment_state && !$filter_trainer && !$filter_type && !$filter_year)
        {
            $sum_parts = $this->trainingspart;

        }
        else
        {
            foreach ($this->items as $tr)
            { 
                $sum_p = $this->escape($tr->participants);
                $sum_parts = $sum_parts + $sum_p;
            }
        }

        $average = $sum_parts / $trainings;

        if (!$this->state->get('filter.trainer') && $this->state->get('filter.payment_state') === '')
        {
          echo 'Mittlere Teilnehmerzahl' . $type . $year .': ' .'<b>' . \number_format($average, 1, ',', ' ') . ' Personen'. '</b>' .'</br>'; 
        }
         
    /**
    * Berechnung der Trainings aufgeschlüsselt nach Trainer, Jahr und Type
    */
         
         //wenn nur der Trainer ausgewählt ist
        if ($filter_trainer && !$filter_type && !$filter_year)
        {
            echo '<b>'. $trainer_name .'</b>'. '</br>';
            echo 'Insgesamt '.$this->total . ' Trainings von '. $trainer_name. ' in der Datenbank'. ', davon '.$this->trainings . ' als Trainer' .' und '. ($this->total - $this->trainings) .' als Assistent'.'</br>';
            echo $sum_text;
        }
        
        //wenn Trainer und Type ausgewählt ist
        if ($filter_trainer && $filter_type && !$filter_year) 
        {
            echo '<b>'. $trainer_name .'</b>'. '</br>';
            echo 'Insgesamt '.$this->total .'x '.$filter_type.' von '. $trainer_name. ' in der Datenbank'. ', davon '.$this->trainings . 'x als Trainer'.' und '. ($this->total - $this->trainings) .'x als Assistent'.'</br>';
        }
             
        //wenn Trainer und Jahr ausgewählt sind 
        
        if ($filter_trainer && !$filter_type && $filter_year)
        {
            echo '<b>'. $trainer_name .'</b>'. '</br>';
            echo 'Insgesamt '.$this->total .'x '.' Training in '.$filter_year.' von '. $trainer_name. ' in der Datenbank'. ', davon '.$this->trainings . 'x als Trainer'.' und '. ($this->total - $this->trainings) .'x als Assistent'.'</br>';
        }
        
        //wenn Trainer, Jahr und Type ausgewählt sind
        if ($filter_trainer && $filter_type && $filter_year) 
        {
            echo '<b>'. $trainer_name .'</b>'. '</br>';
            echo 'Insgesamt '.$this->total .'x '.$filter_type .' in '.$filter_year.' von '. $trainer_name. ' in der Datenbank'. ', davon '.$this->trainings . 'x als Trainer'.' und '. ($this->total - $this->trainings) .'x als Assistent'.'</br>';
        }
        
        //wenn kein Filter ausgewählt ist
        if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == '')
        {  
            echo 'Gesamtzahl der Trainings in der Datenbank: ' .'<b>'.$this->allrows .'</b>';
        }
        
        //wenn nur State ausgwählt ist
        if (!$filter_trainer && !$filter_type && !$filter_year && !$filter_search && $filter_payment_state == 1)
        { 
            echo 'Gesamtzahl der abgerechneten Trainings in der Datenbank: ' .'<b>'.$this->total .'</b>';
        }
         
?>