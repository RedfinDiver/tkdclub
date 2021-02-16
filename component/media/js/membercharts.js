/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * MEMBERSTATE chart
 */
Tkdclub.drawStateChart = function () {
    var data = new google.visualization.arrayToDataTable([
        [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_ACTIVE'), Tkdclub.memberdata.active],
        [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_INACTIVE'), Tkdclub.memberdata.inactive],
        [Joomla.JText._('COM_TKDCLUB_MEMBER_STATE_SUPPORTER'), Tkdclub.memberdata.support]
    ],
        true
    );

    var options = {
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_STATE_ALL_MEMBERS_IN_DB'),
        titleTextStyle: { fontSize: 12, color: '#333' },
        "colors": ["#3366CC", "#ec8f6e", "#F9A541"],
        slices: { 1: { offset: 0.15 } },
        chartArea: {
            left: 30,
            top: 50
        },
    };

    var chart = new google.visualization.PieChart(document.getElementById("chart_state"));
    chart.draw(data, options);
}

/**
 * GENDER DISTRIBUTION chart
 */
Tkdclub.drawGenderChart = function () {
    var data = new google.visualization.arrayToDataTable([
        [Joomla.JText._('COM_TKDCLUB_MEMBER_SEX_FEMALE'), Tkdclub.memberdata.genderdist.female],
        [Joomla.JText._('COM_TKDCLUB_MEMBER_SEX_MALE'), Tkdclub.memberdata.genderdist.male]
    ]
        , true
    );

    var options = {
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_GENDER_DIST'),
        titleTextStyle: { fontSize: 12, color: '#333' },
        "colors": ["#ec8f6e", "#3366CC"],
        chartArea: {
            left: 30,
            top: 50
        }
    };

    var chart = new google.visualization.PieChart(document.getElementById("chart_genderdist"));
    chart.draw(data, options);
}

/**
 * AGE DISTRIBUTION chart
 */
Tkdclub.drawAgeChart = function () {
    var preparedData = [];

    for (var cat in Tkdclub.memberdata.agedist) {
        if (Tkdclub.memberdata.agedist[cat] > 0) {
            preparedData.push([cat, Tkdclub.memberdata.agedist[cat]]);
        }
    }
    var data = new google.visualization.arrayToDataTable(preparedData, true);

    var options = {
        title: Joomla.JText._('COM_TKDCLUB_STATISTIC_AGE_DIST'),
        titleTextStyle: { fontSize: 12, color: '#333' },
        chartArea: {
            left: 30,
            top: 50
        },
        legend: {
            position: 'none'
        }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chart_agedist"));
    chart.draw(data, options);
}

/**
 * Writes the memberdata to DOM
 */
Tkdclub.writeMembers = function () {

    document.getElementById('tkdclub-members-allrows').textContent += ' ' + Tkdclub.memberdata.allrows;
    document.getElementById('tkdclub-members-active').textContent += ' ' + Tkdclub.memberdata.active;
    document.getElementById('tkdclub-members-inactive').textContent += ' ' + Tkdclub.memberdata.inactive;
    document.getElementById('tkdclub-members-support').textContent += ' ' + Tkdclub.memberdata.support;

    document.getElementById('tkdclub-members-female').textContent += ' ' + Tkdclub.memberdata.genderdist.female;
    document.getElementById('tkdclub-members-male').textContent += ' ' + Tkdclub.memberdata.genderdist.male;

    document.getElementById('tkdclub-members-average').textContent += ' ' + Tkdclub.memberdata.average_age;
    var oldest_text = ' ' + Tkdclub.memberdata.oldest.name + ' (' + Tkdclub.memberdata.oldest.age_y + ')';
    document.getElementById('tkdclub-members-oldest').textContent += oldest_text;
    var youngest_text = ' ' + Tkdclub.memberdata.youngest.name + ' (' + Tkdclub.memberdata.youngest.age_y + ')';
    document.getElementById('tkdclub-members-youngest').textContent += youngest_text;
}
