<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{confirmButton}"  : "[data-confirm-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{confirmButton} click"	: function()
		{
			$( '[data-conversation-leave-form]' ).submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_LEAVE_DIALOG_TITLE'); ?></title>
	<content>
		<form name="leave-conversation-form" method="post" data-conversation-leave-form>
			<p>
				<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_LEAVE_DIALOG_CONFIRMATION' );?>
			</p>

			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="controller" value="conversations" />
			<input type="hidden" name="task" value="leave" />
			<input type="hidden" name="<?php echo FD::token();?>" value="1" />
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-confirm-button type="button" class="btn btn-es-danger btn-sm"><?php echo JText::_('COM_EASYSOCIAL_LEAVE_CONVERSATION_BUTTON'); ?></button>
	</buttons>
</dialog>
