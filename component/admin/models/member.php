<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
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

        if (empty($form))
        {
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
     * @param   boolean    $picture false for ordinary attachment file
     *                     $picture true for member picture upload
	 *
	 * @return  boolean    TRUE on success, FALSE on fail
	 */
    public function uploadFile($picture = false)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->getInt('member_id', 0);
        $data  = $input->post->get('jform', array(), 'array');
        $files = $input->files->get('jform', '', '');
        $file = $picture == true ? $files['picture'] : $files['file'];
        $possible_file_extensions = $picture == true ? ['png', 'jpg', 'jpeg'] : ['pdf', 'png', 'jpg', 'jpeg'];

        // just processing the file if there is no error with it
        if ($file['error'] != 0)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }
        
        // make the filename safe and get the file-extension
        $filename = JFile::makeSafe($file['name']);
        $ext = JFile::getExt($filename);
        $picture == true ? $filename = 'memberpicture' . '.' . $ext : null;
        $file_size = $file['size'];
        
        // only certain files are allowed, give error otherwise
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
        if ($picture)
        {
            $upload_path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/memberpicture/';
        }
        else
        {
            $upload_path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/';
        }
        
        JFolder::create($upload_path);
        $dest = $upload_path . $filename;

        // check for already existing files with same name
        in_array($filename, JFolder::files($upload_path)) ? $file_existed = true : $file_existed = false;

        // Upload the file create messages
        if (!JFile::upload($file['tmp_name'], $dest))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }

        // for memberpicture upload set different message
        if ($picture)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_PICTUREUPLOAD_SUCCESS'), 'message');
            return true; 
        }

        // marking existing files in databasefield for ordinary files only, not member picture
        $this->setAttachmentsInDatabase($id, 1); 

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
     * @param   bool     $picture true to search for memberpicture folder
     *                   $picture false for searching in the attachments folder only
     * 
	 * @return  mixed    FALSE if there is no data
     *                   ARRAY $attachments with data otherwise
	 */
    public function getAttachments($picture = false)
    {      
        $app = JFactory::getApplication();
        $member_id = $app->input->get('member_id', 0, 'INT' );

        if ($picture)
        {
            $folder = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $member_id . '/memberpicture';
        }
        else
        {
            $folder = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $member_id;
        }
        
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
	 * @return  mixed    TRUE if everything worked
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
    public function deleteFile($picture = false)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->getInt('member_id', 0);
        $filename = $input->getString('filename', '');

        // building file path and getting already existing attachment data
        if ($picture)
        {
            $file = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/memberpicture/' . $filename;
        }
        else
        {
            $file = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/' . $filename;
        }
        
        // check if file exists then proceed
        if (!JFile::exists($file))
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_NOT_EXISTS'), 'error');
            return false;
        }

        // delete the file, throw error if it didn't work
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

        // selecting the right message
        if ($picture)
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_PICTURE_DELETED'));
        }
        else
        {
            $app->enqueueMessage(JText::_('COM_TKDCLUB_MEMBER_FILE_DELETED'). $filename);
            
        }

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
        $picture = $input->getInt('picture', 0);

        //setting path to File
        if($picture)
        {
            $path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/memberpicture/' . $filename;
        }
        else
        {
            $path = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $id . '/' . $filename;
        }
        
        $mime = mime_content_type($path);

        // preparing headers
        header('Content-Type: ' . $mime); 
        header('Content-Disposition: inline; filename="'.$filename.'"');
        
        // clean the buffer, we don't want Joomla-data in the stream
        ob_clean();
        flush();
        
        // readfile and exit
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

        $query->select('*')
                ->from('#__tkdclub_medals')
                ->where('winner_ids REGEXP \'' . '[[:<:]]' . (int) $id_win . '[[:>:]]\'')
                ->order('date DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }    
}