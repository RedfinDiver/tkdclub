<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Api\Controller;

use Joomla\CMS\MVC\Controller\ApiController;

class CandidatesController extends ApiController
{
    /**
	 * The content type of the item.
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $contentType = 'candidates';

	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  5.0
	 */
	protected $default_view = 'candidates';

}