/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

(function (document, submitForm) {
    'use strict'; // Selectors used by this script
  
    var buttonDataSelector = 'data-submit-task';
    var formId = 'adminForm';
    
    /**
     * Submit the task
     * @param task
     */
    var submitTask = function submitTask(task) {
      var form = document.getElementById(formId);
  
      if (task === 'medal.cancel' || document.formvalidator.isValid(form)) {
        submitForm(task, form);
      }
    };
    
    // Register events
    document.addEventListener('DOMContentLoaded', function () {
      var buttons = [].slice.call(document.querySelectorAll("[".concat(buttonDataSelector, "]")));
      buttons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          var task = e.target.getAttribute(buttonDataSelector);
          submitTask(task);
        });
      });
    });
  })(document, Joomla.submitform);