<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

/**
 * Helper class for tkdclub component
 */
class TkdclubHelper
{
    /**
     * Get an associative array from string
     * Mostly used for generating lists for select boxes out of parameters
     */
    public static function getList($string = '')
    {
        if (empty($string)) {
            return false;
        } else {
            $parts = explode(',', $string);
            $result = array();
            foreach ($parts as $key => $value) {
                $result[$value] = $value;
            }

            return $result;
        }
    }

    /**
     * Gets the number of participants for an event
     * 
     * @param   int $event_id id of the event in the events-table
     * 
     * @return  int number of participants for the event
     * 
     * @see     tmpl/events/default.php
     */
    public static function getEventparts($event_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('sum(' . $db->quoteName('registered') . ')')
            ->from($db->quoteName('#__tkdclub_event_participants'))
            ->where($db->quoteName('event_id') .' = :event_id')
            ->bind(':event_id', $event_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        return $db->loadResult();
    }

    /**
     * Get all the email adresses for the given user group
     * 
     * @param   array   $groups   the group ids
     * 
     * @return  array   indexed array with email adresses for the groups
     * 
     */
    public static function getEmailFromUserGroups($groups)
    {
        // If no group is given return
        if (empty($groups))
        {
            return false;
        }

        $emails = array();

        foreach ($groups as $group) {
            // Get all email adesses for the users
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('a.email'))
                ->from($db->quoteName('#__users', 'a'))
                ->join('LEFT', $db->quoteName('#__user_usergroup_map', 'b') . ' ON a.id = b.user_id')
                ->where($db->quoteName('group_id') . ' = :group')
                ->bind(':group', $group, ParameterType::INTEGER);

            $emails = array_merge($emails, $db->setQuery($query)->loadColumn());
        }

        return array_unique($emails);
    }

    /**
     * Method get the name of a member by id
     * 
     * @param   mixed   array   with integers which are the ids of members in db
     *                  json    valid json string with integers
     *                  integer id of member
     *
     * @return  mixed   FALSE   if there is no data
     *                  STRING  'firstname lastname' for 1 member
     *                  STRING   names, diveded with "/" for 2 or more members
     */
    public static function getMembersNames($ids, $memberlist)
    {   
        // 0 means no more winner
        if ($ids == 0) {
            return '';
        }

        $type = gettype($ids);

        switch ($type) {
            case 'integer':
                return $memberlist[$ids];
                break;

            case 'string';
                $ids = json_decode($ids);
                break;

            case 'array':
                break;
        }

        $names = '';

        foreach ($ids as $id) {
            $id ? $names .= ' / ' . $memberlist[$id] : '';
        }

        return ltrim($names, " /");
    }

    /**
     * Method get a associated array with members from the database
     *
     * @return  array    ["id" => "firstname lastname"]
     *
     */
    public static function getMemberlist()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $fields = array('id', 'firstname', 'lastname');

        $query->select($db->quoteName($fields))
            ->from($db->quoteName('#__tkdclub_members'));
        $db->setQuery($query);
        $items = $db->loadObjectList();

        // create arrays, $key = id, $value = 'firstname lastname'
        $memberlist = array();

        foreach ($items as $i => $item) {
            $memberlist[$item->id] = $item->firstname . ' ' . $item->lastname;
        }

