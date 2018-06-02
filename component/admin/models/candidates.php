<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Model-class for list view 'candidates'
 *
 */
class TkdClubModelCandidates extends JModelList
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
        $config['filter_fields'] = 
            array('date', 'c.date',
                  'promotion', 'a.promotion_id',
                  'candidate', 'a.id_candidate',
                  'type', 'c.type',
                  'state', 'a.test_state');

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
	 */
    protected function populateState($ordering = 'c.date', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '');
        $this->setState('filter.search', $search);

        $promotion = $this->getUserStateFromRequest($this->context.'.filter.promotion', 'filter_promotion', '');
        $this->setState('filter.promotion', $promotion);

        $candidate = $this->getUserStateFromRequest($this->context.'.filter.candidate', 'filter_candidate', '');
        $this->setState('filter.candidate', $candidate);

        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
        $this->setState('filter.state', $state);

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
    */
    protected function getStoreId($id = '')
    {
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.promotion');
        $id	.= ':'.$this->getState('filter.candidate');
        $id	.= ':'.$this->getState('filter.type');
        $id	.= ':'.$this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // columns from candidates table
        $query->select('a.*');
        $query->from($db->quoteName('#__tkdclub_candidates', 'a'));

        // adding columns from members table
        $query->select('b.firstname,b.lastname,b.birthdate,b.citizenship,b.street,b.zip,b.city,b.memberpass,b.grade,b.lastpromotion');
        $query->join('LEFT', $db->quoteName('#__tkdclub_members'). ' AS b ON a.id_candidate=b.member_id');  

        // addings columns from promotion table
        $query->select('c.date, c.city, c.type');
        $query->join('LEFT', $db->quoteName('#__tkdclub_promotions'). 'AS c ON a.id_promotion=c.promotion_id');

        // only showing candidates for active promotions
        $query->where('c.promotion_state = 1');

        // adding filters to query
        $candidate = $this->getState('filter.candidate');
        if (!empty($candidate))
        {
            $query->where('a.id_candidate = '. (int) $candidate);
        }

        $promotion = $this->getState('filter.promotion');
        if (!empty($promotion))
        {
            $query->where('a.id_promotion = '. (int) $promotion);
        }

        $type = $this->getState('filter.type');
        if (!empty($type))
        {
            $query->where('c.type = ' . $db->quote($type));
        }

        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where('a.test_state = ' . (int) $state);
        }

        $search = $this->getState('filter.search');
        if (!empty($search))
        {

            $search = $db->quote('%'. $db->escape($search, true).'%');
            $query->where('id LIKE' .$search
                        .'OR b.lastname LIKE' .$search
                        .'OR b.firstname LIKE' .$search
                        .'OR b.memberpass LIKE' .$search
                        .'OR b.birthdate LIKE' .$search);
        }

        // Join over the users for the checked out user.
		$query->select('u.name AS editor')
        ->join('LEFT', '#__users AS u ON u.id=a.checked_out');

        $sort = $this->getState('list.ordering', 'c.date');
        $order = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($sort).' '.$db->escape($order));

        return $query;

    }

    /**
    * Method to get the number of all entries in the candidates-table
    * 
    * @return type integer 
    */
    public function getAllRows()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
                ->from($db->quoteName('#__tkdclub_candidates'));
        $db->setQuery($query);
        $allrows = $db->loadResult();

        return (int) $allrows;
    }

    /**
     * Get all statistics data for candidates
     */
    public function getCandidateData()
    {
        return TRUE;
    }

    /**
	 * Method to get the data that should be exported.
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
		$pklist = implode(',', $pks);

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);

        // select fields from members table
        $query->select('b.memberpass,b.firstname,b.lastname,b.birthdate,b.sex,b.citizenship,b.zip,b.city,b.street');
        $query->from($db->quoteName('#__tkdclub_members', 'b'));  

        // select fields from candidates table
        $query->select('a.grade_achieve');
        $query->join('LEFT', $db->quoteName('#__tkdclub_candidates', 'a') . ' ON a.id_candidate = b.member_id');

        // only selected items in the list
        $query->where('a.id IN ('.$pklist.')');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

        $headers = array(
            JText::_('COM_TKDCLUB_MEMBER_PASS'),                        // b.memberpass
            JText::_('COM_TKDCLUB_MEMBER_FIRSTNAME'),                   // b.firstname
            JText::_('COM_TKDCLUB_MEMBER_LASTNAME'),                    // b.lastname
            JText::_('COM_TKDCLUB_MEMBER_BIRTHDATE'),                   // b.birthdate
            JText::_('COM_TKDCLUB_MEMBER_SEX'),                         // b.sex
            JText::_('COM_TKDCLUB_MEMBER_CITIZENSHIP'),                 // b.citizenship       
            JText::_('COM_TKDCLUB_MEMBER_ZIP'),                         // b.zip  
            JText::_('COM_TKDCLUB_MEMBER_CITY'),                        // b.city   
            JText::_('COM_TKDCLUB_MEMBER_STREET'),                      // b.street 
            JText::_('COM_TKDCLUB_CANDIDATE_PROMOTION_GRADE_ACHIEVE')   // a.grade_achive  
        );

		// return the results as an array of items, each consisting of an array of fields
		$content	= array($headers);	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
    }
 
 }