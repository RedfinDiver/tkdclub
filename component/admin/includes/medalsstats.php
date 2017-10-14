<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

?>


<div id="medals" class="btn-toolbar">
    <label>
        <span class="tkdclub-goldmedal">
            <p><?php $gold = TkdClubModelMedals::getMedals(1); echo $gold; ?></p>
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_GOLD') ?>
        
        <span class="tkdclub-silbermedal">
            <?php $silver = TkdClubModelMedals::getMedals(2); echo $silver; ?>                    
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_SILVER') ?>
        
        <span class="tkdclub-bronzemedal">
            <?php $bronce = TkdClubModelMedals::getMedals(3); echo $bronce; ?>

        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_BRONCE') ?>
        <?php echo ' | '; ?>
        <?php $all = $gold+$silver+$bronce; echo JText::_('COM_TKDCLUB_MEDAL_SUMMARY').' ' .'<strong>' . $all . '</strong>'; ?>  
    </label>
</div>