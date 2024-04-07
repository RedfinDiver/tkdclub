/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

function removeTrainer(selected, origin) {
    
    sources = ["#jform_trainer", "#jform_assist1", "#jform_assist2", "#jform_assist3"];
    
    sources.forEach(id => {
        if (id != origin && selected > 0) {
            jQuery(id + " option[value='" + selected + "']").remove();
            jQuery(id).trigger("liszt:updated");
        }
    });
}

jQuery(document).ready(function(){

    jQuery("#jform_trainer").change(function (event) {
        selected = jQuery(event.target).val();
        removeTrainer(selected, '#jform_trainer');
    });

    jQuery("#jform_assist1").change(function (event) {
        selected = jQuery(event.target).val();
        removeTrainer(selected, '#jform_assist1');
    });

    jQuery("#jform_assist2").change(function (event) {
        selected = jQuery(event.target).val();
        removeTrainer(selected, '#jform_assist2'); 
    });

    jQuery("#jform_assist3").change(function (event) {
        selected = jQuery(event.target).val();
        removeTrainer(selected, '#jform_assist3');
    });
})