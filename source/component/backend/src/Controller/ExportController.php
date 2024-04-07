<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;

class ExportController extends FormController
{	
	/**
	 * Writes the export data to browser as csv file
	 * 
	 * @param	string	$model	The name of the model to call
	 * @param	string	$name	The name of the file to write
	 * 
	 * @return	void
	 */
	protected function writeFile($model, $name)
	{
		$rows = $this->getContent($model);

		$this->setHeaders($name);

		$output = fopen("php://output", "w");

		foreach ($rows as $row)
		{
			fputcsv($output, $row, ',');
		}

		fclose($output);
		$this->app->close();
	}

	/**
	 * Method to get the content for csv export
	 *
	 * @param string $model the name of model to call
	 *
	 * @return array an array with data in it
	 */
	protected function getContent($model = '')
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
	protected function setHeaders($filename = 'download')
	{
		$date = date('d-m-Y');
		$this->app->setHeader('Content-Type', 'text/csv; charset=utf-8', true)
			->setHeader('Content-disposition', 'attachment; filename="' . $filename . $date . '.csv"', true)
			->setHeader('Cache-Control', 'no-cache', true)
			->sendHeaders();
	}

	/**
	*	csv export function for members-view
	**/
	public function members()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('members', Text::_('COM_TKDCLUB_EXPORT_MEMBERS'));
	}

	/**
	*	csv export function for trainings-view
	**/
	public function trainings()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('trainings', Text::_('COM_TKDCLUB_EXPORT_TRAININGS'));
	}

	/**
	 * csv export function for medals-view
	 **/
    public function medals()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('medals', Text::_('COM_TKDCLUB_EXPORT_MEDALS'));
	}

	/**
	 * csv export function for promotions
	 **/
    public function promotions()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('promotions', Text::_('COM_TKDCLUB_EXPORT_PROMOTIONS'));
	}

	/**
	*	csv export function for candidates-view
	*	the data fits right into the official promotion protocol
	*
	**/
	public function candidates()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('candidates', Text::_('COM_TKDCLUB_EXPORT_CANDIDATES'));
	}

	/**
	 * csv export function for events-view
	 */
	public function events()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('events', Text::_('COM_TKDCLUB_EXPORT_EVENTS'));
	}

	/**
	*	csv export function for participants-view
	**/
	public function participants()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('participants', Text::_('COM_TKDCLUB_EXPORT_PARTICIPANTS'));
	}

	/**
	*	csv export function for newsletter subscripion-view
	**/
	public function subscribers()
	{	
		// Check for request forgeries.
		$this->checkToken();
		$this->writeFile('subscribers', Text::_('COM_TKDCLUB_EXPORT_SUBSCRIBERS'));
	}
}
