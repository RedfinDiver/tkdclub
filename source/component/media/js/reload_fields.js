/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

/**
 * This function loads fields for either single or multiple subscriptions in the frontend.
 * It is fired, when the multiple-field ist changed.
 */
function reload_fields() {

    // What is selected
    var selection = document.getElementById("jform_group").value;

    // Build the url for request
    var url = 'index.php?option=com_tkdclub&task=participant.execute&format=json&selection=' + selection;
    
    // Trigger request
    var request = new XMLHttpRequest();
    request.open('GET', url);
    request.responseType = 'json';
    request.send();
    request.onload = function() {
        
        var div = document.getElementById('single_multiple');
        var new_inner = request.response.data.response;

        // Empty the div with the input field markup
        if (div.childElementCount > 0) {

            while (div.hasChildNodes()) {
                div.removeChild(div.firstChild);
            }

        }

        // Now fill the div with the new markup
        div.innerHTML = new_inner;

        // Restore popovers on label
        jQuery("#jform_kupgradesachieve-lbl").popover({trigger: 'hover'});
        jQuery("#jform_grade-lbl").popover({trigger: 'hover'});
        jQuery("#jform_age-lbl").popover({trigger: 'hover'});
        jQuery("#jform_participants-lbl").popover({trigger: 'hover'});
        jQuery("#jform_age_dist-lbl").popover({trigger: 'hover'});
        jQuery("#jform_grade_dist-lbl").popover({trigger: 'hover'});

        // Restore the pretty style of select-boxes
        jQuery("#jform_grade").chosen();
        jQuery("#jform_kupgradesachieve").chosen();
    }
}