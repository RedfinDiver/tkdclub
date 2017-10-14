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
        <span class="goldMedal">
            <p><?php $gold = TkdClubModelMedals::getMedals(1); echo $gold; ?></p>
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDALS_GOLD') ?>
        
        <span class="silberMedal">
            <?php $silver = TkdClubModelMedals::getMedals(2); echo $silver; ?>                    
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDALS_SILVER') ?>
        
        <span class="bronzeMedal">
            <?php $bronce = TkdClubModelMedals::getMedals(3); echo $bronce; ?>

        </span>  <?php echo JText::_('COM_TKDCLUB_MEDALS_BRONCE') ?>
        <?php echo ' | '; ?>
        <?php $all = $gold+$silver+$bronce; echo JText::_('COM_TKDCLUB_MEDALS_SUMMARY').' ' .'<strong>' . $all . '</strong>'; ?>  
    </label>
</div>