/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

/**
 * Writes data to DOM
 */
window.onload = function () {
    document.getElementById('tkdclub-members-allrows').innerText += ' ' + Tkdclub.memberdata.allrows;
    document.getElementById('tkdclub-members-active').innerText += ' ' + Tkdclub.memberdata.active;
    document.getElementById('tkdclub-members-inactive').innerText += ' ' + Tkdclub.memberdata.inactive;
    document.getElementById('tkdclub-members-support').innerText += ' ' + Tkdclub.memberdata.support;

    document.getElementById('tkdclub-members-female').innerText += ' ' + Tkdclub.memberdata.genderdist.female;
    document.getElementById('tkdclub-members-male').innerText += ' ' + Tkdclub.memberdata.genderdist.male;

    document.getElementById('tkdclub-members-average').innerText += ' ' + Tkdclub.memberdata.average_age;
    var oldest_text = ' ' + Tkdclub.memberdata.oldest.name + ' (' + Tkdclub.memberdata.oldest.age_y + ')';
    document.getElementById('tkdclub-members-oldest').innerText += oldest_text;
    var youngest_text = ' ' + Tkdclub.memberdata.youngest.name + ' (' + Tkdclub.memberdata.youngest.age_y + ')';
    document.getElementById('tkdclub-members-youngest').innerText += youngest_text;
}


/**
 * Prepares and drawes the MEMBERSTATE chart
 */
google.charts.setOnLoadCallback(drawStateChart);
function drawStateChart () {
    var data = new google.visualization.arrayToDataTable([
            [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_ACTIVE'), Tkdclub.memberdata.active],
            [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_INACTIVE'), Tkdclub.memberdata.inactive],
            [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_SUPPORTER'), Tkdclub.memberdata.support]
        ],
        true
    );

    var options = {
        legend : {
            alignment : 'center',
        },
        width : '30%',
        height : 180,
        title : Joomla.JText._('COM_TKDCLUB_STATISTICS_STATE_ALL_MEMBERS_IN_DB'),
        titleTextStyle : { fontSize : 12, color : '#333' },  
        "colors": ["#3366CC", "#ec8f6e", "#F9A541"],
        slices : { 2 : {offset: 0.1} }
    };

    var chart = new google.visualization.PieChart(document.getElementById("chart_state"));
    chart.draw(data, options);
}

/**
 * Prepares and draws the GENDER DISTRIBUTION chart
 */
google.charts.setOnLoadCallback(drawGenderChart);
function drawGenderChart() {
    var data = new google.visualization.arrayToDataTable([
        [Joomla.JText._('COM_TKDCLUB_MEMBER_SEX_FEMALE'), Tkdclub.memberdata.genderdist.female],
        [Joomla.JText._('COM_TKDCLUB_MEMBER_SEX_MALE'), Tkdclub.memberdata.genderdist.male]
    ]
    , true 
    );
    
    var options = {  
        legend: {alignment : "center"},
        width : '30%',
        height : 180,
        title : Joomla.JText._('COM_TKDCLUB_STATISTICS_GENDER_DIST'),
        titleTextStyle : { fontSize : 12, color : '#333' },
        "colors": ["#ec8f6e", "#3366CC"],
        slices: {2: {offset: 0.1} },
    };
    
        var chart = new google.visualization.PieChart(document.getElementById("chart_genderdist"));
        chart.draw(data, options);
}

/**
 * Prepares and draws the AGE DISTRIBUTION chart
 */
google.charts.setOnLoadCallback(drawAgeChart);
function drawAgeChart() {
    var preparedData = [];

    for (var cat in Tkdclub.memberdata.agedist) {
        if (Tkdclub.memberdata.agedist[cat] > 0) {
            preparedData.push([cat, Tkdclub.memberdata.agedist[cat]]);
        }
    }
    var data = new google.visualization.arrayToDataTable(preparedData, true);

    var options = {
        chartArea : {
            //left: 20,
            width: '95%'
        },
        width : '95%',
        height : 180,
        legend : {position: "none"},
        title: Joomla.JText._('COM_TKDCLUB_STATISTICS_AGE_DIST'),
        titleTextStyle : { fontSize : 12, color : '#333' }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chart_agedist"));
    chart.draw(data, options);
}
