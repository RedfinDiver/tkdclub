/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Creating message container
var message = {};

/**
 * this function gets the data for a candidate 
 */
function getcandidatedata () {

    // Delete old message-boxes if any
    deleteMsgBoxes();
    
    // Initialising some variables
    var waittext = document.getElementById('waittext').innerHTML;
    var errortext = document.getElementById('errortext').innerHTML;
    var gradeform = document.getElementById('jform_grade');
    var lastpromotionform = document.getElementById('jform_lastpromotion');
    var grade_achieveform = document.getElementById('jform_grade_achieve');
    var notes_form = document.getElementById('jform_notes');
    var tslpromotion = document.getElementById('jform_tslpromotion');

    // Getting promotion and candidate id
    var promotion_id = document.getElementById('jform_id_promotion').selectedOptions[0].value;
    var candidate_id = document.getElementById('jform_id_candidate').value;

    // Displaying some text during data fetch
    gradeform.value = waittext;
    lastpromotionform.value = waittext;
    grade_achieveform.value = waittext;
    tslpromotion.value = waittext;

    // Building URL for request
    var url = 'index.php?option=com_tkdclub&task=candidate.getajaxdata'
                + '&candidate_id=' + candidate_id 
                + '&promotion_id=' + promotion_id ;
    
    // Trigger request
    var request = new XMLHttpRequest();
    request.open('GET', url);
    request.responseType = 'json';
    request.send();
    request.onload = function() {
        
        var responseText = request.response;
        
        // checking response for error messages, render if any
        if (responseText['no_error'] == true ) {
            
            // no errors, response goes to form
            gradeform.value = responseText['grade'];
            lastpromotionform.value = responseText['lastpromotion'];
            grade_achieveform.value = responseText['grade_achieve'];
            tslpromotion.value = responseText['sLastPromotion'];
            
            if (responseText['notes']) {
                notes_form.value = responseText['notes'];
            }
            
            // show save buttons again
            handleSaveButtons('show');
            
        } else {
            
            // we have some errors, write them to message var an render them
            checkErrors(responseText);
            Joomla.renderMessages(message);

            // Fill in errortext in form
            gradeform.value = errortext;
            lastpromotionform.value = errortext;
            grade_achieveform.value = errortext;
            
            // Do not show save buttons, we don not want wrong data been saved
            handleSaveButtons('hide');
        }
    }
}

/**
 * with this function the response is analysed for errors
 * and the messages - arrays are created
 * 
 * @param {*} responseText 
 */
function checkErrors(responseText) {
    
    // object for determine the type of message
    var error_types = {
        error_candidate : 'error',
        error_promotion_id : 'error',
        error_birthdate : 'error',
        error_lastpromotion : 'error',
        error_grade : 'error',
        error_entry : 'error',
        error_minage : 'error',
        error_waitingtime : 'error',
        error : 'error',
        error_already_double : 'error',
        second_promotion : 'error',
        error_type : 'error',
        edit_link : 'info'
    };

    // getting the error-keys
    var x = Object.keys(responseText);

    // determine error type for each error and set message
    x.forEach(function(element) {
        if (error_types.hasOwnProperty(element)) {
            
            if (!message[error_types[element]]) {
                message[error_types[element]] = [];
            }
            
            message[error_types[element]].push(responseText[element]);
        }
    });
}

/**
 * This function checks for old messages and deletes them
 */
function deleteMsgBoxes() {

    // deleting message boxes if any
    var message_container = document.getElementById('system-message-container');
    
    if (message_container.childElementCount > 0) {
        
        while (message_container.hasChildNodes()) {
            message_container.removeChild(message_container.firstChild);
        };

        // Empty message object
        message = {};
    };
}

/**
 * Adds or removes class from toolbar buttons
 * @param {*} action 
 */
function handleSaveButtons(action) {

    // Getting toolbar buttons
    apply_button = document.getElementById('toolbar-apply');
    save_button = document.getElementById('toolbar-save');
    savenew_button = document.getElementById('toolbar-save-new');
    class_name = 'tkdclub-toolbar-hide';

    if (action == 'hide') {
        
        apply_button.classList.add(class_name);
        save_button.classList.add(class_name);
        savenew_button.classList.add(class_name);
    }

    if (action == 'show') {
        
        apply_button.classList.remove(class_name);
        save_button.classList.remove(class_name);
        savenew_button.classList.remove(class_name);

    }
}