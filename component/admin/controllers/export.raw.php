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
	 * method to get the content for csv export
	 *
	 * @param string $model the name of model to call
	 *
	 * @return array an array with data in it
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
	 * method for setting the header in all downloaded csv files
	 *
	 * @param string $filename name of the file to download
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
		{
			print implode(';', $row)."\n";
		}

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
				if ($row[2] > 0)
				{
					$row[2] = $helper->getMembersNames($row[2], $memberlist);
					$row[4] == 1 ? $row[4] = JText::_('COM_TKDCLUB_TRAINING_PAID') : $row[4] = JText::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[5] > 0)
				{
					$row[5] = $helper->getMembersNames($row[5], $memberlist);
					$row[7] == 1 ? $row[7] = JText::_('COM_TKDCLUB_TRAINING_PAID') : $row[7] = JText::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[8] > 0)
				{
					$row[8] = $helper->getMembersNames($row[8], $memberlist);
					$row[10] == 1 ? $row[10] = JText::_('COM_TKDCLUB_TRAINING_PAID') : $row[10] = JText::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[11] > 0)
				{
					$row[11] = $helper->getMembersNames($row[11], $memberlist);
					$row[13] == 1 ? $row[13] = JText::_('COM_TKDCLUB_TRAINING_PAID') : $row[13] = JText::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
			}

			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_TRAININGS'));
	}

	/**
	 * csv export function for medals-view
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

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_MEDALS'));
	}

	/**
	 * csv export function for promotion-view
	 **/
    public function promotions()
	{	
		$content = $this->getContent('promotions');

		foreach ($content as $key => &$row)
		{
			if ($key > 0)
			{
				$row[3] == 'kup' ? $row[3] = JText::_('COM_TKDCLUB_PROMOTION_KUP') : $row[3] = JText::_('COM_TKDCLUB_PROMOTION_DAN');
				$row[7] == 1 ? $row[7] = JText::_('COM_TKDCLUB_PROMOTION_ACTIVE') : $row[7] = JText::_('COM_TKDCLUB_PROMOTION_INACTIVE');
			}

			print implode(';', $row)."\n";
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_PROMOTIONS'));
	}

	/**
	*	csv export function for candidates-view
	*	the data fits right into the official promotion protocol
	*
	**/
	public function candidates()
	{	
		$content = $this->getContent('candidates');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0)
			{
				$row[3] = JHtml::_('date', $row[3], JText::_('DATE_FORMAT_LC4'));
				$row[4] == 'male' ? $row[4] = JText::_('COM_TKDCLUB_MEMBER_SEX_MALE') : $row[4] = JText::_('COM_TKDCLUB_MEMBER_SEX_FEMALE');
			}
			
			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_CANDIDATES'));
	}

	/**
	 * csv export function for events-view
	 */
	public function events()
	{	
		$content = $this->getContent('events');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0)
			{
				$row[2] = JHtml::_('date', $row[2], JText::_('DATE_FORMAT_LC4'));
				$row[3] = JHtml::_('date', $row[3], JText::_('DATE_FORMAT_LC4'));
				$row[7] == 1 ? $row[7] = JText::_('JPUBLISHED') : $row[7] = JText::_('JUNPUBLISHED');
			}
			
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(JText::_('COM_TKDCLUB_SIDEBAR_EVENTS'));
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