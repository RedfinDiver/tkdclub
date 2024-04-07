<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Model-class for edit view member.
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

    /**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 * @since   1.6
	 */
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

    /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
    protected function loadFormData()
    {
        $app =  Factory::getApplication();
        $data = $app->getUserState('com_tkdclub.edit.member.data', array());

        if(empty($data)) {
            $data = $this->getItem();
        }

        $data->lastpromotion == '0000-00-00' ? $data->lastpromotion = '' : '';
        $data->leave == '0000-00-00' ? $data->leave = '' : '';
        $data->memberpass == 0 ? $data->memberpass = '' : '';

        return $data;
    }

    /**
	 * Method to upload a file to attachments folder
	 *
	 * @return  boolean    TRUE on success, FALSE on fail
	 */
    public function uploadFile()
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $member_id = $input->getInt('id', 0);
        $file = $input->files->get('jform', '', '')['file'];
        $file_hash = hash_file('md5', $file['tmp_name']);
        $possible_file_extensions =  ['pdf', 'png', 'jpg', 'jpeg'];
        $upload_path = ComponentHelper::getParams('com_tkdclub')->get('attachments_path', JPATH_ADMINISTRATOR . '/components/com_tkdclub/attachments/');

        // Just processing the file if there is no error with it
        if ($file['error'] != 0)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FAILED'), 'error');
            return false;
        }
        
        // Get the filename file-extension
        $filename = $file['name'];
        $ext = File::getExt($filename);
        $file_size = $file['size'];
        
        // Only certain files are allowed, give error otherwise
        if (!in_array($ext, $possible_file_extensions))
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_ONLY_CERTAIN_EXT'), 'error');
            return false;
        }

        // Only files <500 kB are allowed
        if ($file_size > 500000)
        {
            $app->enqueueMessage(Text::_('COM_TKDCLUB_MEMBER_FILEUPLOAD_FILESZIZE'), 'error');
            return false;
        }       

        // Check the upload path for .htaccess file
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

        // Check for already existing files with same hash, not likely but can be...
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
	 * Method to get the path and original filename for already existing files
     * stored in the database field attachments
     * 
     * @param   INT     $member_id      member id in database
     * 
	 * @return  mixed   FALSE  if there is no data
     *                  OBJECT json-object
	 */
    public function getAttachments($member_id)
    {   
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('attachments'))
                ->from($db->quoteName('#__tkdclub_members'))
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $member_id, ParameterType::INTEGER);
       
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
        $update->id = $member_id;
        $update->attachments = $attachments;
       
        if (Factory::getDbo()->updateObject('#__tkdclub_members', $update, 'id', true))
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
	 * @return  object  medals for the member
     *
	 */
    public function getMedals()
    {
        $id_win = Factory::getApplication()->input->getInt('member_id');

        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
                ->from('#__tkdclub_medals')
                ->where($db->quoteName('winner_1') . ' = :winner_1', 'OR')
                ->where($db->quoteName('winner_2') . ' = :winner_2', 'OR')
                ->where($db->quoteName('winner_3') . ' = :winner_3', 'OR')
                ->bind([':winner_1', ':winner_2', ':winner_3'], $id_win, ParameterType::INTEGER)
                ->order('date DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
	 * Method to get trainingsdata for a member
	 *
	 * @return  array  trainingsdata for the given member
     *
	 */
    public function getTrainings($member_id = 0)
    {
        if ($member_id == 0) {

            $member_id = Factory::getApplication()->input->getInt('id');
        }

        $json_member_id = json_encode([strval($member_id)]);
        
        $trainings = [];

        $db = Factory::getDbo();

        // all trainings
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')->from($db->quoteName('#__tkdclub_trainings'))
                ->where('JSON_CONTAINS('. $db->quoteName('participant_ids') . ', ' . ':member_id' . ')')
                ->bind([':member_id'], $json_member_id, ParameterType::STRING);
        $db->setQuery($query);
        $trainings['all'] = $db->loadResult();

        // getting lastpromotion date
        $query = $db->getQuery(true);
        $query->select($db->quoteName('lastpromotion'))->from($db->quoteName('#__tkdclub_members'))
                    ->where($db->quoteName('id') . ' = ' . ':id')
                    ->bind(':id', $member_id, ParameterType::INTEGER);
        $db->setQuery($query);
        $lastpromotion = $db->loadResult();

        // if there is no promotion date, all trainings count for the next promotion
        if($lastpromotion == '0000-00-00') {

            $trainings['sLastPromotion'] = $trainings['all'];

            return $trainings;
        }

        // getting trainings since last promotion
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')->from($db->quoteName('#__tkdclub_trainings'))
                ->where('JSON_CONTAINS('. $db->quoteName('participant_ids') . ', ' . ':id' . ')')
                ->where($db->quoteName('date') . ' > ' . $db->quote($lastpromotion))
                ->bind([':id'], $json_member_id, ParameterType::STRING);
        $db->setQuery($query);
        $trainings['sLastPromotion'] = $db->loadResult();

        return $trainings;
    }


    /**
     * Method to validate the form data.
     *
     * @param   Form    $form   The form to validate against.
     * @param   array   $data   The data to validate.
     * @param   string  $group  The name of the field group to validate.
     *
     * @return  array|boolean  Array of filtered data if valid, false otherwise.
     *
     * @see     FormRule
     * @see     InputFilter
     * @since   1.6
     */
    public function validate($form, $data, $group = null)
    {
        $validData = parent::validate($form, $data, $group = null);

        // check for no data for functions and licenses fields
        if (!isset($validData['functions'])) {
            $validData['functions'] = "";
        }

        if (!isset($validData['licenses'])) {
            $validData['licenses'] = "";
        }

        return $validData;
    }


}
