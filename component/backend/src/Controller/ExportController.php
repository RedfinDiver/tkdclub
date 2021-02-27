<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper as Helper;
use Joomla\Utilities\ArrayHelper;


class ExportController extends FormController
{	
	/**
	 * Method to get the content for csv export
	 *
	 * @param string $model the name of model to call
	 *
	 * @return array an array with data in it
	 */
	public function getContent($model = '')
	{
		// Get the input from the url / post
		$pks = $this->input->post->get('cid', array());
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
		$this->app->setHeader('Content-Type', 'application/csv', true)
			->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"', true)
			->setHeader('Cache-Control', 'must-revalidate', true)
			->sendHeaders();
	}

	/**
	*	csv export function for members-view
	**/
	public function members()
	{	
		// Check for request forgeries.
		$this->checkToken();

		$rows = $this->getContent('members');

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_MEMBERS'));

		$output = fopen("php://output", "w");

		foreach ($rows as $row)
		{
			fputcsv($output, $row, ',');
		}

		fclose($output);
		$this->app->close();
	}

	/**
	*	csv export function for trainings-view
	**/
	public function trainings()
	{	
		$memberlist = Helper::getMemberlist();
		$content = $this->getContent('trainings');

		foreach ($content as $key => &$row)
		{
			if ($key > 0)
			{
				if ($row[2] > 0)
				{
					$row[2] = Helper::getMembersNames($row[2], $memberlist);
					$row[4] == 1 ? $row[4] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[4] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[5] > 0)
				{
					$row[5] = Helper::getMembersNames($row[5], $memberlist);
					$row[7] == 1 ? $row[7] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[7] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[8] > 0)
				{
					$row[8] = Helper::getMembersNames($row[8], $memberlist);
					$row[10] == 1 ? $row[10] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[10] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
				if ($row[11] > 0)
				{
					$row[11] = Helper::getMembersNames($row[11], $memberlist);
					$row[13] == 1 ? $row[13] = Text::_('COM_TKDCLUB_TRAINING_PAID') : $row[13] = Text::_('COM_TKDCLUB_TRAINING_NOT_PAID');
				}
			}

			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_TRAININGS'));
	}

	/**
	 * csv export function for medals-view
	 **/
    public function medals()
	{	
		$memberlist = Helper::getMemberlist();
		$content = $this->getContent('medals');;

		foreach ($content as $key => &$row)
		{
			if ($key > 0) // getting the names
			{
				$row[] = Helper::getMembersNames($row[5], $memberlist);
			}

			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_MEDALS'));
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
				$row[3] == 'kup' ? $row[3] = Text::_('COM_TKDCLUB_KUP') : $row[3] = Text::_('COM_TKDCLUB_DAN');
				$row[7] == 1 ? $row[7] = Text::_('COM_TKDCLUB_PROMOTION_ACTIVE') : $row[7] = Text::_('COM_TKDCLUB_PROMOTION_INACTIVE');
			}

			print implode(';', $row)."\n";
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_PROMOTIONS'));
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
				$row[3] = HTMLHelper::_('date', $row[3], Text::_('DATE_FORMAT_LC4'));
				$row[4] == 'male' ? $row[4] = Text::_('COM_TKDCLUB_MEMBER_SEX_MALE') : $row[4] = Text::_('COM_TKDCLUB_MEMBER_SEX_FEMALE');
			}
			
			print implode(';', $row)."\n"; // write data to the browser
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_CANDIDATES'));
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
				$row[2] = HTMLHelper::_('date', $row[2], Text::_('DATE_FORMAT_LC4'));
				$row[3] = HTMLHelper::_('date', $row[3], Text::_('DATE_FORMAT_LC4'));
				$row[7] == 1 ? $row[7] = Text::_('JPUBLISHED') : $row[7] = Text::_('JUNPUBLISHED');
			}
			
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_EVENTS'));
	}

	/**
	*	csv export function for participants-view
	**/
	public function participants()
	{	
		$content = $this->getContent('participants');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0) {$row[1] = HTMLHelper::_('date', $row[1], Text::_('DATE_FORMAT_LC4')); }
			
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_PARTICIPANTS'));
	}

	/**
	*	csv export function for newsletter subscripion-view
	**/
	public function subscribers()
	{	
		$content = $this->getContent('subscribers');
		$origin = array("1" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_MANUAL',
						"2" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_FORM');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0)
			{
				$row[3] = HTMLHelper::_('date', $row[3], Text::_('DATE_FORMAT_LC4'));
				$row[4] = Text::_($origin[$row[4]]);
			}
			
			print implode(';', $row)."\n"; 
		}

		$this->setHeaders(Text::_('COM_TKDCLUB_SIDEBAR_SUBSCRIBERS'));
	}

}