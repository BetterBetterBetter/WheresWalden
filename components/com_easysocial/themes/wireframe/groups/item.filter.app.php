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
<li class="widget-filter custom-filter <?php echo $filter->favicon ? 'has-fonticon' : '';?> <?php if ($context == $filter->alias) { ?>active<?php } ?>"
	style="<?php if ($hide) { ?>display: none;<?php } ?>"
	data-es-group-filter
	data-dashboardSidebar-menu
	data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
	data-id="<?php echo $group->id; ?>"
	data-fid="<?php echo '0'; ?>"
>
	<a href="javascript:void(0);"
		data-id="<?php echo $filter->alias;?>"
		data-url="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias(), 'app' => $filter->alias ) );?>"
		data-title="<?php echo $this->html( 'string.escape' , $filter->title ); ?>"
		data-es-group-app-filter
		class="data-dashboardfeeds-item"
	>
		<span class="es-app-filter">
			<?php if( $filter->image ){ ?>
				<img src="<?php echo $filter->image;?>" alt="<?php echo JText::sprintf( 'COM_EASYSOCIAL_STREAM_FILTERS_FILTER_TYPE' , $filter->title );?>" width="16" class="mr-5" />
			<?php } ?>

			<?php if( $filter->favicon ){ ?>
				<span class="es-app-favicon" style="border: 1px solid <?php echo $filter->favicon->color;?>;background:<?php echo $filter->favicon->color;?>">
					<span>
						<i class="<?php echo $filter->favicon->icon;?>"></i>
					</span>
				</span>
			<?php } ?>

			<?php if( $filter->icon ){ ?>
				<i class="<?php echo $filter->icon;?>"></i>
			<?php } ?>

			<span class="filter-title"><?php echo $filter->title;?></span>
		</span>
	</a>
</li>
