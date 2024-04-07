<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace Redfindiver\Component\Tkdclub\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * Model class for list view 'promotions'
 */
class PromotionsModel extends ListModel
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
            $config['filter_fields'] = array('id', 'promotion_state', 'type', 'city', 'examiner_name');
        }
        
        parent::__construct($config);
    }

    /**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
    protected function populateState($ordering = 'date', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest($this->context.'.filter.promotion_state', 'filter_promotion_state', '');
        $this->setState('filter.promotion_state', $state);

        $type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        parent::populateState($ordering, $direction);
    }
    
	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.0
	 */
    protected function getStoreId($id = '')
    {
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.promotion_state');
        $id	.= ':'.$this->getState('filter.type');
        
        return parent::getStoreId($id);
    }

    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.0
	 */        
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('p.*')->from($db->quoteName('#__tkdclub_promotions') . ' as p');

        $stateselect = $this->getState('filter.promotion_state');
        if (is_numeric($stateselect))
        {   
            $query->where('p.promotion_state = ' . (int) $stateselect);
        }

        $typeselect = $this->getState('filter.type');
        if (!empty($typeselect))
        {   
            $type = $db->quote($db->escape($typeselect, true));
            $query->where('p.type = ' .$type);
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('p.id = '.(int) substr($search, 3));
            }
            else
            {
                $search = $db->Quote('%'. $db->escape($search).'%');
                $query->where('p.id LIKE' .$search
                            .'OR p.city LIKE' .$search
                            .'OR p.examiner_name LIKE' .$search);
            }
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')
        ->join('LEFT', '#__users AS u ON u.id=p.checked_out');

        $sort = $this->getState('list.ordering', 'p.date');
        $order = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;
    }

    /**
    * Method to get the number of all entries in the promotions-table
    * 
    * @return type integer 
    */
    public function getAllRows()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__tkdclub_promotions');

        $db->setQuery($query);
        $sets = $db->loadObjectList();

        $allrows = count($sets);

        return $allrows;
    }

    /**
	 * Method to get the data that should be exported.
     * 
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{   
        $db	= Factory::getDBO();
        $query = $db->getQuery(true);

        $fields = array(
            'id',                 // 0
            'date',               // 1    
            'city',               // 2
            'type',               // 3
            'examiner_name',      // 4
            'examiner_address',   // 5
            'examiner_email',     // 6
            'promotion_state',    // 7
            'notes'               // 8
        );

        $pks = ArrayHelper::toInteger($pks);
		$query->select($db->quoteName($fields))->from($db->quoteName('#__tkdclub_promotions'));

        if (count($pks) > 0)
        {
            $query->whereIn($db->quoteName('id'), $pks);
        }
        else
        {
            $query->where($db->quoteName('id') . ' > 0');
        }

        $query->order('date DESC');

		$db->setQuery($query);
		$rows = $db->loadRowList();

        $headers = array(
            Text::_('COM_TKDCLUB_PROMOTION_ID'),               // id
            Text::_('COM_TKDCLUB_DATE'),                       // date
            Text::_('COM_TKDCLUB_PROMOTION_CITY'),             // city
            Text::_('COM_TKDCLUB_PROMOTION_TYPE'),             // type
            Text::_('COM_TKDCLUB_PROMOTION_EXAMINER'),         // examiner_name
            Text::_('COM_TKDCLUB_PROMOTION_EXAMINER_ADDRESS'), // examiner_address      
            Text::_('COM_TKDCLUB_PROMOTION_EXAMINER_EMAIL'),   // examiner_email  
            Text::_('JSTATUS'),                                // promotion_state
            Text::_('COM_TKDCLUB_NOTES')                       // notes
        );

		// return the results as an array of items, each consisting of an array of fields
		$content    = array($headers);	// header with column names
		$content	= array_merge( $content,  $rows);


        foreach ($content as $key => &$row)
		{
			if ($key > 0)
			{
				$row[3] == 'kup' ? $row[3] = Text::_('COM_TKDCLUB_KUP') : $row[3] = Text::_('COM_TKDCLUB_DAN');
				$row[7] == 1 ? $row[7] = Text::_('COM_TKDCLUB_PROMOTION_ACTIVE') : $row[7] = Text::_('COM_TKDCLUB_PROMOTION_INACTIVE');
			}
		}

		return $content;
	}
 }
