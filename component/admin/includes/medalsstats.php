<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="container-fluid alert alert-info">
    <div class="span3">
        <h4 class="alert-heading"><?php echo JText::_('COM_TKDCLUB_MEDAL_DISTRIBUTION'); ?></h4>
        <span class="tkdclub-goldmedal">
            <p><?php echo $this->medaldata['placings']['1']; ?></p>
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_GOLD') ?>
        
        <span class="tkdclub-silbermedal">
            <?php echo $this->medaldata['placings']['2']; ?>                    
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_SILVER') ?>
        
        <span class="tkdclub-bronzemedal">
            <?php echo $this->medaldata['placings']['1']; ?>
        </span>  <?php echo JText::_('COM_TKDCLUB_MEDAL_BRONCE') ?>
    </div>

    <div class="span3">
        <?php echo JText::_('COM_TKDCLUB_MEDAL_SUMMARY').': ' .'<strong>' . $this->medaldata['sum'] . '</strong>'; ?>  
    </div>
</div>