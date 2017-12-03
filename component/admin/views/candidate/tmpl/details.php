<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

// JLayout for standard handling of the details sidebar in administrator edit screens.
$title = $displayData->get('form')->getValue('title');
$published = $displayData->get('form')->getField('published');
JHtml::_('behavior.modal');
?>

<div class="span2">

    <h4><?php echo JText::_('JDETAILS');?></h4>

    <hr />

    <div id="ajax_wait" class="" href="#"></div>

    <fieldset class="form-vertical">        
        <div class="control-group">
            <div class="control-label">
                <?php echo $displayData->get('form')->getLabel('lastpromotion'); ?>
            </div>
            <div class="controls">
                <?php echo $displayData->get('form')->getInput('lastpromotion'); ?>
            </div>
        </div>
                                
        <div class="control-group">
            <div class="control-label">
                <?php echo $displayData->get('form')->getLabel('grade'); ?>
            </div>
            <div class="controls">
                <?php echo $displayData->get('form')->getInput('grade'); ?>
            </div>
        </div>
    </fieldset>

</div>