<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Model class for list view 'subscribers'
 */
class TkdClubModelSubscribers extends ListModel
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
		$pklist = implode(',', $pks);

		$db	= Factory::getDBO();
		$query	= $db->getQuery(true);

        // select fields from newsletter subscribers table
        $query->select('a.email,a.firstname,a.lastname,a.created,a.origin,a.id');
        $query->from($db->quoteName('#__tkdclub_newsletter_subscribers', 'a'));  

        // only selected items in the list
        $query->where('a.id IN ('.$pklist.')');

        // ordering
        $query->order('a.created DESC');

		$db->setQuery((string)$query);
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
		return $content;
    }
}