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
	if($this->image!='')
	{
		echo '<div class="infowindow_th"><a  href="'.$this->easyblog_url.'"  >
		<img src="'.$this->image.'" alt="thumb" width="64" height="64" />
		</a></div>'; 
	}
	?>
    <div class="iw_textual">
    	<?php
		echo '<a  href="'.$this->easyblog_url.'" ><strong>'.ucfirst($this->title).'</strong></a>
		<div data-user-id="'.$this->user_id.'" data-popbox="module://easysocial/profile/popbox">'.JText::_('COM_GEOMMUNITY3ES_ADDEDBY').' '.ucfirst($this->naming).'</div>
		<div>'.JText::_('COM_GEOMMUNITY3ES_IN').' <a href="'.$this->easyblog_caturl.'">'.$this->cattitle.'</a> </div>
		<div>'.JHtml::date( substr($this->publish_up,0,10) , JText::_('DATE_FORMAT_LC3')).'</div>
		<div class="btn-group btn-group-pending">
		<a class="btn btn-mini "  href="'.$this->easyblog_url.'">
		<i class="icon-edit"></i>'.JText::_('COM_GEOMMUNITY3ES_READBLOGPOST').'</a>
		<a class="btn btn-mini" href="javascript:calcRoute('.$this->latitude.','.$this->longitude.');" title="'.JText::_('COM_GEOMMUNITY3ES_ROUTE').'" >
		<i class="icon-flag"></i></a></div>';
		?>
    </div>
</div>