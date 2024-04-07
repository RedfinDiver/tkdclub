<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Api\View\Members;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * The member view
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
        'firstname',
        'lastname',
        'functions',
        'member_state'
    ];

    /**
     * The fields to render items in the documents
     *
     * @var  array
     * @since  4.0.0
     */
    protected $fieldsToRenderList = [
        'firstname',
        'lastname',
        'functions',
        'member_state'
    ];
}