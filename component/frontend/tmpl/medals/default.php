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
use Redfindiver\Component\Tkdclub\Site\Model\MedalsModel;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry();
$wa->useStyle('com_tkdclub.tkdclub-admin');

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
    <div class="row m-3">
        <div class="col-2">
            <span class="tkdclub-goldmedal">
                <p><?php $gold = MedalsModel::getMedals(1); echo $gold; ?></p>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?>
        </div>
        <div class="col-2">
            <span class="tkdclub-silbermedal">
                <?php $silver = MedalsModel::getMedals(2); echo $silver; ?>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?>
        </div>
        <div class="col-2">
            <span class="tkdclub-bronzemedal">
                <?php $bronce = MedalsModel::getMedals(3); echo $bronce; ?>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?>
        </div>
        <div class="col-6">   
            <?php $all = $gold+$silver+$bronce; echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY').': ' .'<strong>' . $all . '</strong>'; ?> 
        </div>
    </div>
<table class="table table-hover">
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
            <td class=""><?php echo TkdclubHelper::getMembersNames($item->winner_ids, $this->memberlist); ?></td>             
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (!empty($this->items)) : ?>
    <div><?php echo $this->pagination->getPagesCounter(); ?></div>
    <?php echo $this->pagination->getPagesLinks(); ?>
<?php endif; ?>