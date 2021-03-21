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

<div class="row text-center">
        <div class="col-md-3">
            <div class="card alert-info">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x gold"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?></h5>
                    <div><?php echo $this->medaldata['placings'][1]; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card alert-info">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x silver"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?></h5>
                    <div><?php echo $this->medaldata['placings'][2] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card alert-info">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x bronce"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?></h5>
                    <div><?php echo $this->medaldata['placings'][3] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card alert-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col"><span class="fas fa-medal fa-3x gold"></span></div>
                        <div class="col"><span class="fas fa-medal fa-3x silver"></span></div>
                        <div class="col"><span class="fas fa-medal fa-3x bronce"></span></div>
                    </div>
                    
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY') ?></h5>
                    <div><?php echo $this->medaldata['sum'] ?></div>
                </div>
            </div>
        </div>
    </div>
</div>