/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Namespace
Tkdclub = {};

// Load google charts
google.charts.load("current", {"packages":["corechart"]});

// Function to get all data with AJAX syncro call
Tkdclub.getData = function(controller, task, varname = controller) {
    var url = 'index.php?option=com_tkdclub&task='
              + controller + '.' + task
              + '&' + Joomla.optionsStorage['csrf.token'] + '=1';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, false);

    xhr.send(null);
    if (xhr.status === 200) {
        var data = JSON.parse(xhr.responseText);
        Object.defineProperty(Tkdclub, varname, {value : data});
    }
}

/**
 * Writes data to DOM
 */
window.onload = function () {

    // Get all the data and store it in globale variable
    Tkdclub.getData('members', 'getmemberdata', 'memberdata');
    Tkdclub.getData('trainings', 'gettrainerdata', 'trainerdata');
    Tkdclub.getData('trainings', 'gettrainingsdata', 'trainingsdata');

    // Write data to Dom
    writeMembers();
    writeTrainings();

    // No show the container and remove loader
    chartcontainer = document.getElementById('tkdclub-chartcontainer');
    chartcontainer.classList.remove('hidden');
    loader = document.getElementById('tkdclub-loader');
    loader.classList.remove('tkdclub-loader');

   writeTrainingsTable();
}




