/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// validates filled in fields in email view
Joomla.submitbutton = function(pressbutton) {
    var form = document.adminForm;

    if (pressbutton == 'email.cancel') {
        Joomla.submitform(pressbutton);
        return;
    }

    // do field validation
    if (form.jform_subject.value == "") {
        alert(Joomla.Text._('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_SUBJECT'));
    } else if (form.jform_message.value == "") {
        alert(Joomla.Text._('COM_TKDCLUB_EMAIL_PLEASE_FILL_IN_THE_MESSAGE'));
    } else {
        Joomla.submitform(pressbutton);
    }
}