        return $memberlist;
    }

    /**
     * Checks the payment-state of a training
     * 
     * @param int $trainer_paid 0 for not paid, 1 for paid
     */
    public static function getpaystate($trainer_paid, $assist1, $assist2, $assist3, $assist1_paid, $assist2_paid, $assist3_paid)
    {
        // No trainer or assistent paid -> training is not paid at all
        if (!$trainer_paid && !$assist1_paid && !$assist2_paid && !$assist3_paid)
        {
            return 0;
        }
        else
        {
            !$assist1 || $assist1 && $assist1_paid ? $check1 = true : $check1 = false;
            !$assist2 || $assist2 && $assist2_paid ? $check2 = true : $check1 = false;
            !$assist3 || $assist3 && $assist3_paid ? $check3 = true : $check3 = false;

            // All checks good --> training is entirly paid
            if ($check1 && $check2 && $check3 && $trainer_paid)
            {
                return 1;
            } 
            else // One or more checks not OK --> so return partly paid
            {
                return 2;
            }
        }
    }

    /**
     * Returns the Age in Years at current date
     * 
     * @param string $birthday date-string in the format YYYY-mm-dd
     * @param string $format   'y' for integer years, 'days' for integer days
     * 
     * @return integer Age on current date in years or days
     * 
     * @since 1.0
     */
    public static function getAge($birthday, $format = 'y')
    {
        if (!$birthday)
        {
            return 0;
        }

        $dob = new \DateTime($birthday);
        $now = new \DateTime('today');
        $age = $dob->diff($now);

        if ($format == 'y')
        {
            return $age->y;
        } 
        else
        {
            return $age->days;
        }
    }

    /**
     * Returns the Age in Years to particular date
     * 
     * @param string $date      date-string in the format YYYY-mm-dd
     * @param string $birthday  date-string in the format YYYY-mm-dd
     * 
     * @return string Age in years on current date
     * 
     * @since 1.0
     */
    public static function getAgetoDate($date, $birthday)
    {
        if (!$birthday)
        {
            return "n/a";
        }

        $dob = new \DateTime($birthday);
        $dat = new \DateTime($date);
        $age = $dob->diff($dat);

        return $age->y;
    }

    /**
     * Helper for getting trainer names from database, used for select fields
     * 
     * @param   bool    $fromTrainingsTable  true  = get trainernames from Trainingstable
     *                                       false = get names from aktive members with trainer function
     * 
     * @return  object  when called with false, not directly usable for select fields, more data is fetched from db
     * @return  array   array with trainer names like ["id" => "firstname lastname"] when called with false
     * 
     * @since   3.0.0
     */
    public static function getTrainer($fromTrainingsTable = true)
    {
        if ($fromTrainingsTable == true)
        {
            $db = Factory::getDBO();

            $q1 = $db->getQuery(true)
                ->select(array('a.id', 'a.firstname', 'a.lastname', 'a.sex'))->from($db->quoteName('#__tkdclub_members', 'a'))
                ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.trainer') . ')')
                ->where('trainer>0');

            $q2 = $db->getQuery(true)
                ->select(array('a.id', 'a.firstname', 'a.lastname', 'a.sex'))->from($db->quoteName('#__tkdclub_members', 'a'))
                ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.assist1') . ')')
                ->where('assist1>0');

            $q3 = $db->getQuery(true)
                ->select(array('a.id', 'a.firstname', 'a.lastname', 'a.sex'))->from($db->quoteName('#__tkdclub_members', 'a'))
                ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.assist2') . ')')
                ->where('assist2>0');

            $q4 = $db->getQuery(true)
                ->select(array('a.id', 'a.firstname', 'a.lastname', 'a.sex'))->from($db->quoteName('#__tkdclub_members', 'a'))
                ->join('LEFT', $db->quoteName('#__tkdclub_trainings', 'b') . ' ON(' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.assist3') . ')')
                ->where('assist3>0');

            $query = $q1->union($q2)->union($q3)->union($q4);
            $db->setQuery($query);

            return $db->loadObjectList();
        }

        if ($fromTrainingsTable == false)
        {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);

            $query->select($db->quoteName(array('id', 'firstname', 'lastname')));
            $query->from($db->quoteName('#__tkdclub_members'));
            $query->order('id ASC');
            $query->where($db->quoteName('functions') . ' LIKE ' . $db->quote('%Trainer%'));

            $db->setQuery($query);

            return TkdclubHelper::prepareArray($db->loadObjectList());
        }
    }

    /**
     * Prepare array for trainer list
     */
    public static function prepareArray($result)
    {
        $trainers = array();

        foreach ($result as $trainer) {
            $trainers[$trainer->id] = $trainer->firstname . ' ' . $trainer->lastname;
        }

        return $trainers;
    }

    /**
     * Translation helper for functions
     * 
     * @return  array
     */
    public static function getFunctionTranslation($sex = 'male')
    {
        if ($sex == 'male')
        {
            $translation = array(
                'president' => Text::_('COM_TKDCLUB_SELECT_PRESIDENT'),
                'vpresident'=> Text::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT'),
                'treasurer' => Text::_('COM_TKDCLUB_SELECT_TREASURER'),
                'secratary' => Text::_('COM_TKDCLUB_SELECT_SECRETARY'),
                'trainer'   => Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM')
            );
        }
        else
        {   $translation = array(
                'president' => Text::_('COM_TKDCLUB_SELECT_PRESIDENT_FEMALE'),
                'vpresident'=> Text::_('COM_TKDCLUB_SELECT_VICE_PRESIDENT_FEMALE'),
                'treasurer' => Text::_('COM_TKDCLUB_SELECT_TREASURER_FEMALE'),
                'secratary' => Text::_('COM_TKDCLUB_SELECT_SECRETARY_FEMALE'),
                'trainer'   => Text::_('COM_TKDCLUB_SELECT_TRAINER_IN_MEMBERFORM_FEMALE')
            );

        }

        return $translation;
    }
}
