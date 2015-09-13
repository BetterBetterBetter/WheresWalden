<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="iw_container">
	<?php 
	echo '<div class="infowindow_th">
	<a  href="'.$this->photo_url.'"  >
	<img src="'.$this->thumb.'" alt="thumb" width="64" height="64" />
	</a>
	</div>'; 
	?>
    <div class="iw_textual">
    	<?php
		echo '<a  href="'.$this->photo_url.'" ><strong>'.ucfirst($this->title).'</strong></a>
		<div data-user-id="'.$this->user_id.'" data-popbox="module://easysocial/profile/popbox"><strong>'.JText::_('COM_GEOMMUNITY3ES_ADDEDBY').'</strong> '.ucfirst($this->naming).'</div>
		<div><strong>'.JText::_('COM_GEOMMUNITY3ES_IN').'</strong> <a href="'.$this->album_url.'">'.$this->albumname.'</a> </div>
		<div>'.$this->caption.' </div>
		<div class="btn-group btn-group-pending">
		<a class="btn btn-mini "  href="'.$this->photo_url.'">
		<i class="geom-icon-picture"></i>'.JText::_('COM_GEOMMUNITY3ES_ENLARGEPHOTO').'</a>
		<a class="btn btn-mini" href="javascript:calcRoute('.$this->latitude.','.$this->longitude.');" title="'.JText::_('COM_GEOMMUNITY3ES_ROUTE').'" >
		<i class="geom-icon-flag"></i></a></div>';
		?>
    </div>
</div>