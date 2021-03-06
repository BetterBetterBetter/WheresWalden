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
<a href="javascript:void(0);" class="btn btn-clean btn-block dropdown-toggle btn-sm"
	data-profileFriends-button
	data-profileFriends-pending
	data-bs-toggle="dropdown"
>
	<i class="ies-user-add mr-5"></i>
	<span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT' );?></span>
	<i class="ies-arrow-down ies-small"></i>
</a>

<ul class="dropdown-menu dropdown-arrow-topleft dropdown-friends" data-profileFriends-dropdown>
	<li>
		<a href="javascript:void(0);" data-profileFriends-cancelRequest>
			<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_CANCEL_FRIEND_REQUEST' );?>
		</a>
	</li>
</ul>
