<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubControllerParticipant extends JControllerLegacy
{

    public function execute($task = 'execute')
    {
        try
        {
            $app = JFactory::getApplication();
            $html = '';
            $toRender = $app->input->get('selection', '', 'int');
            $model = $this->getModel($name = 'participant', $prefix = 'TkdClubModel', $config = array());
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
            
            echo new JResponseJson($result);
        }
        catch(Exception $e)
        {
            echo new JResponseJson($e);
        }

    }

}