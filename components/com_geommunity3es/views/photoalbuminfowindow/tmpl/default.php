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
	if($this->albumphotocount>0)
		echo '<div class="infowindow_th"><a  href="'.$this->photoalbum_url.'"  ><img src="'.$this->thumb.'" alt="thumb" width="64" height="64" /></a></div>';
 ?>
    <div class="iw_textual">
    	<?php
		echo '<a  href="'.$this->photoalbum_url.'" ><strong>'.ucfirst($this->title).'</strong></a>
		<div data-user-id="'.$this->user_id.'" data-popbox="module://easysocial/profile/popbox"><strong>'.JText::_('COM_GEOMMUNITY3ES_CREATEDBY').'</strong> '.ucfirst($this->naming).'</div>
		<div class="badge" data-es-provide="tooltip" data-original-title="'.JText::_('COM_GEOMMUNITY3ES_PHOTOS').'"> <span class="geom-icon-picture"></span> '.$this->albumphotocount.' </div>
		<div class="badge" data-es-provide="tooltip" data-original-title="'.JText::_('COM_GEOMMUNITY3ES_HITS').'"> <span class="geom-icon-eye"></span> '.$this->hits.' </div>
		<div>'.$this->caption.' </div>
		<div class="btn-group btn-group-pending">
		<a class="btn btn-mini "  href="'.$this->photoalbum_url.'">
		<i class="geom-icon-folder"></i>'.JText::_('COM_GEOMMUNITY3ES_VISITALBUM').'</a>
		<a class="btn btn-mini" href="javascript:calcRoute('.$this->latitude.','.$this->longitude.');" title="'.JText::_('COM_GEOMMUNITY3ES_ROUTE').'" >
		<i class="icon-flag"></i></a>
		</div>';
		?>
    </div>
</div>