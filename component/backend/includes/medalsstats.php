<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$gold = isset($this->medaldata['placings']['1']) ? $this->medaldata['placings']['1'] : 0;
$silver = isset($this->medaldata['placings']['2']) ? $this->medaldata['placings']['2'] : 0;
$bronce = isset($this->medaldata['placings']['3']) ? $this->medaldata['placings']['3'] : 0;

?>


<div class="m-2 row">
    <div class="col-12">
        <div class="card alert-info">
            <div class="card-body">
                <h4 class="card-title"><?php echo Text::_('COM_TKDCLUB_MEDAL_DISTRIBUTION'); ?></h4>
                <div>
                    <span class="fas fa-medal gold"></span>
                    <?php echo $gold . ' x ' . Text::_('COM_TKDCLUB_MEDAL_GOLD'); ?>
                </div>
                <div>
                    <span class="fas fa-medal silver"></span>
                    <?php echo $silver . ' x ' . Text::_('COM_TKDCLUB_MEDAL_SILVER'); ?>
                </div>
                <div>
                    <span class="fas fa-medal bronce"></span>
                    <?php echo $bronce . ' x ' . Text::_('COM_TKDCLUB_MEDAL_BRONCE'); ?>
                </div>
                <div>
                <?php echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY').': ' . $this->medaldata['sum']; ?>
                </div>
            </div>
        </div>
    </div>
</div>