<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model class for list view 'promotions'
 */
class TkdClubModelPromotions extends JModelList
{       
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JModelLegacy
     * @since   1.0
     */
    public function __construct($config = array())
    {
            //currently not used, but does no harm
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array('promotion_id', 'date');
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
        $query->select('*')
                ->from($db->quoteName('#__tkdclub_promotions'));

        $sort = $this->getState('list.ordering');
        $order = $this->getState('list.direction');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;

    }

    /**
    * Method to get the number of all entries in the exams-table
    * 
    * @since   1.0
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
 }
