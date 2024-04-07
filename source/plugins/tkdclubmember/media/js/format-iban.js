/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict'
    setTimeout(function() {
        if (document.formvalidator) {
            document.formvalidator.setHandler('iban', function(value) {
                return IBAN.isValid(value);
            });

            var patternmask = new IMask(document.querySelector('.validate-iban'), {
                mask: 'aa00 0000 0000 0000 0000 0000 0000',
                placeholderchar: '_'
            })
        }
    }, 1000)
});