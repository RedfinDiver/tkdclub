<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Model-class for edit view 'member'
 *
 */
class TkdClubModelMember extends JModelAdmin
{
    public function getTable($type = 'Members', $prefix = 'TkdClubTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {   
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('tkdclub', 'member',  $options);

        if (empty($form)) {
            return false;
        }

        return $form;
    }
        
    protected function loadFormData()
    {
        $app =  JFactory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.member.data', array());

        if(empty($data)) {
            $data = $this->getItem();
        }
        
        //handling of date fields, if nothing stored, just show empty string
        foreach ($data as &$value)
        {
            $value == '0000-00-00' ?  $value = '' : null;
        }

        return $data;
    }

    /**
	 * Method to upload a file to attachments folder
	 *
	 * @return  boolean    TRUE on success, FALSE on fail
	 */
    public function uploadFile()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->getInt('member_id', 0);
        $data  = $input->post->get('jform', array(), 'array');
        $file = $input->files->get('jform', '', '');
        $possible_file_extensions = ['pdf', 'png', 'jpg'];

        // just processing the file if there is no error with it
        if ($file['file']['error'] != 0)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }
        
        // make the filename safe and get the file-extension
        $filename = JFile::makeSafe($file['file']['name']);
        $ext = JFile::getExt($filename);
        $file_size = $file['file']['size'];
        
        // only certain Files are allowed, give error otherwise
        if (!in_array($ext, $possible_file_extensions))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_ONLY_CERTAIN_EXT'), 'error');
            return false;
        }

        // only files <500 kB are allowed
        if ($file_size > 500000)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FILESZIZE'), 'error');
            return false;
        }       

        // creating the upload directory and path
        $upload_path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/';
        JFolder::create($upload_path);
        $dest = $upload_path . $filename;

        // check for already existing files with same name
        in_array($filename, JFolder::files($upload_path)) ? $file_existed = true : $file_existed = false;

        // Upload the file create messages
        if (!JFile::upload($file['file']['tmp_name'], $dest))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }

        $this->setAttachmentsInDatabase($id, 1); // marking existing files in databasefield

        if ($file_existed == false)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_SUCCESS') . $filename, 'message');
            return true;
        }

        $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_OVERWRITE') . $filename, 'notice');
        return true;
    }
    
    /**
	 * Method to get the already existing files in an array from folder structure
     * Used also in 'member' - view
     * 
	 * @return  mixed    FALSE if there is no data
     *                   ARRAY $attachments with data otherwise
	 */
    public function getAttachments()
    {      
        $app = JFactory::getApplication();
        $member_id = $app->input->get('member_id', 0, 'INT' );

        $folder = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $member_id;

        if (Jfolder::exists($folder))
        {
            return JFolder::files($folder);
        }

        return false;
    }

    /**
	 * Method to set the attachments field in the database
     * 
     * @param   INT      $member_id integer for member_id
     * @param   BOOL     $bool true or false
     * 
	 * @return  mixed    TRUE if everything workes
     *                   FALSE if something went wrong
	 */
    public function setAttachmentsInDatabase($member_id, $bool)
    {      
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__tkdclub_members'))
              ->set($db->quoteName('attachments') . ' = ' . $bool)
              ->where($db->quoteName('member_id') . ' = ' . $member_id);
        
        $db->setQuery($query);

        if ($db->execute())
        {
            return true;
        }

        return false;
    }
    
    /**
     * Method to delete a file from file-system
     *
     * @return  mixed    FALSE if there is no data
     *                   ARRAY $attachments with data otherwise
     */
    public function deleteFile()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->getInt('member_id', 0);
        $filename = $input->getString('filename', '');

        //building file path and getting already existing attachment data
        $file = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/' . $filename;

        //check if file exists then proceed
        if (!JFile::exists($file))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_NOT_EXISTS'), 'error');
            return false;
        }

        //delete the file, throw error if it didn't work
        if (!JFile::delete($file))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_DELETE_ERROR'), 'error');
            return false;
        }

        // marking database field if there are no more files
        if (!$this->getAttachments())
        {
            $this->setAttachmentsInDatabase($id, 0);
        }

        $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_DELETED'). $filename);
        return true;
    }

    /**
    * Method to send file to the browser
    *
    */   
    public function downloadFile()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('member_id');
        $filename = $input->getString('filename', '');

        //setting path to File
        $path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/' . $filename;

        //preparing headers
        header("Content-Type: "); 
        header("Content-Disposition: inline; filename=\"$filename\"");
        
        //clean the buffer, we don't want Joomla-data in the stream
        ob_clean();
        flush();
        
        //readfile and exit
        readfile($path); 
        jexit();
    }

    /**
	 * Method to get all medals for a member
	 *
	 * @return  object           medals for the member
     *
	 */
    public function getMedals()
    {
        $id_win = JFactory::getApplication()->input->getInt('member_id');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // @TODO creating a JSON field in database for id_win and handle it here
        $query->select('*')
                ->from('#__tkdclub_medals')
                ->where('id_win REGEXP \'' . '[[:<:]]' . (int)$id_win . '[[:>:]]\'')
                ->order('date_win DESC');

        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }

    /**
    * Overrload getItem method for multiple club functions
    */
    public function getItem($pk = null)
    {   
        $items = parent::getItem($pk);
        $registry = new JRegistry;
        $registry->loadString($items->get('functions'));
        $items->set('functions', $registry->toArray());

        return $items;
    }
              
}