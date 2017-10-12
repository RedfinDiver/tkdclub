<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
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
     * Overriding save method for additional info about
     * modifiy and create date
    */
    public function save($key = null, $urlVar = null)
    {
        // getting the saving user and the current datetime
        $user_id = JFactory::getUser()->id;
        $date = JFactory::getDate()->toSql();
        
        // getting the data array
        $fields = $this->input->post->get('jform', array(), 'array');
        
        if ($this->input->get('member_id') > 0)
        {                    
            //already existing item, just changing the modified fields
            $fields['modified'] = $date;
            $fields['modified_by'] = intval($user_id);
        }
        elseif ($this->input->get('member_id') == 0)
        {
            //new created item, just setting create fields
            $fields['created'] = $date;
            $fields['created_by'] = intval($user_id);            
        }

        // checking if there is a array for multiple field date_add
        // for converting it to a string for saving in the database
        if ($fields['functions']) 
        {
            $registry = new JRegistry;
            $registry->loadArray($fields['functions']);

            $fields['functions'] = (string) $registry;
        }
        else
        {
            $fields['functions'] = '';
        }
        
        //pushing back the fields to the input object
        $this->input->post->set('jform', $fields);
        
        parent::save($key, $urlVar);
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
