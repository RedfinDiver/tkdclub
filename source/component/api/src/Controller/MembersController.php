<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2024 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Api\Controller;

use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\CMS\Language\Text;

class MembersController extends ApiController
{
    /**
	 * The content type of the item.
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $contentType = 'members';

	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  5.0
	 */
	protected $default_view = 'members';

	/**
     * Members list view, only active members
     *
     * @return  static  A BaseController object to support chaining.
     *
     * @since   4.0.0
     */
    public function displayList()
    {
        // only active members
        $this->modelState->set('filter.member_state', 'active');

        $viewType   = $this->app->getDocument()->getType();
        $viewName   = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        try {
            /** @var JsonApiView $view */
            $view = $this->getView(
                $viewName,
                $viewType,
                '',
                ['base_path' => $this->basePath, 'layout' => $viewLayout, 'contentType' => $this->contentType]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $modelName = $this->input->get('model', $this->contentType);

        /** @var ListModel $model */
        $model = $this->getModel($modelName, '', ['ignore_request' => true, 'state' => $this->modelState]);

        if (!$model) {
            throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_MODEL_CREATE'));
        }

        // Push the model into the view (as default)
        $view->setModel($model, true);

        $view->document = $this->app->getDocument();

        $view->displayList();

        return $this;
    }

}