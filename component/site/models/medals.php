<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdClubModelMedals extends JModelList
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
         $db = JFactory::getDbo();
         $query = $db->getQuery(true);
         $query->select('placing')->from('#__tkdclub_medals');
         $query->where('placing =' .$placing);

         $db->setQuery($query);
         
         $medals = count($db->loadObjectList());
         
         return $medals;
    }

    public function getMemberlist()
    {
          $db = $this->getDbo();
          $query = $db->getQuery(true);

          //Mitglieder wählen
          $query->select('*')
                ->from($db->quoteName('#__tkdclub_members'));

          $db->setQuery($query);
          $items = $db->loadObjectList();

          //Array aus Namen erzeugen, Index ist der Wert der ID
          $namelist = array();
          foreach ($items as $i => $item)
          {
              $namelist[$item->member_id] = $item->firstname.' '.$item->lastname;
          }

          return $namelist;
      }
}