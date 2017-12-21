/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

// define a bunch of colors for different training types
var colorsTypes = ["#ff6666", "#4d4dff", "#00cc00", "#ffff4d", "#cc00cc"]

/**
 * Prepares and drawes the TRAININGS DISTRIBUTION chart
 */
google.charts.setOnLoadCallback(drawTrainingsDistChart);
function drawTrainingsDistChart () {
    var preparedData = [];
    var types = Tkdclub.trainingsdata.sums.types;
    for (var type in types) {

            preparedData.push([type, types[type]]);

    }
    var data = new google.visualization.arrayToDataTable(preparedData, true);

    var options = {
        title : Joomla.JText._('COM_TKDCLUB_STATISTIC_TRAINTYPES_DIST'),
        titleTextStyle : { fontSize : 10, color : '#333' },  
        "colors": colorsTypes,
        slices : { 2 : {offset: 0.1} },
        chartArea :  {
            left : 30,
            top : 50
        },
        legend : {position : 'right', alignment : 'center'}
    };

    var chart = new google.visualization.PieChart(document.getElementById("chart_trainingstypes"));
    chart.draw(data, options);
}

/**
 * Prepares and draws the TRAININGNUMBERS chart
 */
google.charts.setOnLoadCallback(drawTrainingYearsChart);
function drawTrainingYearsChart() {

    var data = new google.visualization.arrayToDataTable(Tkdclub.trainingsdata.TrainingYearsChart, false);

    var options = {
        isStacked:true,
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_TRAININGS_PER_YEAR'),
        titleTextStyle : { fontSize : 10, color : '#333' },  
        "colors": colorsTypes,
        chartArea :  {
            left : 40,
            top : 50
        },
        legend : {position : 'right', alignment : 'center'}
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chart_trainingyears"));
    chart.draw(data, options);
}

/**
 * Prepares and draws the PARTICIPANTS PER TRAINING/YEAR chart
 */
google.charts.setOnLoadCallback(drawParticipantsChart);
function drawParticipantsChart() {

    var data = new google.visualization.arrayToDataTable(Tkdclub.trainingsdata.ParticipantsYearChart, false);

    var options = {
        isStacked:false,
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_AVERAGE_PARTICIPANTS'),
        titleTextStyle : { fontSize : 10, color : '#333' },  
        "colors": colorsTypes,
        chartArea :  {
            left : 40,
            top : 50
        },
        legend : {position : 'right', alignment : 'center'}
    };

    var chart = new google.visualization.LineChart(document.getElementById("chart_participants"));
    chart.draw(data, options);
}

/**
 * Write Trainingsdata to DOM
 */
function writeTrainings() {
    var unpaid = 0;
    var unpaid_sum = 0;
    var currency = Tkdclub.trainingsdata.currency;
    var trainerdata = Tkdclub.trainerdata;
    var l = trainerdata.length;
    for (i=0; i<l; i++) {
        unpaid += trainerdata[i]['sums']['unpaid'];
        unpaid_sum += trainerdata[i]['sums']['unpaid_sum'];
    }

    document.getElementById('tkdclub-unpaidtrainings').textContent += ' ' + unpaid;
    document.getElementById('tkdclub-unpaidsum').textContent += ' ' + unpaid_sum + ' ' + currency;
    document.getElementById('tkdclub-alltrainings').textContent += ' ' + Tkdclub.trainingsdata.sums['trainings'];
    document.getElementById('tkdclub-averageparts').textContent += ' ' + Tkdclub.trainingsdata.sums['average'];
}

