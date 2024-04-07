<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

/**
* memberform controller
*/
class MemberController extends FormController
{
    /**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
    protected $text_prefix = 'COM_TKDCLUB_MEMBER';

    /**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since   3.0
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

        $this->registerTask('uploadfile', 'uploadfile');
	}

    /**
     * Uploads a file to members attachment folder
     * 
     * @param   boolean    $picture false for ordinary attachment file
     *                     $picture true for member picture upload
     */
    public function uploadfile()
    {   
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // saving the item id in variable for proper redirect later on
        $recordId = $this->input->get('id', null);
        
        // calling the Model with upload functionality
        $this->getModel()->uploadfile();
        
        // setting the redirect back to the edited item
        $this->setRedirect
        (
                Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar = 'id'), false
                )
        );
    }

    /**
     * Delete the selected file
     * 
     * @param   boolean    $picture false to delete a ordinary attachment file
     *                     $picture true to delete the member picture
     * 
     */
    public function deleteFile()
    {
        // Check for request forgeries.
        Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

        $app = Factory::getApplication();
        $input = $app->input;
        $file_path = $input->getString('file_path', '');
        $file_name = $input->getString('file_name', '');
        $member_id = $input->getInt('id', 0);

        // Check for existing file
        if (!$this->checkFile($file_path, $member_id))
        {
            // Inform the user and redirect
            $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILE_NOT_FOUND', $file_name,), 'error');
            $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=member&layout=edit&id='.$member_id, false));
            
            return false;
        }
        
        $model = $this->getModel();

        if (!$model->deleteFile($file_path))
        {
            // Something went wrong, inform the user
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILE_DELETE_ERROR'), 'error');

            return false;
        }

        if (!$model->setAttachments($member_id, $file_path, false))
        {
            // Update of database field went wrong
            $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILE_NOT_DELETED_DBFIELD', $file_name,), 'error');

            return false;
        }

        $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILE_DELETED', $file_name,), 'notice');
        $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=member&layout=edit&id='.$member_id, false));

        return true;
    }   
    
    /*
     * Open the selected file in a new browser tab
     */
    public function downloadFile()
    {
        // Check for request forgeries.
        Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

        $app = Factory::getApplication();
        $input = $app->input;
        $file_path = $input->getString('file_path', '');
        $file_name = $input->getString('file_name', '');
        $member_id = $input->getInt('id', 0);

        // Check for existing file
        if (!$this->checkFile($file_path, $member_id))
        {
            $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILE_NOT_FOUND', $file_name,), 'error');
            $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=member&layout=edit&id='.$member_id, false));
            return false;
        }

        $model = $this->getModel();
        $model->downloadFile($file_path, $file_name);
        $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=member&layout=edit&id='.$member_id, false));

        return true;
    }

    /*
     * Fix the Attachments field in database
     * 
     * 
    */
    public function checkFile($file_path, $member_id)
    {
        if (!File::exists($file_path))
        {
            // Get the existing attachments
            $model = $this->getModel();
            $model->setAttachments($member_id, $file_path);

            return false;
        }

        return true;
    }
}