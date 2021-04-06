<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry();
$wa->useStyle('com_tkdclub.tkdclub-site');

$params = $this->state->get('parameters.menu')
?>

<div class="tkdclub">
    <?php if ($params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo  $params->get('page_heading') ? $params->get('page_heading') : Factory::getApplication()->getMenu()->getActive()->title; ?>
            </h1>
        </div>
	<?php endif; ?>
    <div class="row text-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x gold"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?></h5>
                    <div><?php echo $this->medaldata['placings'][1]; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x silver"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?></h5>
                    <div><?php echo $this->medaldata['placings'][2] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <span class="fas fa-medal fa-3x bronce"></span>
                    <h5 class="card-title mt-2"><?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?></h5>
                    <div><?php echo $this->medaldata['placings'][3] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
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
<table class="table table-hover mt-3">
    <thead class="">
        <tr>
            <th><?php echo Text::_('COM_TKDCLUB_DATE'); ?></th>
            <th><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'); ?></th>
            <th ><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP_TYPE'); ?></th>
            <th><?php echo Text::_('COM_TKDCLUB_MEDAL_CLASS'); ?></th>
            <th><?php echo Text::_('COM_TKDCLUB_MEDAL_PLACING'); ?></th>
            <th style="text-align:left"><?php echo Text::_('COM_TKDCLUB_ATHLETS'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <?php if ($item->state == 1) : ?> <!-- only published items -->
            <tr class="row<?php echo $i % 2; ?>">
                <td class="text-center"><?php echo HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')); ?></td>
                <td><?php echo $this->escape($item->championship); ?></td>
                <td><?php echo $this->escape($item->type); ?></td>
                <td><?php echo $this->escape($item->class); ?></td>
                <td class="text-center"><?php echo $this->escape($item->placing); ?></td>
                <?php $winner_ids = array($item->winner_1, $item->winner_2, $item->winner_3) ?>
                <td><?php echo TkdclubHelper::getMembersNames($winner_ids, $this->memberlist); ?></td>             
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (!empty($this->items)) : ?>
    <div><?php echo $this->pagination->getPagesCounter(); ?></div>
    <?php echo $this->pagination->getPagesLinks(); ?>
<?php endif; ?>