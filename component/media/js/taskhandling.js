/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

Joomla.submitbutton = task => {
	// intercepting the export tasks for tkdclub
	if (
				task === 'export.members' || 
				task === 'export.trainings' ||
				task === 'export.promotions' ||
				task === 'export.candidates' ||
				task === 'export.participants' ||
				task === 'export.events' ||
				task === 'export.subscribers'
			) {
			exportcommon(task);
			return;
		}

	// intercepting the statistic view toggle in list views
	if (
		task === 'membersstats' ||
		task === 'medalsstats'
	) {
	togglestats(task);
	return;
}

  // no special tkdclub task, perform standard Joomla task
	Joomla.submitform(task);
};

async function exportcommon(task) {
	// task is a hidden field in the foot of the form
	let task_id = document.getElementById("task");
	task_id.value = task;
	const adminform = document.getElementById('adminForm');
	// Bind the FormData object and the form element
	const data = new FormData( adminform );
	const url = 'index.php?option=com_tkdclub';
	const options = {
		method: 'POST',
		body: data
	}
	let response = await fetch(url, options);
	if (!response.ok) {
		throw new Error (Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS') + `${response.status}`);
	}
	const disposition = response.headers.get('content-disposition');
	let filename = '';
	let ext = '';
	if (disposition && disposition.indexOf('attachment') !== -1) {
		var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
		var matches = filenameRegex.exec(disposition);
		if (matches != null && matches[1]) { 
			filename = matches[1].replace(/['"]/g, '');
			ext = filename.substr(filename.lastIndexOf('.'));
		}
	}
	let result = await response.blob();
	// Create a link element, hide it, direct it towards the blob, and then 'click' it programatically
	let a = document.createElement("a");
	a.style = "display: none";
	document.body.appendChild(a);
	// Create a DOMString representing the blob and point the link element towards it
	let myurl = window.URL.createObjectURL(result);
	a.href = myurl;
	// use the filename sent by the server to save the file
	a.download = filename;
	// programatically click the link to trigger the download
	a.click();
	// release the reference to the file by revoking the Object URL
	window.URL.revokeObjectURL(myurl);
	// remove the link created for download
	a.remove();
	// and reset the form task or else ...
	task_id.value = '';
}

function togglestats(task) {
	let stats = document.getElementById(task);
	let icon = document.getElementById("toolbar-eye-open").children[0].children[0];

	console.log(icon);

	if (stats.classList.contains("d-none")) {
		stats.classList.remove("d-none");
		icon.classList.replace("icon-eye-open", "icon-eye-close");
	} else {
		stats.classList.add("d-none");
		icon.classList.replace("icon-eye-close", "icon-eye-open");
	}

	// reset the task value
	let task_id = document.getElementById("task");
	task_id.value = '';
}