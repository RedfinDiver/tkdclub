<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
* memberform controller
*/
class TkdclubControllerMember extends JControllerForm
{
    protected $text_prefix = 'COM_TKDCLUB_MEMBER';

    public function __construct($config = array())
    {
        parent::__construct($config);
        
        $this->registerTask('uploadfile', 'uploadfile');
    }
    
    public function uploadfile()
    {   
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // saving the item id in variable for proper redirect later on
        $recordId = $this->input->get('member_id', null);
        
        // calling the Model with upload functionality
        $this->getModel()->uploadfile();
        
        // setting the redirect back to the edited item
        $this->setRedirect
        (
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar = 'member_id'), false
                )
        );
    }
    
    /**
     * Delete the selected file
     */
    public function deleteFile()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $id = $this->input->get('member_id');
        
        if ($id > 0) {
            
            $model = $this->getModel();
            $model->deleteFile();
            $this->setRedirect(JRoute::_('index.php?option=com_tkdclub&view=member&layout=edit&member_id='.$id, false));
        }
    }   
    
    /*
     * Open the selected file in a new browser tab
     */
    public function downloadFile()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel();
        $model->downloadFile();
    }

}
