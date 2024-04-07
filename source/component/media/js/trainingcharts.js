/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// define a bunch of colors for different training types
Tkdclub.colorsTypes = ["#ff6666", "#4d4dff", "#00cc00", "#ffff4d", "#cc00cc"];

/**
 * TRAININGS DISTRIBUTION chart
 */
Tkdclub.drawTrainingsDistChart = function () {
    var preparedData = [];
    var types = Tkdclub.trainingsdata.sums.types;
    for (var type in types) {

        preparedData.push([type, types[type]]);

    }
    var data = new google.visualization.arrayToDataTable(preparedData, true);

    var options = {
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_TRAINTYPES_DIST'),
        titleTextStyle: { fontSize: 10, color: '#333' },
        "colors": Tkdclub.colorsTypes,
        slices: { 2: { offset: 0.1 } },
        chartArea: {
            left: 30,
            top: 50
        },
        legend: { position: 'right', alignment: 'center' }
    };

    var chart = new google.visualization.PieChart(document.getElementById("chart_trainingstypes"));
    chart.draw(data, options);
}

/**
 * TRAININGNUMBERS chart
 */
Tkdclub.drawTrainingYearsChart = function () {

    var data = new google.visualization.arrayToDataTable(Tkdclub.trainingsdata.TrainingYearsChart, false);

    var options = {
        isStacked: true,
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_TRAININGS_PER_YEAR'),
        titleTextStyle: { fontSize: 10, color: '#333' },
        "colors": Tkdclub.colorsTypes,
        chartArea: {
            left: 40,
            top: 50
        },
        legend: { position: 'right', alignment: 'center' }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chart_trainingyears"));
    chart.draw(data, options);
}

/**
 * PARTICIPANTS PER TRAINING/YEAR chart
 */
Tkdclub.drawParticipantsChart = function () {

    var data = new google.visualization.arrayToDataTable(Tkdclub.trainingsdata.ParticipantsYearChart, false);

    var options = {
        isStacked: false,
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_AVERAGE_PARTICIPANTS'),
        titleTextStyle: { fontSize: 10, color: '#333' },
        "colors": Tkdclub.colorsTypes,
        chartArea: {
            left: 40,
            top: 50
        },
        legend: { position: 'right', alignment: 'center' }
    };

    var chart = new google.visualization.LineChart(document.getElementById("chart_participants"));
    chart.draw(data, options);
}

/**
 * Write Trainingsdata to DOM
 */
Tkdclub.writeTrainings = function () {
    var unpaid = 0;
    var unpaid_sum = 0;
    var currency = Tkdclub.trainingsdata.currency;
    var trainerdata = Tkdclub.trainerdata;
    var l = trainerdata.length;
    for (var i = 0; i < l; i++) {
        unpaid += trainerdata[i]['sums']['unpaid'];
        unpaid_sum += trainerdata[i]['sums']['unpaid_sum'];
    }

    document.getElementById('tkdclub-unpaidtrainings').textContent += ' ' + unpaid;
    document.getElementById('tkdclub-unpaidsum').textContent += ' ' + Math.round(unpaid_sum * 100) / 100 + ' ' + currency;
    document.getElementById('tkdclub-alltrainings').textContent += ' ' + Tkdclub.trainingsdata.sums['trainings'];
    document.getElementById('tkdclub-averageparts').textContent += ' ' + Tkdclub.trainingsdata.sums['average'];
}

/**
 * Writes the trainings table to DOM
 */
Tkdclub.writeTrainerTable = function () {
    Tkdclub.createTable('tkdclub-trainingstable', 'trainings-table');
    Tkdclub.addDataToTable('tkdclub-trainingstable', Tkdclub.trainerdata);
}

/**
 * Creates a table
 */
Tkdclub.createTable = function (id, where) {
    var table = '<table id="' + id + '" class="table table-striped"><thead>';
    table += '<tr><th></th>';
    table += '<th>' + Joomla.JText._('COM_TKDCLUB_SIDEBAR_TRAININGS') + '</th>';

    var types = Object.keys(Tkdclub.trainingsdata.sums.types);

    for (type of types) {
        table += '<th>' + type + '</th>';
    }

    table += '<th>' + Joomla.JText._('COM_TKDCLUB_TRAINING_NOT_PAID') + '</th>';
    table += '<th>' + Joomla.JText._('COM_TKDCLUB_SUM') + '</th>';
    table += '<th></th></tr></thead>';
    table += '<tbody></tbody>';
    table += '</table>';
    document.getElementById(where).innerHTML = table;
}

/**
 * Adds Data to table
 */
Tkdclub.addDataToTable = function (id, data) {

    // first get training types
    var types = Object.keys(Tkdclub.trainingsdata.sums.types);

    var rows = '';

    // Iterate through the data
    for (var trainer of data) {

        var trainer_types = trainer.sums.types;
        if (trainer.sex == 'male') {
            var asTrainer = Joomla.JText._('COM_TKDCLUB_STATISTIC_AS_TRAINER_MALE');
            var asAssistent = Joomla.JText._('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_MALE');
        } else {
            var asTrainer = Joomla.JText._('COM_TKDCLUB_STATISTIC_AS_TRAINER_FEMALE');
            var asAssistent = Joomla.JText._('COM_TKDCLUB_STATISTIC_AS_ASSISTENT_FEMALE');
        }
        var acomma = '';

        rows += '<tr>';
        rows += '<td><strong>' + trainer.trainer_name + '</strong></td>';
        rows += '<td><strong>' + trainer.sums.trainings + '</strong>';
        rows += '<div class="small">';
        if (trainer.sums.trainer > 0) {
            rows += trainer.sums.trainer + asTrainer;
            acomma = ', ';
        }
        if (trainer.sums.assistent > 0) {
            rows += acomma;
            rows += trainer.sums.assistent + asAssistent;
        }
        rows += '</div>'
        rows += '</td>'

        for (type of types) {
            if (trainer_types[type]) {
                var tcomma = '';
                rows += '<td><strong>' + trainer_types[type]['trainings'] + '</strong>';
                rows += '<div class="small">';
                if (trainer_types[type]['trainer'] > 0) {
                    rows += trainer_types[type]['trainer'] + asTrainer;
                    tcomma = ', '
                }
                if (trainer_types[type]['assistent'] > 0) {
                    rows += tcomma;
                    rows += trainer_types[type]['assistent'] + asAssistent;
                }

                rows += '</div>';
                rows += '</td>';
            }
            else {
                rows += '<td>0</td>';
            }
        }

        rows += '<td><strong>' + trainer.sums.unpaid + '</strong></td>';
        rows += '<td><strong>' + trainer.sums.unpaid_sum + '</strong></td>';

        if (trainer.sums.unpaid > 0) {
            var url_pay = 'index.php?option=com_tkdclub&view=statistics&task=trainings.paytrainings&id='
                + trainer.trainer_id
                + '&name=' + trainer.trainer_name
                + '&' + Joomla.optionsStorage['csrf.token'] + '=1';

            rows += '<td><a href="' + url_pay + '" class="btn btn-success btn-sm">';
            rows += '<span class=""></span>' + Joomla.JText._('COM_TKDCLUB_TRAINING_PAY') + '</a></td>';
        } else {
            rows += '<td></td>';
        }
        rows += '</tr>'
    }

    document.getElementById(id).getElementsByTagName("tbody")[0].innerHTML += rows;
}