<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$gold = isset($this->medaldata['placings']['1']) ? $this->medaldata['placings']['1'] : 0;
$silver = isset($this->medaldata['placings']['2']) ? $this->medaldata['placings']['2'] : 0;
$bronce = isset($this->medaldata['placings']['3']) ? $this->medaldata['placings']['3'] : 0;

?>

<div class="container-fluid alert alert-info">
    <div class="span3">
        <h4 class="alert-heading"><?php echo Text::_('COM_TKDCLUB_MEDAL_DISTRIBUTION'); ?></h4>
        <span class="tkdclub-goldmedal">
            <p><?php echo $gold; ?></p>
        </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?>
        
        <span class="tkdclub-silbermedal">
            <?php echo $silver; ?>                    
        </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?>
        
        <span class="tkdclub-bronzemedal">
            <?php echo $bronce; ?>
        </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?>
    </div>

    <div class="span3">
        <?php echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY').': ' .'<strong>' . $this->medaldata['sum'] . '</strong>'; ?>  
    </div>
</div>