<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

defined('_JEXEC') or die;
JLoader::register('TkdclubHelperMembers', JPATH_COMPONENT. '/helpers/members.php' );


class TkdClubControllerExport extends JControllerForm
{	
	/**
	*method to get the content for csv export
	*
	*@param string $model the name of model to call
	*
	*@return array an array with data in it
	*/
	public function getContent($model = '')
	{
		// Get the input from the url / post
		$app 	= JFactory::getApplication();
		$pks 	= $app->input->get('cid', array(), 'array');
		$model  = $this->getModel($model);
		$content = $model->getExportData($pks);

		return $content;
	}

	/**
	*method for setting the header in all downloaded csv files
	*
	*@param string $filename name of the file to download
	*
	**/
	public function setHeaders($filename = 'download')
	{
		$app = JFactory::getApplication();
		$app->setHeader('Content-Type', 'application/csv; charset=utf-8', true);
		$app->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'.csv"', true);
		$app->setHeader('Content-Transfer-Encoding', 'binary', true);
		$app->setHeader('Expires', '0', true);
		$app->setHeader('Pragma','no-cache',true);
	}

	/**
	*	csv export function for members-view
	**/
	public function members()
	{	
		$content = $this->getContent('members');

		foreach ($content as $row)
			{ print implode(';', $row)."\n";  }

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_MEMBERS'));
	}

	/**
	*	csv export function for trainings-view
	**/
	public function trainings()
	{	
		$helper = new TkdclubHelperMembers;
		$memberlist = $helper->getMemberlist();
		$content = $this->getContent('trainings');

		foreach ($content as $key => &$row)
		{
			if ($key > 0)
			{
				if ($row[2] > 0) {$row[2] = $helper->getMembersNames($row[2], $memberlist);}
				if ($row[4] > 0) {$row[4] = $helper->getMembersNames($row[4], $memberlist);}
				if ($row[6] > 0) {$row[6] = $helper->getMembersNames($row[6], $memberlist);}
				if ($row[8] > 0) {$row[8] = $helper->getMembersNames($row[8], $memberlist);}
			}

			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SUBMENU_TRAININGS'));
	}

	/**
	*	csv export function for medals-view
	**/
    public function medals()
	{	
		$helper = new TkdclubHelperMembers;
		$memberlist = $helper->getMemberlist();
		$content = $this->getContent('medals');;

		foreach ($content as $key => &$row)
		{
			if ($key > 0) // getting the names
			{
				$row[] = $helper->getMembersNames($row[5], $memberlist);
			}
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SUBMENU_MEDALS'));
	}

	/**
	*	csv export function for examparts-view
	*	the data fits right into the official exam protocol
	*
	**/
	public function examparts()
	{	
		$content = $this->getContent('examparts');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0) {$row[3] = JHtml::_('date', $row[3], JText::_('DATE_FORMAT_LC4')); }
			
			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SUBMENU_EXAMPARTS'));
	}

	/**
	*	csv export function for eventparts-view
	**/
	public function eventparts()
	{	
		$content = $this->getContent('eventparts');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0) {$row[1] = JHtml::_('date', $row[1], JText::_('DATE_FORMAT_LC4')); }
			
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SUBMENU_EVENTPARTS'));
	}

}