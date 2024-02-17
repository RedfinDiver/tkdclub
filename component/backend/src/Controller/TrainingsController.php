<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class TrainingsController extends AdminController
{
    /**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
    protected $text_prefix = 'COM_TKDCLUB_TRAINING';

    /**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   3.0
	 */
    public function getModel($name = 'Training', $prefix = 'Administrator', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Toggle on/off the stats
     * 
     * With this method the statistics are switched on or off
     * in the trainings list view
     * 
     * @return void
     */ 
    public function togglestats()
    {
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $session = Factory::getSession();

        if (!$session->get('togglestats_trainings', null, 'tkdclub'))
        {
            $session->set('togglestats_trainings', 'ON', 'tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_ON';  
        }
        else
        {
            $session->clear('togglestats_trainings','tkdclub');
            $msg = 'COM_TKDCLUB_TOGGLE_STATS_OFF';  
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=trainings', Text::_($msg));
    }

    /**
     * For Ajax call in statistic view
     */
    public function gettrainerdata()
    {
        // Check for request forgeries.
        Session::checkToken('GET') or jexit(Text::_('JINVALID_TOKEN'));
        
        $model = $this->getModel($name = 'trainings', $prefix = 'Administrator', $config = array());
        $data = $model->gettrainerdata();
        echo json_encode($data);

        Factory::getApplication()->close();
    }

    /**
     * For Ajax call in statistic view
     */
    public function gettrainingsdata()
    {
        // Check for request forgeries.
        Session::checkToken('GET') or jexit(Text::_('JINVALID_TOKEN'));
        
        $model = $this->getModel($name = 'trainings', $prefix = 'Administrator', $config = array());
        $data = $model->gettrainingsdata();
        echo json_encode($data);

        Factory::getApplication()->close();
    }

    /**
     * Function to pay trainings
     */
    public function paytrainings()
    {
        // Check for request forgeries.
        Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        
        $app = Factory::getApplication();
        $member_id = $app->input->get('member_id', 0, 'int');
        $name = $app->input->get('name', '', 'string');
        $view = $app->input->get('view', 'trainings', 'string');
        $model = $this->getModel();
        
        if ($model->paytrainings($member_id) === true)
        {
            $updated_rows = $model->updated_rows;
            $updated_rows == 1 ? $msg = Text::plural('COM_TKDCLUB_TRAINING_PAID_TRAININGS_1', $updated_rows)
                               : $msg = Text::plural('COM_TKDCLUB_TRAINING_PAID_TRAININGS', $updated_rows);

            $app->enqueueMessage($msg . Text::sprintf('COM_TKDCLUB_TRAINING_SAVED_AS_PAID', $name));
        }
        else
        {
            $app->enqueueMessage(
                Text::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_FROM_ERROR')
                .$name
                .Text::_('COM_TKDCLUB_STATISTIC_UNPAID_TRAININGS_PAID_ERROR'), 'error');
        }
        
        $this->setRedirect('index.php?option=com_tkdclub&view=' . $view);
    }
}