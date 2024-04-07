<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Api\View\Medals;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * The article view
 *
 * @since  4.0.0
 */
class JsonapiView extends BaseApiView
{
    /**
     * The fields to render item in the documents
     *
     * @var  array
     * @since  4.0.0
     */
    protected $fieldsToRenderItem = [
        'date',
        'winner1',
        'winner2',
        'winner3',
        'placing',
        'championship',
        'type',
        'class'
    ];

    /**
     * The fields to render items in the documents
     *
     * @var  array
     * @since  4.0.0
     */
    protected $fieldsToRenderList = [
        'date',
        'winner1',
        'winner2',
        'winner3',
        'placing',
        'championship',
        'type',
        'class'
    ];
}