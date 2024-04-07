<?php
/**
 * @package    Taekwondo Club
 * @copyright  Copyright (C) 2021 Markus Moser. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Redfindiver\Component\Tkdclub\Administrator\Helper\TkdclubHelper;

/** @var \Redfindiver\Component\Tkdclub\Administrator\View\Members\HtmlView $this */
HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('behavior.multiselect');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_tkdclub.iban')
    ->useScript('com_tkdclub.members-format-iban')
    ->useScript('com_tkdclub.taskhandling')
    ->useStyle('com_tkdclub.tkdclub-admin');

/**
 * Get some variables
 */
$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$columns   = 10;

?>

<form action="<?php echo Route::_('index.php?option=com_tkdclub&view=members'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
                    <div class="m-2">
                        <?php echo Text::sprintf('COM_TKDCLUB_ENTRIES', $this->total, $this->allrows); ?>
                    </div>
                    <div id="membersstats" class="d-none container">
                        <div class="p-3 row">
                            <div class="col">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <h4 class="card-title"><?php echo Text::_('COM_TKDCLUB_CONFIG_MEMBERS'); ?></h4>
                                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE') . ': ' . $this->memberdata->active; ?><br>
                                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER') . ': ' . $this->memberdata->support; ?><br>
                                        <?php echo Text::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE') . ': ' . $this->memberdata->inactive; ?><br> 
                                        <?php echo Text::_('COM_TKDCLUB_IN_DATABASE') . ': ' . $this->memberdata->allrows; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <h4><?php echo Text::_('COM_TKDCLUB_MEMBER_GENDER_DISTRIBUTION'); ?></h4>
                                        <?php echo Text::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') . ': ' . $this->memberdata->genderdist['female']; ?><br>
                                        <?php echo Text::_('COM_TKDCLUB_MEMBER_SEX_MALE') . ': ' . $this->memberdata->genderdist['male']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card alert-info">
                                    <div class="card-body">
                                        <h4><?php echo Text::_('COM_TKDCLUB_MEMBER_AGE_DISTRIBUTION'); ?></h4>
                                        <?php echo Text::sprintf('COM_TKDCLUB_MEMBER_OLDEST', $this->memberdata->oldest['name'], $this->memberdata->oldest['age_y']) ?><br>
                                        <?php echo Text::sprintf('COM_TKDCLUB_MEMBER_YOUNGEST', $this->memberdata->youngest['name'], $this->memberdata->youngest['age_y']) ?><br>
                                        <?php echo Text::_('COM_TKDCLUB_STATISTIC_AVERAGE_AGE') . ': ' . $this->memberdata->average_age?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HtmlHelper::_('grid.checkall'); ?>
                                </td>
                                <th  class="w-1 text-center"><?php echo Text::_('JSTATUS'); ?></th>
                                <th width="">
                                    <?php echo HtmlHelper::_('grid.sort', 'COM_TKDCLUB_MEMBER_NAME', 'lastname', $listDirn, $listOrder); ?>
                                </th>
                                <th><?php echo Text::_('COM_TKDCLUB_HINTS'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_PHONE'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_EMAIL'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_PASS'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_GRADE'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_LAST_PROMOTION'); ?></th>
                                <th><?php echo Text::_('COM_TKDCLUB_MEMBER_IBAN'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                    $canEdit    = $user->authorise('core.edit',       'com_tkdclub.member.' . $item->id);
                                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                    $canEditOwn = $user->authorise('core.edit.own',   'com_tkdclub.member.' . $item->id) && $item->created_by == $userId;
                                    $canChange  = $user->authorise('core.edit.state', 'com_tkdclub.member.' . $item->id) && $canCheckin;
                                    ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center"><?php echo HtmlHelper::_('grid.id', $i, $item->id); ?></td>

                                    
                                    <td class="w-1 text-center">
                                        <?php
                                                //creating array for altnatives tooltip text on member_state column
                                                $states = array(
                                                    'active' => array('COM_TKDCLUB_MEMBER_STATE_ACTIVE', 'fas fa-user member-active'),
                                                    'inactive' => array('COM_TKDCLUB_MEMBER_STATE_INACTIVE', 'fas fa-user-slash member-inactive'),
                                                    'support' => array('COM_TKDCLUB_MEMBER_STATE_SUPPORTER', 'fas fa-user member-support')
                                                );
                                                $iconClass = $states[$item->member_state]
                                                ?>
                                        <span class="tbody-icon" title="<?php echo Text::_($iconClass[0]) ?>">
                                            <span class="<?php echo $iconClass[1] ?>" aria-hidden="true"></span>
                                        </span>
                                    </td>
                                    <th scope="row">
                                        <div>
                                            <?php if ($item->checked_out) : ?>
                                                <?php echo HtmlHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'members.', $canCheckin); ?>
                                            <?php endif; ?>
                                            <?php if ($canEdit) : ?>
                                                <a href="<?php echo Route::_("index.php?option=com_tkdclub&task=member.edit&id=" . (int) $item->id); ?>">
                                                <?php echo $this->escape($item->firstname) . ' ' . $this->escape($item->lastname); ?></a>
                                            <?php else : ?>
									            <?php echo $this->escape($item->firstname) . ' ' . $this->escape($item->lastname)?>
								            <?php endif; ?>
                                        </div>
                                    </th>
                                    <td>
                                        <?php
                                        // Functions
                                        if ($item->functions) : ?>
                                            <?php
                                                $functions = json_decode($item->functions, true);
                                                $translation = TkdclubHelper::getFunctionTranslation($item->sex);
                                                $fstring = '';
                                                foreach ($functions as $function) {
                                                    array_key_exists($function, $translation) ? $fstring .= $translation[$function] . "<br>" : $fstring .= $function . "<br>";
                                                }
                                            ?>
                                            <span class="far fa-star hasPopover" data-bs-original-title="<?php echo Text::_('COM_TKDCLUB_MEMBER_FUNCTIONS') ?>" data-bs-content="<?php echo $fstring ?>"></span>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Notes
                                        if (!empty($item->notes_taekwondo) || !empty($item->notes_personel) || !empty($item->notes_clubdata)) : ?>
                                            <?php $notes = $item->notes_taekwondo . "\n" . $item->notes_personel . "\n" . $item->notes_clubdata?>
                                            <span class="far fa-clipboard hasPopover" data-bs-original-title="<?php echo Text::_('COM_TKDCLUB_NOTES') ?>" data-bs-content="<?php echo $this->escape($notes) ?>"></span>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Attachments
                                        if ($item->attachments) : ?>
                                            <?php
                                                $attachments = json_decode($item->attachments, true);
                                                $astring = '';
                                                foreach ($attachments as $file => $filename) {
                                                    $astring .= $filename . '<br>';
                                                }
                                                
                                            ?>
                                            <span class="far fa-folder hasPopover" data-bs-original-title="<?php echo Text::_('COM_TKDCLUB_MEMBER_ATTACHMENTS') ?>" data-bs-content="<?php echo $astring ?>"></span>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Image 
                                        if (!empty($item->image)) : ?>
                                            <span class="far fa-user hasPopover" data-bs-original-title="<?php echo Text::_('COM_TKDCLUB_MEMBER_PICTURE') ?>" data-bs-content="<?php echo "<img src='" . Uri::root() . $item->image . "'>" ?>"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $this->escape($item->phone); ?></td>
                                    <td><?php echo $this->escape($item->email); ?></td>
                                    <td><?php if ($item->memberpass) :?>
                                            <?php echo $this->escape($item->memberpass); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            if (!$item->grade) {
                                                echo "";
                                            } else {
                                                echo $this->escape($item->grade); 
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if ($item->lastpromotion == '0000-00-00' || is_null($item->lastpromotion)) {
                                                echo '';
                                            } else {
                                                echo HtmlHelper::_('date', $item->lastpromotion, Text::_('DATE_FORMAT_LC4'));
                                            }
                                        ?>
                                    </td>
                                    <td class="iban"><?php echo $this->escape($item->iban); ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo $this->pagination->getListFooter(); ?>

                <div>
                    <input type="hidden" id="task" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <?php echo HtmlHelper::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>
</form>