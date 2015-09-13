<?php
/**
 * @version     1.0.0
 * @package     com_geommunity3es
 * @copyright   Copyright (C) 2014. Adrien ROUSSEL Nordmograph.com All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nordmograph <contact@nordmograph.com> - http://www.nordmograph.com./extensions
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_geommunity3es/assets/css/geommunity3es.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'map.cancel') {
            Joomla.submitform(task, document.getElementById('map-form'));
        }
        else {
            
            if (task != 'map.cancel' && document.formvalidator.isValid(document.id('map-form'))) {
                
                Joomla.submitform(task, document.getElementById('map-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_geommunity3es&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="map-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_GEOMMUNITY3ES_TITLE_MAP', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">

                    			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
            <div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('def_lat'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('def_lat'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('def_lng'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('def_lng'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_users'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_users'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('users_addressfield_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('users_addressfield_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('profiletypes'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('profiletypes'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('onlineonly'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('onlineonly'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('usermarker'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('usermarker'); ?></div>
			</div>		
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_photoalbums'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_photoalbums'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_photos'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_photos'); ?></div>
			</div>
            <div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_videos'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_videos'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_groups'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_groups'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('groups_addressfield_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('groups_addressfield_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_events'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_events'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('events_addressfield_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('events_addressfield_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('show_easyblogs'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('show_easyblogs'); ?></div>
			</div>
            <div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('kmlurl'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('kmlurl'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('privacyaware'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('privacyaware'); ?></div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>


                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>