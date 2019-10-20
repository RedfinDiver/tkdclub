<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2018 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
JLoader::register('Helper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/tkdclub.php');

HTMLHelper::stylesheet(JUri::base() . '/components/com_tkdclub/assets/css/tkdclub.css');

?>

<div class="tkdclub">
    <h2><?php echo Text::_('COM_TKDCLUB_MEDAL') . ' ' .JComponentHelper::getParams('com_tkdclub')->get('club_name', Text::_('COM_TKDCLUB')); ?></h2>
    <div class="row-fluid">
        <div class="span2 center">
            <span class="tkdclub-goldmedal">
                <p><?php $gold = TkdClubModelMedals::getMedals(1); echo $gold; ?></p>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_GOLD') ?>
        </div>
        <div class="span2 center">
            <span class="tkdclub-silbermedal">
                <?php $silver = TkdClubModelMedals::getMedals(2); echo $silver; ?>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_SILVER') ?>
        </div>
        <div class="span2 center">
            <span class="tkdclub-bronzemedal">
                <?php $bronce = TkdClubModelMedals::getMedals(3); echo $bronce; ?>
            </span>  <?php echo Text::_('COM_TKDCLUB_MEDAL_BRONCE') ?>
        </div>
        <div class="span6 left allmedals">   
            <?php $all = $gold+$silver+$bronce; echo Text::_('COM_TKDCLUB_MEDAL_SUMMARY').': ' .'<strong>' . $all . '</strong>'; ?> 
        </div>
    </div>
<table class="table table-hover">
    <thead class="">
        <tr>
            <th class=""><?php echo Text::_('COM_TKDCLUB_DATE'); ?></th>
            <th style="text-align:left"><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP'); ?></th>
            <th style="text-align:left"><?php echo Text::_('COM_TKDCLUB_MEDAL_CHAMPIONSSHIP_TYPE'); ?></th>
            <th style="text-align:left"><?php echo Text::_('COM_TKDCLUB_MEDAL_CLASS'); ?></th>
            <th class=""><?php echo Text::_('COM_TKDCLUB_MEDAL_PLACING'); ?></th>
            <th style="text-align:left"><?php echo Text::_('COM_TKDCLUB_ATHLETS'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
        <?php if ($item->state == 1) : ?> <!-- only published items -->
        <tr class="row<?php echo $i % 2; ?>">
            <td style="text-align:center"><?php echo HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')); ?></td>
            <td><?php echo $this->escape($item->championship); ?></td>
            <td><?php echo $this->escape($item->type); ?></td>
            <td><?php echo $this->escape($item->class); ?></td>
            <td style="text-align:center"><?php echo $this->escape($item->placing); ?></td>
            <td class=""><?php echo Helper::getMembersNames($item->winner_ids, $this->memberlist); ?></td>             
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (!empty($this->items)) : ?>
<div class="pagination">
    <p class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
    </p>
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>