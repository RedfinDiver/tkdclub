<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class TkdclubModelStatistics extends JModelLegacy
{
    public function paytrainings($member_id)
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        
        $db = JFactory::getDbo();
 
        $query = $db->getQuery(true);

        // Fields to update.
        $fields = array(
            $db->quoteName('published') . ' = 1'
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('payment_state') . ' = 0',
            $db->quoteName('payment_state') . ' = ' . $member_id
        );

        $query->update($db->quoteName('#__tkdclub_trainings'))->set($fields)->where($conditions);

        $db->setQuery($query);

        return $db->execute();
    }
}