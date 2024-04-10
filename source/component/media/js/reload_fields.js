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
    var group = document.getElementById("jform_group").value;
    var itemId = document.getElementById("item_id").value;

    // Build the url for request
    var url = 'index.php?option=com_tkdclub&task=participant.reloadfields'
                + '&group=' + group
                + '&item_id=' + itemId;
    
    // Trigger request
    var request = new XMLHttpRequest();
    request.open('GET', url);
    request.responseType = '';
    request.send();
    request.onload = function() {
        
        var div = document.getElementById('participant_data');
        var new_inner = request.response;

        // Empty the div with the input field markup
        if (div.childElementCount > 0) {

            while (div.hasChildNodes()) {
                div.removeChild(div.firstChild);
            }

        }

        // Now fill the div with the new markup
        div.innerHTML = new_inner;
    }
}