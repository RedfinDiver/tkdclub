<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;

class TkdClubControllerParticipant extends BaseController
{

    public function execute($task = 'execute')
    {
        try
        {
            $app = Factory::getApplication();
            $html = '';
            $toRender = $app->input->get('selection', '', 'int');
            $model = $this->getModel($name = 'participant', $prefix = 'Administrator', $config = array());
            $form = $model->getForm($data = array(), $loadData = true);
        
            if ($toRender == 1)
            {
                foreach($form->getFieldset('participant_data_multiple') as $field)
                {
                    $html .= !$field->hidden ? $form->renderField($field->fieldname) : null;
                }
            }
            elseif ($toRender == 0)
            {
                foreach($form->getFieldset('participant_data_single') as $field)
                {
                    $html .= !$field->hidden ? $form->renderField($field->fieldname) : null;
                }
            }

            $result = array('response' => $html);
            
            echo new JsonResponse($result);
        }
        catch(Exception $e)
        {
            echo new JsonResponse($e);
        }

    }

}