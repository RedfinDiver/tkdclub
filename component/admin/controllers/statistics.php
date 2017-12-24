<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubControllerStatistics extends JControllerLegacy
{
    public function __construct($config = array())
    {
        parent::__construct($config = array());
    }
    
    public function getModel($name = 'statistics', $prefix = 'TkdClubModel', $config = array())
    {
        $config['ignore_request'] = true;
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function paytrainings()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        $member_id = $app->input->get('member_id', 0, 'int');
        $name = $app->input->get('name', 0, 'string');
        $model = $this->getModel();
        
        if ($model->paytrainings($member_id) === true)
        {
            $app->enqueueMessage(
                    JText::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_FROM')
                    .$name
                    .JText::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_PAID')
                );
        }
        else
        {
            $app->enqueueMessage(
                JText::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_FROM_ERROR')
                .$name
                .JText::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_PAID_ERROR'), 'error');
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=statistics');
    }
}