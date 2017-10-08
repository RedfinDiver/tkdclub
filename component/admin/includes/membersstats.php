<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

?>
  
<div class="container-fluid alert alert-info">
    <div class="span2">
        <h4 class="alert-heading"><?php echo JText::_('COM_TKDCLUB_CONFIG_MEMBERS'); ?></h4>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_ACTIVE') . ': ' . $this->memberdata->active; ?><br>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_SUPPORTER') . ': ' . $this->memberdata->support; ?><br>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_STATE_INACTIVE') . ': ' . $this->memberdata->support; ?><br> 
            <?php echo JText::_('COM_TKDCLUB_IN_DATABASE') . ': ' . $this->memberdata->allrows;; ?>
    </div>
    <div class="span2">
        <h4 class="alert-heading"><?php echo JText::_('COM_TKDCLUB_MEMBER_GENDER_DISTRIBUTION'); ?></h4>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_SEX_FEMALE') . ': ' . $this->memberdata->genderdist['female']; ?><br>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_SEX_MALE') . ': ' . $this->memberdata->genderdist['male']; ?>
    </div>
    <div class="span3">
        <h4 class="alert-heading"><?php echo JText::_('COM_TKDCLUB_MEMBER_AGE_DISTRIBUTION'); ?></h4>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_OLDEST')
                    . ': ' . $this->memberdata->oldest['name']
                    . ', ' . $this->memberdata->oldest['age_y']
                    . ' ' . JText::_('COM_TKDCLUB_YEARS') ; ?><br>
            <?php echo JText::_('COM_TKDCLUB_MEMBER_YOUNGEST')
                    . ': ' . $this->memberdata->youngest['name']
                    . ', ' . $this->memberdata->youngest['age_y']
                    . ' ' . JText::_('COM_TKDCLUB_YEARS') ; ?>
    </div>
</div>


 
