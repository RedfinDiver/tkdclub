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
    public function uploadfile($picture = false)
    {   
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // saving the item id in variable for proper redirect later on
        $recordId = $this->input->get('member_id', null);
        
        // calling the Model with upload functionality
        $this->getModel()->uploadfile($picture);
        
        // setting the redirect back to the edited item
        $this->setRedirect
        (
                Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar = 'member_id'), false
                )
        );
    }

    /**
     * Upload the member picture
     */
    public function uploadpicture()
    {   
        // calling the Model with upload functionality
        $this->uploadfile($picture = true);
    }

    /**
     * Delete the memberpicture
     */
    public function deletePicture()
    {
        $this->deleteFile($picture = true);
    }


    /**
     * Delete the selected file
     * 
     * @param   boolean    $picture false to delete a ordinary attachment file
     *                     $picture true to delete the member picture
     * 
     */
    public function deleteFile($picture = false)
    {
        // Check for request forgeries.
        Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

        $id = $this->input->get('member_id');
        
        if ($id > 0) {
            
            $model = $this->getModel();
            $model->deleteFile($picture);
            $this->setRedirect(Route::_('index.php?option=com_tkdclub&view=member&layout=edit&member_id='.$id, false));
        }
    }   
    
    /*
     * Open the selected file in a new browser tab
     */
    public function downloadFile()
    {
        // Check for request forgeries.
        Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

        $model = $this->getModel();
        $model->downloadFile();
    }

}
