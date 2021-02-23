<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Database\ParameterType;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use stdClass;

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
    public function uploadFile()
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $member_id = $input->getInt('member_id', 0);
        $file = $input->files->get('jform', '', '')['file'];
        $file_hash = hash_file('md5', $file['tmp_name']);
        $possible_file_extensions =  ['pdf', 'png', 'jpg', 'jpeg'];
        $upload_path = ComponentHelper::getParams('com_tkdclub')->get('attachments_path', JPATH_SITE . '/images/com_tkdclub/attachments/');

        // just processing the file if there is no error with it
        if ($file['error'] != 0)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }
        
        // get the filename file-extension
        $filename = $file['name'];
        $ext = File::getExt($filename);
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

        // check the upload path for .htaccess file
        $upload_path = rtrim($upload_path, '/') . '/'; // make sure we have a trailing /
        if (!File::exists($upload_path .'.htaccess'))
        {
            if (File::write($upload_path . '.htaccess', "<Files '*.*'>\nDeny from all\n</Files>"))
            {
                $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILEUPLOAD_HTACCESS', $upload_path) , 'info');
            } 
            else
            {   
                $app->enqueueMessage(Text::sprintf('COM_TKDCLUB_MEMBER_FILEUPLOAD_NO_HTACCESS', $upload_path) , 'error');
                return false;
            }
        }

        $dest = $upload_path . $file_hash;

        // check for already existing files with same hash, not likely but can be...
        in_array($file_hash, Folder::files($upload_path)) ? $file_existed = true : $file_existed = false;

        // Upload the file, create messages
        if (!File::upload($file['tmp_name'], $dest))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }

        // Save filedata in database
        $this->setAttachments($member_id, $dest, $filename); 

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
     * @param   INT     $member_id      member id in database
     * 
	 * @return  mixed    FALSE  if there is no data
     *                   OBJECT json-object
	 */
    public function getAttachments($member_id)
    {   
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('attachments'))
                ->from($db->quoteName('#__tkdclub_members'))
                ->where($db->quoteName('member_id') . ' = :member_id')
                ->bind(':member_id', $member_id, ParameterType::INTEGER);
       
        $db->setQuery($query);

        $result = $db->loadResult();
        
        if ($result === null)
        {
            return false;
        }

        return json_decode($result, true);
        
    }

    /**
	 * Method to set the attachments field in the database
     * 
     * @param   INT      $member_id     integer for member_id
     * @param   STRING   $file_path     full file path
     * @param   STRING   $file_name     original filename, if set to false, $file_path is deleted from field
     * 
	 * @return  mixed    TRUE if everything worked
     *                   FALSE if something went wrong
	 */
    public function setAttachments($member_id, $file_path, $file_name = false)
    {   
        // First get existing entries
        $attachments = $this->getAttachments($member_id);
   
        if (!$attachments)
        {
            $attachments = array();
        }
        
        if (!$file_name)
        {
            // No file_name means just remove invalid items from field
            unset($attachments[$file_path]);
        }
        else
        {
            // Add more data
            $attachments[$file_path] = $file_name;
        }

        if (empty($attachments))
        {
            $attachments = null;
        }
        else
        {
            $attachments = json_encode($attachments, JSON_UNESCAPED_SLASHES);
        }
  
        // Create the update object
        $update = new \stdClass;
        $update->member_id = $member_id;
        $update->attachments = $attachments;
       
        if (Factory::getDbo()->updateObject('#__tkdclub_members', $update, 'member_id', true))
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
    public function deleteFile($file_path)
    {
        // Delete the file
        if (!File::delete($file_path))
        {
            return false;
        }

        return true;
    }

    /**
    * Method to send file to the browser
    *
    */   
    public function downloadFile($file_path, $file_name)
    {
        $mime = mime_content_type($file_path);

        // preparing headers
        header('Content-Type: ' . $mime); 
        header('Content-Disposition: inline; filename="'.$file_name.'"');
        
        // clean the buffer, we don't want Joomla-data in the stream
        ob_clean();
        flush();
        
        // readfile and exit
        readfile($file_path); 
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