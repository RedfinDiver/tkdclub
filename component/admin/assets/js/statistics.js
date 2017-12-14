/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

// Namespace
Tkdclub = {};

// Load google charts
google.charts.load("current", {"packages":["corechart"]});

// function to get all data with AJAX
Tkdclub.getData = function(controller, task, varname = controller) {
    var url = 'index.php?option=com_tkdclub&task=' + controller + '.' + task;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, false);

    xhr.send(null);
    if (xhr.status === 200) {
        var data = JSON.parse(xhr.responseText);
        Object.defineProperty(Tkdclub, varname, {value : data});
        }
}

// Get all the data and store it in globale variable
Tkdclub.getData('members', 'getmemberdata', 'memberdata');



