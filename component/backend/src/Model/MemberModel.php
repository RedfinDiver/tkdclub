<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

/**
 * Model-class for edit view 'member'
 *
 */
class MemberModel extends AdminModel
{
    /**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   3.0
	 * @throws  \Exception
	 */
    public function getTable($type = 'Members', $prefix = 'Administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
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
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.member.data', array());

        if(empty($data)) {
            $data = $this->getItem();
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
        $app = Factory::getApplication();
        $input = $app->input;
        $id = $input->getInt('member_id', 0);
        $data  = $input->post->get('jform', array(), 'array');
        $files = $input->files->get('jform', '', '');
        $file = $picture == true ? $files['picture'] : $files['file'];
        $possible_file_extensions = $picture == true ? ['png', 'jpg', 'jpeg'] : ['pdf', 'png', 'jpg', 'jpeg'];

        // just processing the file if there is no error with it
        if ($file['error'] != 0)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }
        
        // make the filename safe and get the file-extension
        $filename = File::makeSafe($file['name']);
        $ext = File::getExt($filename);
        $picture == true ? $filename = 'memberpicture' . '.' . $ext : null;
        $file_size = $file['size'];
        
        // only certain files are allowed, give error otherwise
        if (!in_array($ext, $possible_file_extensions))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_ONLY_CERTAIN_EXT'), 'error');
            return false;
        }

        // only files <500 kB are allowed
        if ($file_size > 500000)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FILESZIZE'), 'error');
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
        
        Folder::create($upload_path);
        $dest = $upload_path . $filename;

        // check for already existing files with same name
        in_array($filename, Folder::files($upload_path)) ? $file_existed = true : $file_existed = false;

        // Upload the file create messages
        if (!File::upload($file['tmp_name'], $dest))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }

        // for memberpicture upload set different message
        if ($picture)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_PICTUREUPLOAD_SUCCESS'), 'message');
            return true; 
        }

        // marking existing files in databasefield for ordinary files only, not member picture
        $this->setAttachmentsInDatabase($id, 1); 

        if ($file_existed == false)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_SUCCESS') . $filename, 'message');
            return true;
        }

        $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILE_OVERWRITE') . $filename, 'notice');
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
        $app = Factory::getApplication();
        $member_id = $app->input->get('member_id', 0, 'INT' );

        if ($picture)
        {
            $folder = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $member_id . '/memberpicture';
        }
        else
        {
            $folder = JPATH_COMPONENT_ADMINISTRATOR . '/attachments/members/' . $member_id;
        }
        
        if (Folder::exists($folder))
        {
            return Folder::files($folder);
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
        $db = Factory::getDBO();
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
        $app = Factory::getApplication();
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
        if (!File::exists($file))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILE_NOT_EXISTS'), 'error');
            return false;
        }

        // delete the file, throw error if it didn't work
        if (!File::delete($file))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILE_DELETE_ERROR'), 'error');
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
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_PICTURE_DELETED'));
        }
        else
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILE_DELETED'). $filename);
            
        }

        return true;
        
    }

    /**
    * Method to send file to the browser
    *
    */   
    public function downloadFile()
    {
        $app = Factory::getApplication();
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
        $id_win = Factory::getApplication()->input->getInt('member_id');

        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
                ->from('#__tkdclub_medals')
                ->where('winner_ids REGEXP \'' . '[[:<:]]' . (int) $id_win . '[[:>:]]\'')
                ->order('date DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }    
}