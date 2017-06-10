<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
/**
 * JLayout for standard handling of the details sidebar in administrator edit screens.
 **/
$title = $displayData->get('form')->getValue('title');
$published = $displayData->get('form')->getField('member_state');
?>
<div class="span2">
    <h4><?php echo JText::_('JDETAILS');?></h4>
			
        <fieldset class="form-vertical">

            <div class="control-group">
                <div class="control-label">
                    <?php echo $displayData->get('form')->getLabel('mamber_state'); ?>
                </div>
                <div class="controls">
                    <?php echo $displayData->get('form')->getInput('member_state'); ?>
                </div> 
            </div>

        </fieldset>
</div>