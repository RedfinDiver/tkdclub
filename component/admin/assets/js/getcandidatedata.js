var message = {};
/**
 * this function gets the data for a candidate 
 */
function getcandidatedata () {

    // deleting message boxes if any
    var message_container = document.getElementById('system-message-container');

    if (message_container.childElementCount > 0) {
        
        while (message_container.hasChildNodes()) {
            message_container.removeChild(message_container.firstChild);
        };

        message = {};
    };
    
    // initialising some variables
    var waittext = document.getElementById('waittext').innerHTML;
    var errortext = document.getElementById('errortext').innerHTML;
    var gradeform = document.getElementById('jform_grade');
    var lastpromotionform = document.getElementById('jform_lastpromotion');
    var grade_achieveform = document.getElementById('jform_grade_achieve');

    // getting promotion and candidate id
    var promotion_id = document.getElementById('jform_id_promotion').selectedOptions[0].value;
    var candidate_id = document.getElementById('jform_id_candidate').value;

    // displaying some text during data fetch
    gradeform.value = waittext;
    lastpromotionform.value = waittext;
    grade_achieveform.value = waittext;

    // building URL for request
    var url = 'index.php?option=com_tkdclub&task=candidate.getajaxdata'
                + '&candidate_id=' + candidate_id 
                + '&promotion_id=' + promotion_id ;
    
    // trigger request
    var request = new XMLHttpRequest();
    request.open('GET', url);
    request.responseType = 'json';
    request.send();
    request.onload = function() {
        
        var responseText = request.response;
        
        // checking response for error messages, render if any
        if (responseText['no_error'] == true ){
            // no errors, response goes to form
            gradeform.value = responseText['grade'];
            lastpromotionform.value = responseText['lastpromotion'];
            grade_achieveform.value = responseText['grade_achieve'];
        } else {
            checkErrors(responseText);
            Joomla.renderMessages(message);
            // errors, errortext for form
            gradeform.value = errortext;
            lastpromotionform.value = errortext;
            grade_achieveform.value = errortext;
        }
    }
}

/**
 * with this function the response is analysed for errors
 * and the messages - arrays are created
 * 
 * @param {*} responseText 
 */
function checkErrors(responseText)
{
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