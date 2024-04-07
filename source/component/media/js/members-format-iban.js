/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

document.addEventListener('DOMContentLoaded', function(){
    ibans = document.querySelectorAll('.iban');
    ibans.forEach(function(iban) {
        iban.innerText = IBAN.printFormat(iban.innerText);
    }); 
});