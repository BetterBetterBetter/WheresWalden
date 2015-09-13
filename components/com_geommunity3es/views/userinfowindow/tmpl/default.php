<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
$user 	= JFactory::getUser();
$doc 	= JFactory::getDocument();
$juri 	= JURI::Base();

?>
<div  class="infowin_container">
    <div id="fd" class="popbox es popbox-profile top-left"  style="position:relative;">
        <div class="popbox-content" data-popbox-content="">
            
            <div class="profile-details">
                <div class="profile-title">
                    <a href="<?php echo $this->profile_url ; ?>">
                        <?php echo ucfirst($this->naming) ; ?>
                    </a>
                </div>
                <div class="profile-desp">
                    <?php if( $this->daysdiff>1)
                        echo sprintf( JText::_('COM_GEOMMUNITY3ES_LASTLOGIN') , $this->daysdiff ) ; 
                    elseif($this->daysdiff==1)
                        echo JText::_('COM_GEOMMUNITY3ES_LASTLOGINYESTERDAY' ) ;
                    elseif($this->daysdiff<1)
                        echo JText::_('COM_GEOMMUNITY3ES_LASTLOGINTODAY' ) ;
                    ?>	
                </div>
            </div>
            
            <div class="popbox-cover">
                <div style="background-image: url('<?php echo $this->cover; ?>'); background-position: <?php echo $this->x; ?>% <?php echo ($this->y * 100) ; ?>%; background-size: cover" class="es-photo-scaled es-photo-wrap">
                </div>
            </div>
    
            <a class="es-avatar es-avatar-medium popbox-avatar" href="<?php echo $this->profile_url ; ?>">
                <img alt="<?php echo ucfirst($this->naming) ; ?>" src="<?php echo $this->avatar ; ?>">
            </a>
            <div class="es-online-status es-online-status-mini">
                <i data-es-provide="tooltip" title="<?php echo JText::_( 'COM_GEOMMUNITY3ES_'.strtoupper($this->onlinestatus).'LINESTATUS' )  ?>" data-placement="top" class="hasTooltip es-status-<?php echo $this->onlinestatus ?>" >
                </i>
            </div>
    
            <div class="popbox-info">
                <ul class="list-unstyled popbox-items">
                    <li>
                        <div class="popbox-item-info">
                
                                <div class="popbox-item-text">
                                    <?php echo JText::_('COM_GEOMMUNITY3ES_FRIENDS') ?>
                                </div>
                                <div class="popbox-item-total">
                                <?php  echo $this->friendcount; ?>
                                </div>
                        
                        </div>
                    </li>
                    <?php 
                     if($this->photos_enabled==1){ ?>
                    <li>
                        <div class="popbox-item-info">
                    
                                <div class="popbox-item-text">
                                    <?php echo JText::_('COM_GEOMMUNITY3ES_ALBUMS') ?>
                                </div>
                                <div class="popbox-item-total">
                                    <?php echo $this->photoalbumscount ?>
                                </div>
                        
                        </div>
                    </li>
                   <?php } 
                   if($this->followers_enabled==1){ ?>
                    <li>
                        <div class="popbox-item-info">
                    
                                <div class="popbox-item-text">
                                    <?php echo JText::_('COM_GEOMMUNITY3ES_FOLLOWERS') ?>
                                </div>
                                <div class="popbox-item-total">
                                    <?php echo $this->followerscount ?>
                                </div>
                        
                        </div>
                    </li>
                   <?php } 
                    ?> 
                </ul>
            </div>
    
            <div class="popbox-footer">
            <?php if($this->userid != $user->id){ ?>
                <div class="pull-right">
               
                    <div class="btn-group btn-group-pending">
                        
                       <?php  if(!$this->isfriend) { ?>
                            <a class="btn-es btn-friends" href="javascript:void(0);" data-es-friends-add data-es-friends-id="<?php echo $this->userid;?>" >
                            <i class="ies-plus ies-small mr-5"></i> <?php echo JText::_('COM_GEOMMUNITY3ES_ADDASAFRIEND') ?>
                             </a>
                        <?php   } 
                            elseif($this->isfriend=='-1'){?>
                            <a class="btn-es btn-friends"  >
                                    <i class="ies-refresh ies-small mr-5"></i> <?php echo JText::_('COM_GEOMMUNITY3ES_FRIENDREQUESTED') ?>
                            <?php   } ?>
                        </a>
                    </div>
                    
                    <div class="btn-group btn-group-message">
                        <a class="btn-es btn-message" href="javascript:void(0);"  data-es-conversations-compose  data-es-conversations-id="<?php echo $this->userid; ?>"  >
                         
                             <i class="ies-mail-3 ies-small mr-5"></i> <?php echo JText::_('COM_GEOMMUNITY3ES_MESSAGE') ?>
                    
                        </a>
                    </div>
                    
                    <div class="btn-group btn-group-route">
                        <a class="btn-es btn-message" href="javascript:void(0);"  onclick="javascript:calcRoute(<?php echo $this->latitude ?>,<?php echo $this->longitude ?> );">
                         
                              <i class="ies-flag ies-small mr-5"></i> <?php echo JText::_('COM_GEOMMUNITY3ES_ROUTE') ?>
                        </a>
                    </div>
                   
                </div>
                <?php } ?>
            </div>
            
        </div>
    </div>
