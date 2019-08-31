/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Namespace
Tkdclub = {};

// Load google charts
google.charts.load("current", { "packages": ["corechart"] });

// Function to get all data with AJAX syncro call
Tkdclub.getData = function (controller, task, varname = controller) {
    var url = 'index.php?option=com_tkdclub&task='
        + controller + '.' + task
        + '&' + Joomla.optionsStorage['csrf.token'] + '=1';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, false);

    xhr.send(null);
    if (xhr.status === 200) {
        var data = JSON.parse(xhr.responseText);
        Object.defineProperty(Tkdclub, varname, { value: data });
    }
}
/**
 * Displays the Membercharts
 */
Tkdclub.displayMembercharts = function () {

    var memberdiv = document.querySelector('#memberdata');
    var loader = document.querySelector('#memberdata-loader');

    if (Tkdclub.memberdata) {
        google.charts.setOnLoadCallback(Tkdclub.drawStateChart());
        google.charts.setOnLoadCallback(Tkdclub.drawGenderChart());
        google.charts.setOnLoadCallback(Tkdclub.drawAgeChart());
        Tkdclub.writeMembers();
    } else {
        while (memberdiv.firstChild) {
            memberdiv.removeChild(memberdiv.firstChild);
        }
        memberdiv.innerHTML = '<div class="alert alert-no-items">' + Joomla.JText._('JGLOBAL_NO_MATCHING_RESULTS') + '</div>';
    }

    loader.classList.remove('tkdclub-loader');
    memberdiv.classList.remove('hidden');
}

/**
 * Displays the trainingsdata
 */
Tkdclub.displayTrainingscharts = function () {

    var trainingsdiv = document.querySelector('#trainingsdata');
    var loader = document.querySelector('#trainingsdata-loader');

    if (Tkdclub.trainingsdata) {
        google.charts.setOnLoadCallback(Tkdclub.drawTrainingsDistChart());
        google.charts.setOnLoadCallback(Tkdclub.drawTrainingYearsChart());
        google.charts.setOnLoadCallback(Tkdclub.drawParticipantsChart());
        Tkdclub.writeTrainings();
    } else {
        while (trainingsdiv.firstChild) {
            trainingsdiv.removeChild(trainingsdiv.firstChild);
        }
        trainingsdiv.innerHTML = '<div class="alert alert-no-items">' + Joomla.JText._('JGLOBAL_NO_MATCHING_RESULTS') + '</div>';
    }

    loader.classList.remove('tkdclub-loader');
    trainingsdiv.classList.remove('hidden');
}

/**
 * Displays the trainer-table
 */
Tkdclub.displayTrainertable = function () {
    Tkdclub.writeTrainerTable();
}

/**
 * Writes data to DOM
 */
window.onload = function () {
    // Get all the data and store it in globale variable
    Tkdclub.getData('members', 'getmemberdata', 'memberdata');
    Tkdclub.getData('trainings', 'gettrainerdata', 'trainerdata');
    Tkdclub.getData('trainings', 'gettrainingsdata', 'trainingsdata');

    Tkdclub.displayMembercharts();
    Tkdclub.displayTrainingscharts();
    Tkdclub.displayTrainertable();
}




