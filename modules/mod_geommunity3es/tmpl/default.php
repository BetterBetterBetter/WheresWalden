<?php
/**
 * @package     Geommunity3
 * @subpackage  mod_geommunity3es
 * @copyright   Copyright (C) 2010 - 2014 Nordmograph.com , Adrien ROUSSEL. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
echo '<div id="mapajaxloader"><img src="'.$juri.'components/com_geommunity3es/assets/img/loader.gif" alt="loading" width="16" height="11" /></div>';


if($show_users && $custom_filter )
{
	$values = modGeommunity3esHelper::getCustomFilterOptions( $custom_filter );
	if(count($values) >2 )
	{
		//var_dump($values);
		$filter_h = str_replace( 'px' , '' , $height ) - 50 ;
		if($profiletypes_filter)
			$filter_h = (str_replace( 'px' , '' , $height ) )/2 - 50 ;
		echo '<div id="custom_filter" class="gckn_filter" style="max-height:'.$filter_h.'px">
		<div class="gckn_filter_inner">';
		$app_id = $values[ count($values)-1 ];
		$title = $values[ count($values)-2 ];
		if( strstr($title,'COM_EASYSOCIAL_' ) )

		{
			
			$lang->load( 'com_easysocial', JPATH_ADMINISTRATOR , '', false);
			
		}
		$title = JText::_($title);
		echo '<h4>'.$title .'</h4>';
		
		echo '<div><span class="geom-icon-filter"></span> '.JText::_('COM_GEOMMUNITY3ES_ALLNONE').' <input type="checkbox" id="custom_allnone" class="custom_allnone" checked /></div>';
		$allnone = "jQuery('#custom_allnone').click(function () {
		if ( jQuery(this).is(':checked') ){
			jQuery('.custom_filter_box').prop(\"checked\", true);
		}
		else{
			jQuery('.custom_filter_box').removeAttr(\"checked\");
		}
		});";
		echo '<script>'.$allnone.'</script>';
		echo '<table class="table table-condensed table-striped table-hover">';
		
		unset($values[count($values)-1]); // remove the appid
		unset($values[count($values)-1]); // remove the title
		if(count($values) )
		{
			foreach($values as $value)
			{
				echo '<tr>
				<td width="1"><input type="checkbox" class="custom_filter_box" name="custom_filter" id="custom'.$value->data.'" value="'.$value->data.'" checked=""> </td>
				<td><label for="custom'.$value->data.'" onmouseover="bounceCustomMarkers(\'data\',\''.$value->data.'\',1);"  onmouseout="bounceCustomMarkers(\'data\',\''.$value->data.'\',0);" >';
				if($app_id=='63' OR strpos($custom_filter , 'GENDER' ) !==false) // Gender
				{
					if($value->data=='1')
						echo JText::_('COM_GEOMMUNITY3ES_MALE');
					elseif($value->data=='2')
						echo JText::_('COM_GEOMMUNITY3ES_FEMALE');	
					
				}
				else
					echo JText::_($value->data);
				
				echo '</label></td></tr>';	
			}
		}
		else 
			echo '<i class="geom-icon-cancel" data-es-provide="tooltip" data-original-title="Incorrect field unique key value in module options" ></i> ';
		echo '</table>';
		echo '</div>';
		echo '</div>';
	}
}



if( $show_users && $profiletypes_filter)
{
	$profiles = modGeommunity3esHelper::getProfileTypes($mapid);
	if(count($profiles)>1)
	{
		$filter_h = str_replace( 'px' , '' , $height ) - 50 ;
		if($custom_filter)
			$filter_h = (str_replace( 'px' , '' , $height ) )/2 - 50 ;
		echo '<div id="profiletypes_filter" class="gckn_filter" style="max-height:'.$filter_h.'px">
		<div class="gckn_filter_inner">';
		
		echo '<div><span class="geom-icon-filter"></span> '.JText::_('COM_GEOMMUNITY3ES_ALLNONE').' <input type="checkbox" id="profiletypes_allnone" class="profiletypes_allnone" checked /></div>';
		$allnone = "jQuery('#profiletypes_allnone').click(function () {
		if ( jQuery(this).is(':checked') ){
			jQuery('.profiletypes_filter_box').prop(\"checked\", true);
		}
		else{
			jQuery('.profiletypes_filter_box').removeAttr(\"checked\");
		}
		});";
		echo '<script>'.$allnone.'</script>';
		echo '<table class="table table-condensed table-striped table-hover">';
		foreach($profiles as $profile)
		{
			echo '<tr>
			<td width="1"><input type="checkbox" class="profiletypes_filter_box" name="profiletypes_filter" id="profile'.$profile->id.'" value="'.$profile->id.'" checked=""> </td>
			<td><label for="profile'.$profile->id.'" onmouseover="bounceProfileMarkers(\'profileid\',\''.$profile->id.'\',1);"  onmouseout="bounceProfileMarkers(\'profileid\',\''.$profile->id.'\',0);" >'.JText::_($profile->title).'</label></td></tr>';	
		}
		echo '</table>';
		echo '</div>';
		echo '</div>';
	}
}







	echo '<div id="integrations"  style="display:none">';	
	echo '<table><tr>';
	if($show_users)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_USERS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="user" value="user"><label for="user" onmouseover="bounceIntegrationMarkers(\'type\',\'user\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'user\',0);">
		<i class="geom-icon-user"></i>
		</label>
		
		
		</td>';
	}
	if($show_groups)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_GROUPS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="group" value="group"><label for="group" onmouseover="bounceIntegrationMarkers(\'type\',\'group\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'group\',0);">
		<i class="geom-icon-users"></i>
		</label>
		
		
		</td>';
	}
	if($show_events)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_EVENTS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="event" value="event"><label for="event" onmouseover="bounceIntegrationMarkers(\'type\',\'event\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'event\',0);">
		<i class="geom-icon-calendar"></i>
		</label>
		</td>';
	}
	if($show_photoalbums)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_PHOTOALBUMS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="photoalbum" value="photoalbum"><label for="photoalbum" onmouseover="bounceIntegrationMarkers(\'type\',\'photoalbum\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'photoalbum\',0);">
		<i class="geom-icon-folder"></i>
		</label>
		
		
		</td>';
	}
	if($show_photos)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_PHOTOS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="photo" value="photo"><label for="photo" onmouseover="bounceIntegrationMarkers(\'type\',\'photo\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'photo\',0);">
		<i class="geom-icon-picture"></i>
		</label>
		
		
		</td>';
	}
	if($show_videos)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_VIDEOS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="video" value="video"><label for="video" onmouseover="bounceIntegrationMarkers(\'type\',\'video\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'video\',0);">
		<i class="geom-icon-video"></i>
		</label>
		</td>';
	}
	if($show_easyblogs)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_EASYBLOGS').'">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="easyblog" value="easyblog"><label for="easyblog" onmouseover="bounceIntegrationMarkers(\'type\',\'easyblog\',1);"  onmouseout="bounceIntegrationMarkers(\'type\',\'easyblog\',0);">
		<i class="geom-icon-edit"></i>
		</label>
		</td>';
	}
	if($kmlurl)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_KML').'" class="anx_integ">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="kml" value="kml"><label for="kml"  >
		<i class="geom-icon-paperclip"></i>
		</label>
		</td>';
	}
	
	if($clustering==2)
	{
		echo '<td data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_CLUSTERING').'" class="anx_integ">
		<input checked type="checkbox" class="integrations_box" name="integrations" id="clustering" value="clustering" ><label for="clustering"  >
		<i class="geom-icon-wifi"></i>
		</label>
		</td>';
	}
	echo '</tr></table></div>';	


if($addmodule!='')
{
	$embed_mods = JModuleHelper::getModules( $addmodule );
	if(count($embed_mods)>0)
	{
		echo '<div id="embed_modules">';
		foreach ($embed_mods as $embed_mod ){	
			echo '<h3 class="module-title">'.$embed_mod->title.'</h3>';
			echo '<div>'.JModuleHelper::renderModule($embed_mod).'</div>';
		}
		if($closemodule)
		{
			echo '<div id="close_embedmodule"><span onclick="jQuery(\'#embed_modules\').hide();" class="geom-icon-cancel closebutton" data-es-provide="tooltip" data-original-title="'.JText::_('COM_GEOMMUNITY3ES_CLOSE').'"></span></div>';	
		}
		echo '</div>';
		
	}
}




echo '<a name="map_top"></a><div id="map-canvas" >
<div id="map_loader">';

echo '<img src="'.$juri.'components/com_geommunity3es/assets/img/loader.gif" alt="loader" width="16" height="11" /> '.JText::_('COM_GEOMMUNITY3ES_LOCATINGYOU').'</div>';
echo '</div><a name="map_bot"></a>';
if( $placeapifield )
	echo '<input id="pac-input" class="geomcontrols" type="text" placeholder="'.JText::_('COM_GEOMMUNITY3ES_SEARCHBOX').'" style="display:none;" >';
	
if( $marker_search )
	echo '<input id="marker-search" class="geomcontrols auto" type="text" placeholder="'.JText::_('COM_GEOMMUNITY3ES_MARKERSEARCH').'" style="display:none;" >';
	

	echo '<div id="directionspanel" class="directionspanel">';
	$default_modeoftravel='DRIVING';
	
		echo '<div class="closepanel">
		<i class="geom-icon-cancel" data-es-provide="tooltip" data-original-title="'.JText::_('COM_GEOMMUNITY3ES_CLOSEDIRPANEL').'" onclick="document.getElementById(\'directionspanel\').style.display=\'none\';directionsDisplay.setDirections({routes: []});"></i>
		</div>';

		echo '<input type="hidden" id="routelat" name="routelat" /><input type="hidden" id="routelng" name="routelng" />';
		echo '<div id="modeoftravel">
		<select id="mode" onchange="calcRoute(document.getElementById(\'routelat\').value,document.getElementById(\'routelng\').value,this.value);" class="form-control">';
		  echo '<option value="DRIVING" ';
		  if($default_modeoftravel=='DRIVING')
				echo 'selected';
		  echo '>'.JText::_('COM_GEOMMUNITY3ES_DRIVING').'</option>';
		  echo '<option value="WALKING" ';
		  if($default_modeoftravel=='WALKING')
			echo 'selected';
		  echo '>'.JText::_('COM_GEOMMUNITY3ES_WALKING').'</option>';
		  echo '<option value="BICYCLING" ';
		  if($default_modeoftravel=='BICYCLING')
			echo 'selected';
		  echo '>'.JText::_('COM_GEOMMUNITY3ES_BICYCLING').'</option>';
		echo '</select>';
		echo '</div>';
		echo '<div style="clear:both"></div>';
echo '</div>';
echo '<style>#map-canvas{width:'.$width.';height:'.$height.';}</style>';
if($fullscreen_button)
{
	echo '<div id="fs_toggler"><a class="btn btn-mini btn-primary" id="fullscreen_toggler" data-es-provide="tooltip" title="'.JText::_('COM_GEOMMUNITY3ES_TOGGLEFULLSCREEN').'"><i class="geom-icon-screen"></i></a></div>';
}