</div>
<style>
.infowin_container{
width:422px;height:217px;
}
@media screen and (max-width: 700px) {
	body div#fd.es a [class^="ies-"], body div#fd.es a [class*=" ies-"] {
display: none;
text-decoration: inherit;
}
	.infowin_container{
		width:232px;height:127px;
	}
	body div#fd.es.popbox.popbox-profile {
width: 98% !important;
left: 0 !important;
right: 0 !important;

	}
body div#fd.es.popbox.popbox-profile .btn-group-route.btn-group {
display:none;
}
	
	body div#fd.es.popbox.popbox-profile .popbox-cover {
display: block;
width: 100%;
height: 70px;
background: #333;
position: absolute;
}

body div#fd.es.popbox.popbox-profile {

height:115px !important;
background-color: #fff !important;
border-radius: 1px;
padding: 0;
border: 1px solid rgba(0,0,0,0.2);
-webkit-box-shadow: 0 5px 10px rgba(0,0,0,0.2);
box-shadow: 0 5px 10px rgba(0,0,0,0.2);
}

body div#fd.es .es-online-status .es-status-on, body div#fd.es .es-online-status .es-status-off {
position: absolute;
top: 10px;
right: 0;
width: 22px;
height: 22px;
display: inline-block;
border: 3px solid #fff;
border-radius: 50%;
}

	
	.popbox-info{
		display:none;
	}
	.popbox-cover{
		height:30px;
		
	}
	.profile-details{
		bottom:0;
		top:10px;	
	}

	.popbox-avatar{
		bottom:0;
		top:10px;
	}
span i{
	display:none;
}
	.btn-group-message{
		left: 2px;
display: block;	
padding: 5px;
	}
	div#fd.es.popbox.popbox-profile{
		height:100px;
	}
}
@media screen and (max-width: 550px) {
	body div#fd.es.popbox.popbox-profile {
height: 115px !important;

}
	.infowin_container {
width: 132px;
height: 127px;
}
	body div#fd.es.popbox.popbox-profile .popbox-footer {
display:none;
}
body div#fd.es.popbox.popbox-profile .popbox-avatar img {
width: 32px;
height: 32px;
background: #fff;
display: block;
max-width: none;
}
body div#fd.es.popbox.popbox-profile .popbox-avatar {
position: absolute;
z-index: 2;
left: 10px;
bottom: 50px;
border-radius: 2px;
overflow: visible;
width: 36px;
height: 36px;
border: 2px solid #fff;
}
body div#fd.es.popbox.popbox-profile .profile-details {
position: absolute;
z-index: 2;
bottom: 75px;
left: 0;
color: white;
padding: 40px 0 5px 10px;
width: 100%;
height: 61px;
background-image: -webkit-linear-gradient(rgba(255,255,255,0),rgba(0,0,0,0.1) 30%,rgba(0,0,0,0.5));
background-image: linear-gradient(rgba(255,255,255,0),rgba(0,0,0,0.1) 30%,rgba(0,0,0,0.5));
background-repeat: no-repeat;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ffffff',endColorstr='#7f000000',GradientType=0);
}
body div#fd.es.popbox.popbox-profile .es-online-status {
position: absolute;
bottom: 118px;
left: 50px;
right: auto;
top: auto;
}
 .profile-desp {
display:none;
}
body div#fd.es.popbox.popbox-profile {
height:65px !important;
}
.infowin_container{
height:77px;
}
}
</style>