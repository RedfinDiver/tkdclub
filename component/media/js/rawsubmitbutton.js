/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

RawFormatSubmitbutton = function(task)
{
	var admform	= document.forms['adminForm'];
	if(admform == null) {
		alert('no adminForm defined');
		return;
	}
	var fmt		= admform.elements.namedItem('format');
	if ((fmt == null) || (fmt.tagName != 'INPUT')) {
		fmt		= document.createElement('input');
		fmt.name	= 'format';
		fmt.type	= 'hidden';
		admform.appendChild(fmt);
		oldfmt		= 'html';
	} else {
		oldfmt		= fmt.value;
	}
	fmt.value	= 'raw';
	Joomla.submitform(task);
	fmt.value	= oldfmt;
	var tsk = admform.elements.namedItem('task');
	tsk.value = '';
}