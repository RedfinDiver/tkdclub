<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class TkdClubModelMedals extends ListModel
{   
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_medals');
        $query->order('date DESC');
        
        return $query;
    }
    
    public static function getMedals ($placing)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('placing'))->from($db->qn('#__tkdclub_medals'));
        $query->where($db->qn('placing') . ' = ' . $db->q($placing));
        $query->where($db->qn('state') . ' = ' . (int) 1);

        $db->setQuery($query);
        
        $medals = count($db->loadObjectList());
        
        return $medals;
    }

    public function getMemberlist()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')->from($db->quoteName('#__tkdclub_members'));

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $namelist = array();
        foreach ($items as $i => $item)
        {
            $namelist[$item->member_id] = $item->firstname.' '.$item->lastname;
        }

        return $namelist;
      }
}