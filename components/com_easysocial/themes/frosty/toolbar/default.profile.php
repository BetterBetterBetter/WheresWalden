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
<li class="toolbarItem toolbar-profile" data-toolbar-profile
    data-popbox
    data-popbox-id="fd"
    data-popbox-component="es"
    data-popbox-type="toolbar"
    data-popbox-toggle="click"
    data-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-right' : 'bottom-left';?>"
    data-popbox-target=".toobar-profile-popbox"
>
	<a href="javascript:void(0);" class="dropdown-toggle_ login-link loginLink">
		<span class="es-avatar">
			<img src="<?php echo $this->my->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $this->my->getName() );?>" />
		</span>
		<b class="caret"></b>
	</a>

	<div style="display:none;" class="toobar-profile-popbox" data-toolbar-profile-dropdown>
		<ul class="popbox-dropdown-menu dropdown-menu-user" style="display: block;">
			<?php if ($this->my->hasCommunityAccess()) { ?>	
				<li class="divider"></li>
				<li>
					<h5 class="ml-10">
						<i class="ies-cog-2"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_HEADING_PREFERENCES' );?>
					</h5>
				</li>
				<li class="divider"></li>
				<li>
					<a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ) );?>">
						<i class="ies-cog-2 ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_ACCOUNT_SETTINGS' );?>
					</a>
				</li>
				<li>
					<a href="<?php echo FRoute::profile( array( 'layout' => 'editPrivacy' ) );?>">
						<i class="ies-key ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PRIVACY_SETTINGS' );?>
					</a>
				</li>
				<li>
					<a href="<?php echo FRoute::profile( array( 'layout' => 'editNotifications' ) );?>">
						<i class="ies-mail-3 ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_NOTIFICATION_SETTINGS' );?>
					</a>
				</li>
				<?php if ($this->template->get('show_browse_users', true) || $this->template->get('show_advanced_search', true)) { ?>
				<li class="divider"></li>
					<li>
						<h5 class="ml-10">
							<i class="ies-podcast"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_DISCOVER');?>
						</h5>
					</li>
					<li class="divider"></li>
					<?php if ($this->template->get('show_browse_users', true)) { ?>
					<li>
						<a href="<?php echo FRoute::users();?>">
							<i class="ies-users ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_BROWSE_USERS');?>
						</a>
					</li>
					<li class="divider"></li>
					<?php } ?>
					<?php if ($this->template->get('show_advanced_search', true)) { ?>
					<li>
						<a href="<?php echo FRoute::search(array('layout' => 'advanced'));?>">
							</i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ADVANCED_SEARCH');?>
						</a>
					</li>
					<?php } ?>
					<?php } ?>
				<?php } ?>	
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" class="logout-link" data-toolbar-logout-button>
					<i class="ies-switch ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_LOGOUT' );?>
				</a>
				<form class="logout-form" data-toolbar-logout-form>
					<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
					<input type="hidden" name="option" value="com_easysocial" />
					<input type="hidden" name="controller" value="account" />
					<input type="hidden" name="task" value="logout" />
					<?php echo $this->html( 'form.token' ); ?>
				</form>
			</li>
		</ul>
	</div>
</li>
