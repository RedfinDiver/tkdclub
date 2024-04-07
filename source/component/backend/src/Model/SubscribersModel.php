<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * Model class for list view 'subscribers'
 */
class SubscribersModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     */
    public function __construct($config = array())
    {   
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                    'email', 'firstname',
                    'lastname', 'registered',
                    'origin', 'id'
                );
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = 'created', $direction = 'DESC')
    {
        // Suchbegriff aus vorheriger Eingabe ermitteln
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $origin = $this->getUserStateFromRequest($this->context.'.filter.origin', 'filter_origin', '');
        $this->setState('filter.year', $origin);

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.origin');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*')->from($db->quoteName('#__tkdclub_newsletter_subscribers', 'a'));
        
        $originselect = $this->getState('filter.origin');
        if (!empty($originselect))
        {
            $query->where('a.origin = ' . (int) $originselect);
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = '.(int) substr($search, 3));
            }
            else
            {
                $search = $db->Quote('%'. $db->escape($search).'%');
                $query->where('a.id LIKE' .$search
                            . ' OR a.email LIKE ' .$search
                            . ' OR a.firstname LIKE ' . $search
                            . ' OR a.lastname LIKE ' . $search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id=a.checked_out');

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));
        
        return $query;
    }

    /**
    * Method to get the number of all entries in the eventparts-table
    * 
    * @return type integer 
    */
    public function getAllRows()

    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_newsletter_subscribers');

        $db->setQuery($query);
        $sets = $db->loadObjectList();

        $allrows = count($sets);

        return $allrows;

    }

    /**
	 * Method to get the data that should be exported.
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
        $db	= Factory::getDBO();
        $query = $db->getQuery(true);

        $fields = array(
            'email',       // 0
            'firstname',   // 1    
            'lastname',    // 2
            'created',     // 3
            'origin',      // 4
            'id',          // 5
        );

        $pks = ArrayHelper::toInteger($pks);
		$query->select($db->quoteName($fields))->from($db->quoteName('#__tkdclub_newsletter_subscribers'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }

        $query->order('created DESC');

		$db->setQuery($query);
		$rows = $db->loadRowList();

        $headers = array(
            Text::_('COM_TKDCLUB_SUBSCRIBER_EMAIL'),       // a.email
            Text::_('COM_TKDCLUB_SUBSCRIBER_FIRSTNAME'),   // a.firstname
            Text::_('COM_TKDCLUB_SUBSCRIBER_LASTNAME'),    // a.lastname
            Text::_('COM_TKDCLUB_SUBSCRIBER_SUBSCRIBED'),  // a.created
            Text::_('COM_TKDCLUB_SUBSCRIBER_ORIGIN'),      // a.origin
            Text::_('COM_TKDCLUB_SUBSCRIBER_ID')           // a.id
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		
       
		$origin = array("1" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_MANUAL',
						"2" => 'COM_TKDCLUB_SUBSCRIBER_ORIGIN_FORM');

		foreach ($content as $key => &$row)
		{ 	
			// conversion of date in LC4-format
			if ($key > 0)
			{
				$row[3] = HTMLHelper::_('date', $row[3], Text::_('DATE_FORMAT_LC4'));
				$row[4] = Text::_($origin[$row[4]]);
			}
		}
        
        return $content;
    }
}