<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class MedalController extends FormController
{   
    /**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
    protected $text_prefix = 'COM_TKDCLUB_MEDAL';

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// We have to change the input data, because we using an array to
		// fill 3 fields in the database
		$data  = $this->input->post->get('jform', array(), 'array');
		$winner_ids = $data['winner_ids'];
		
		$i = 1;
		foreach ($winner_ids as $id)
		{
			if ($id)
			{
				$data['winner_' . $i] = $id;
				$i++;
			}

			// Only allow up to 3 winner fields
			if ($i == 4) break;
		}

		// Check if the required fields are set
		!isset($data['winner_2']) ? $data['winner_2'] = 0 : "";
		!isset($data['winner_3']) ? $data['winner_3'] = 0 : "";
		
		$this->input->post->set('jform', $data);

		return parent::save($key, $urlVar);
	}
}