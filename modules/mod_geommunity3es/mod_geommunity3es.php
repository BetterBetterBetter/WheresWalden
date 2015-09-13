<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_geommunity3es
 * @copyright   Copyright (C) 2005 - 2014 Nordmograph.com. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once( dirname(__FILE__).'/helper.php' );
$juri	= JURI::Base();
$jinput 	= JFactory::getApplication()->input;
$input_lat	= $jinput->get('lat','');
$input_lng	= $jinput->get('lng','');
$doc	= JFactory::getDocument();
$db		= JFactory::getDBO();
$user	= JFactory::getUser();
$lang 	= JFactory::getLanguage();
$lang->load( 'com_geommunity3es', JPATH_SITE , '', false);
$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
jimport( 'joomla.filesystem.file' );
if( !JFile::exists( $file ) )
{
	return;
}
require_once( $file );// Include main Easysocial engine
$modules 	= Foundry::modules( 'mod_easysocial_toolbar' );
$modules->loadComponentScripts();
if( !Foundry::exists() )
{
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}
$config 	= Foundry::config();
$naming = $config->get( 'users.displayName' );  // username or realname
if($naming=='realname')
	$naming = 'name';
if(JFactory::getApplication()->input->get('option')!='com_easysocial')
{
	$doc->addStyleSheet( $juri.'components/com_easysocial/themes/wireframe/styles/base.min.css' );
	$doc->addStyleSheet( $juri.'components/com_easysocial/themes/wireframe/styles/style.min.css' ); 
	$doc->addStyleSheet( $juri.'components/com_easysocial/themes/wireframe/styles/more.min.css' );	
}
// component params
$cparams 			= JComponentHelper::getParams('com_geommunity3es');
$visitor_addressfield	= $cparams->get('visitor_addressfield','12');
$unitsystem			= $cparams->get('unitsystem','METRIC');
$js_apikey			= $cparams->get('js_apikey');  
$js_client			= $cparams->get('js_client'); 	
$js_signature		= $cparams->get('js_signature');
//$doc->addStylesheet($juri.'components/com_geommunity3es/assets/css/fontello.css');
echo '<link rel="stylesheet" href="'.$juri.'components/com_geommunity3es/assets/css/fontello.css" type="text/css" />';

// module params
$mapid 				= $params->get('mapid','1');
$width 				= $params->get('width','100%');
$height				= $params->get('height','400px');
$html5				= $params->get('html5',1);
$zoom				= $params->get('zoom','10');
$min_zoom			= $params->get('min_zoom','5');
$max_zoom			= $params->get('max_zoom','15');
$map_type			= $params->get('map_type','ROADMAP');
$scrollwheel 		= $params->get('scrollwheel','false');
$streetview 		= $params->get('show_streetview',1);
$placeapifield 		= $params->get('address_search',1);
$marker_search 		= $params->get('marker_search',1);
$stylez				= $params->get('stylez');
$profiletypes_filter= $params->get('profiletypes_filter', 1 );
$custom_filter		= strtoupper( $params->get('custom_filter' ) );
$addmodule			= $params->get('addmodule' );
$addmodule_position	= $params->get('addmodule_position','RIGHT_CENTER' );
$closemodule		= $params->get('closemodule',1 );
//$parallax			= $params->get('parallax',0 );
$fullscreen_button	= $params->get('fullscreen_button',1 );
$clustering			= $params->get('clustering',2 );
$cluster_lib		= $params->get('cluster_lib',1 );
$drop_markers		= $params->get('drop_markers',1 );

$integrations_position		= $params->get('integrations_position', 'BOTTOM_LEFT' );
$teleportation_position		= $params->get('teleportation_position' , 'TOP_LEFT');
$markersearch_position		= $params->get('markersearch_position' , 'TOP_CENTER');
$ajaxloader_position		= $params->get('ajaxloader_position' , 'BOTTOM_CENTER');
$customfilter_position		= $params->get('customfilter_position' , 'RIGHT_TOP');
$profiletypefilter_position	= $params->get('profiletypefilter_position', 'RIGHT_BOTTOM' );

$include_jquery		= $params->get('include_jquery',0 );
$jquery_url			= $params->get('jquery_url','//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js' );
$jqueryui_url		= $params->get('jqueryui_url','//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js' );
$jqueryui_css		= $params->get('jqueryui_css','//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
if($include_jquery)
	$doc->addScript($jquery_url);

//map params
$q = "SELECT title,
 def_lat,
 def_lng,
 show_users,
 users_addressfield_id,
 profiletypes,
 onlineonly,
 usermarker,
 show_photoalbums,
 show_photos,
 show_groups,
 groups_addressfield_id,
 show_events,
 show_easyblogs,
 events_addressfield_id,
 kmlurl, privacyaware
  FROM #__geommunity3es_maps WHERE id='".$mapid."' ";
$db->setQuery($q);
$map_params 			= $db->loadObject();
$integrations 			= 0;
$title 					= $map_params->title;
$def_lat				= $map_params->def_lat;
$def_lng				= $map_params->def_lng;
if($user->id>0)
{
	$coords = modGeommunity3esHelper::getMemberCoords($visitor_addressfield);
	if($coords[0]!='' && $coords[0]!='')
	{
		$def_lat = $coords[0];
		$def_lng = $coords[1];
	}
}  
if($input_lat && $input_lng)
{
	$def_lat = $input_lat;
	$def_lng = $input_lng;
	$zoom 	= $max_zoom;
}
$show_users				= $map_params->show_users;
$users_addressfield_id	= $map_params->users_addressfield_id;
$profiletypes 			= $map_params->profiletypes;
$onlineonly				= $map_params->onlineonly;
$usermarker 			= $map_params->usermarker;
$show_photoalbums		= $map_params->show_photoalbums;
$show_photos			= $map_params->show_photos;
$show_videos 			= 0 ; //////// To come
$show_groups			= $map_params->show_groups;
$groups_addressfield_id	= $map_params->groups_addressfield_id;
$show_events			= $map_params->show_events;
$show_easyblogs			= $map_params->show_easyblogs;
$events_addressfield_id = $map_params->events_addressfield_id;
$kmlurl					= $map_params->kmlurl;
$privacyaware			= $map_params->privacyaware;
if($show_users)
	$integrations++;
if($show_photoalbums)
	$integrations++;
if($show_photos)
	$integrations++;
if($show_videos)
	$integrations++;	
if($show_groups)
	$integrations++;
if($show_events)
	$integrations++;
if($show_easyblogs)
	$integrations++;
if($kmlurl)
	$integrations++;
if($clustering==2)
	$integrations++;	
$api_url= '//maps.googleapis.com/maps/api/js?key='.$js_apikey.'&sensor=false';
if($placeapifield)
	$api_url .= '&libraries=places';
if($js_client !='' && $js_signature !='' )
	$api_url .= '&client='.$js_client.'&signature='.$js_signature;		
$doc->addScript( $api_url );	

if($clustering>0)
{
	if($cluster_lib==1 )
		$doc->addScript('//google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js');
	elseif($cluster_lib==2)
		$doc->addScript( $juri.'modules/mod_geommunity3es/js/markerclusterer.js' );
}

$doc->addStylesheet($juri.'modules/mod_geommunity3es/css/style.css');

$script =  "var map; 
			var seenCoordinates 	= [];
			var markers 			= [];
			var marker 				;
			var html				= [];
			var directionsDisplay	= null;
      		var directionsService 	= new google.maps.DirectionsService();
			var mc 					=[];
			var clustate 			= 1;
			var input_lat 			= '".$input_lat."' ;
			var input_lng 			= '".$input_lng."';
			function initialize()   
			{  
  				directionsDisplay = new google.maps.DirectionsRenderer();
				function loadLoc(){
					if( $html5 && navigator.geolocation  && input_lat=='' ){
						navigator.geolocation.getCurrentPosition(
							function(position) {							   
								var loc = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
								loadmap(loc, true);
							},
							function (error) { 
						  		loadmap(new google.maps.LatLng('".$def_lat."', '".$def_lng."') , false );
							}
						);	
					}
					else
						loadmap(new google.maps.LatLng('".$def_lat."', '".$def_lng."') , false );
				}
				loadLoc();
		 		function loadmap(loc , guessed ){
					var myOptions = {  
						zoom: ".$zoom.",  
						minZoom: ".$min_zoom.",
						maxZoom: ".$max_zoom.",
						scrollwheel: ".$scrollwheel." ,
						center: loc,  
						mapTypeId: google.maps.MapTypeId.".$map_type.",
						scroll:{x:jQuery(window).scrollLeft(),y:jQuery(window).scrollTop()},
						mapTypeControl: true,
						streetViewControl: ".$streetview."
					}  
					var map = new google.maps.Map(document.getElementById('map-canvas'), myOptions); 
					directionsDisplay.setMap(map);
					directionsDisplay.setPanel(document.getElementById('directionspanel'));";
					
					if($clustering>0)
					{
						if($cluster_lib==1)
							$script .="mc = new MarkerClusterer(map, markers, {averageCenter: true, gridSize: 40});";
						elseif($cluster_lib==2)
						{
							$script .= "var mcOptions = {gridSize: 40, maxZoom: 20};
								mc = new MarkerClusterer(map, markers ,mcOptions);";
							
						}
					}

				if($clustering==2)
				{
					$script .="jQuery('input:checkbox.integrations_box').on('change', function(){
					var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						return this.value;
					}).get();
					if(jQuery.inArray('clustering' , integrations)=='-1' && clustate == 1){
				  		mc.clearMarkers();
						markers = []; 
						seenCoordinates = []; 
						if($show_users && jQuery.inArray('user' , integrations)>=0)
							loadUserMarkersFromCurrentBounds(map);
						if($show_groups && jQuery.inArray('group' , integrations)>=0)
							loadGroupMarkersFromCurrentBounds(map);
						if($show_photoalbums && jQuery.inArray('photoalbum' , integrations)>=0)
							loadPhotoalbumMarkersFromCurrentBounds(map);
						if($show_photos && jQuery.inArray('photo' , integrations)>=0)
							loadPhotoMarkersFromCurrentBounds(map);
						if($show_events && jQuery.inArray('event' , integrations)>=0)
							loadEventMarkersFromCurrentBounds(map);
						if($show_easyblogs && jQuery.inArray('easyblog' , integrations)>=0)
							loadEasyblogMarkersFromCurrentBounds(map);
						if($show_videos && jQuery.inArray('video' , integrations)>=0)
							loadVideoMarkersFromCurrentBounds(map);
						clustate = 0;
					}
					else if(jQuery.inArray('clustering' , integrations)>=0 && clustate == 0){
						mc.clearMarkers();
						buildMultiInfowindowCluster(map);
						clustate = 1;
					}		
				});";
		
	}
				/*if($parallax)
				{
					$script .= "var offset=jQuery(map.getDiv()).offset();
       				map.panBy(((myOptions.scroll.x-offset.left)/4),((myOptions.scroll.y-offset.top)/4));
      				google.maps.event.addDomListener(window, 'scroll', function(){
     					var scrollY=jQuery(window).scrollTop(),
         				scrollX=jQuery(window).scrollLeft(),
          				scroll=map.get('scroll');
					  	if(scroll){
							map.panBy(-((scroll.x-scrollX)/4),-((scroll.y-scrollY)/4));
					  	}
      					map.set('scroll',{x:scrollX,y:scrollY})
					});";
				}*/

			if($stylez!=''){
				$script .="var stylez = ".$stylez.";
					var styledMapOptions = {
							name: '1'
					}
					var MapType = new google.maps.StyledMapType( stylez, styledMapOptions);
					map.mapTypes.set('1', MapType);
					map.setMapTypeId('1');";	
			}
			if($kmlurl!=''){
				$script .= "var kmlayers = [];
							var kmlisloaded = 1;
							function unsetLayer(element){
								element.setMap(null);
								}
							function setLayer(element){
								element.setMap(map);
							}";
			if(strpos($kmlurl,',')){
				$layers = explode(",",$kmlurl);
				for ($i=0, $n=count($layers); $i < $n; $i++){
					$script .= "var Layer".$i." = new google.maps.KmlLayer('".$layers[$i]."',{ suppressInfoWindows: 0 , preserveViewport: true });
						Layer".$i.".setMap(map);
						kmlayers.push(Layer".$i.");";	
				}
				$script .="jQuery('input:checkbox.integrations_box').on('change', function(){
					var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						return this.value;
					}).get();
					if(jQuery.inArray('kml' , integrations)=='-1'){
						kmlayers.forEach(unsetLayer);
						kmlisloaded	= 0;
					}
					else if(jQuery.inArray('kml' , integrations)>=0 && kmlisloaded==0){
						kmlayers.forEach(setLayer);
						kmlisloaded	= 1;
					}		
				});";			
			}
			else{
				$script .= "var Layer = new google.maps.KmlLayer('".$kmlurl."',{ suppressInfoWindows: 0 , preserveViewport: true });
						Layer.setMap(map);
						var kmlisloaded = 1;
						jQuery('input:checkbox.integrations_box').on('change', function(){
					 		var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
							return this.value;
						}).get();
						if(jQuery.inArray('kml' , integrations)=='-1'){
					 		Layer.setMap(null);
							kmlisloaded	= 0;
						}
						else if(jQuery.inArray('kml' , integrations)>=0 && kmlisloaded==0){
							Layer.setMap(map);
						}
					});";		
				} 	
			}
			$script .= "var mapajaxloader = /** @type {HTMLInputElement} */( document.getElementById('mapajaxloader'));
				map.controls[google.maps.ControlPosition.".$ajaxloader_position."].push(mapajaxloader);				
				jQuery('#mapajaxloader').css('margin-top', '5px');
				jQuery('#mapajaxloader').bind('ajaxStart', function(){
					jQuery(this).show();
				}).bind('ajaxStop', function(){
					jQuery(this).hide();
				});
				";
			if($fullscreen_button)
			{	
				$script .= "var fullscreen_tog = /** @type {HTMLInputElement} */( document.getElementById('fs_toggler'));
				map.controls[google.maps.ControlPosition.TOP_RIGHT].push(fullscreen_tog);
				jQuery('#fs_toggler').css('padding-top', '5px');
				var fs = 0;
				
				function setfullscreen(){
					jQuery('#map-canvas').toggleClass('setfullscreen');
					jQuery('body').css('overflow','hidden');
			 		jQuery('#map-canvas').css('position','fixed');
					fs = 1;
					google.maps.event.trigger(map, 'resize');
				}
				function unsetfullscreen(){				
					jQuery('#map-canvas').toggleClass('setfullscreen');
					jQuery('body').css('overflow','');
					jQuery('#map-canvas').css('position','relative');
					fs = 0;
					google.maps.event.trigger(map, 'resize');
				}
				jQuery('#fullscreen_toggler').click(function() {
			  		if(fs==0){
						setfullscreen();
					}
					else if(fs==1){
						unsetfullscreen();
					}
				});
				jQuery(document).keyup(function(e) {
					if (e.keyCode == 27 && fs==1){					
							unsetfullscreen();
					}
				});";
			}
			if( $show_users && $profiletypes_filter)	{
				$script .= "var profiletypes_filter = /** @type {HTMLInputElement} */( document.getElementById('profiletypes_filter'));
				map.controls[google.maps.ControlPosition.".$profiletypefilter_position."].push(profiletypes_filter);
				jQuery('#profiletypes_filter').show();";
			}
			if( $show_users &&  $custom_filter)
			{
				$script .= "var custom_filter = /** @type {HTMLInputElement} */( document.getElementById('custom_filter'));
				map.controls[google.maps.ControlPosition.".$customfilter_position."].push(custom_filter);
				jQuery('#custom_filter').show();";
			}
			if( $integrations>1)
			{
				$script .= "var integrations = /** @type {HTMLInputElement} */( document.getElementById('integrations'));
				map.controls[google.maps.ControlPosition.".$integrations_position."].push(integrations);
				jQuery('#integrations').show();";
			}
			if($addmodule)
			{
				$script .= "var embed_modules = /** @type {HTMLInputElement} */( document.getElementById('embed_modules'));
				map.controls[google.maps.ControlPosition.".$addmodule_position."].push(embed_modules);
				jQuery('#embed_modules').show();";
			}
			if($marker_search)
			{
				//$doc->addScript($jqueryui_url);
				$doc->addStyleSheet($jqueryui_css);
				echo '<script src="'.$jqueryui_url.'"></script>';
				$script .= "var marker_search = /** @type {HTMLInputElement} */(
				document.getElementById('marker-search'));
				map.controls[google.maps.ControlPosition.".$markersearch_position."].push(marker_search);
				document.getElementById('marker-search').style.display ='';
				jQuery(function() {
					jQuery('.auto').autocomplete({
						source: function( request, response )
								{                      
									var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
									  return this.value;
									}).get();
							jQuery.ajax(
									{ 
										url: 'index.php?option=com_geommunity3es&view=markersearch&format=json',
										data: {
												term: request.term, 
												mapid: ".$mapid.",  
											  },        
										type: 'POST',  
										dataType: 'json',                                                                                        
										success: function( data ) 
										{
											response( jQuery.map( data, function( item ) 
											{
												return{
														label: item.label+': '+item.title ,
														value: item.title ,
														coords: item.coords    ,
														integration: item.integration,
														contentid:item.contentid       
													   }
											}));
										}
									});                
								},
						minLength: 3,
						select: function(event, ui) { 
									var coordz = ui.item.coords.split(',');
									var center = new google.maps.LatLng( coordz[0],coordz[1] );
									map.setCenter( center );
									map.setZoom(20);
									var wait = setTimeout( bounceSearchResultMarker( ui.item.integration , ui.item.contentid) ,1000);
									clearTimeout(wait);
								}
					
					});				
				});";
			}
			if( $placeapifield)	{				
				$script .= "// Create the search box and link it to the UI element.
			  		var input = /** @type {HTMLInputElement} */(
				  	document.getElementById('pac-input'));					
			  		map.controls[google.maps.ControlPosition.".$teleportation_position."].push(input);
					document.getElementById('pac-input').style.display ='';
			  		var searchBox = new google.maps.places.SearchBox(
					/** @type {HTMLInputElement} */(input));
				  	google.maps.event.addListener(searchBox, 'places_changed', function() {
						var places = searchBox.getPlaces();
						var placemarkers = [];
						for (var i = 0, placemarker; placemarker = placemarkers[i]; i++) {
						  placemarker.setMap(null);
						}
						var bounds = new google.maps.LatLngBounds();
						for (var i = 0, place; place = places[i]; i++) {
					  		bounds.extend(place.geometry.location);
						}
			    		map.fitBounds(bounds);
						map.setZoom(12);
  					});";
			}
			$script .= "google.maps.event.addListener(map, 'idle', mapSettleTime);
						google.maps.event.addListener(map,'dragend',function(event) {
							jQuery('#marker-search').val('');
							jQuery('#pac-input').val('');
						});
			
			
			
			";
			if($show_users && $profiletypes_filter)
			{	
				$script .= " jQuery('input:checkbox.profiletypes_filter_box').on('change', function(){
					 	dropOutofCustomProfilesMarkers(map);
					 	 var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						  return this.value;
						}).get();
						if(jQuery.inArray('user' , integrations)>=0)
					 		loadUserMarkersFromCurrentBounds(map);
					});
					jQuery('input:checkbox.profiletypes_allnone').click('change', function(){
					if(jQuery(this).is(':checked')){    // checked
						var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						 	return this.value;
						}).get();
						if(jQuery.inArray('user' , integrations)>=0)
							loadUserMarkersFromCurrentBounds(map);
						if($clustering>0 && clustate==1){
							mc.clearMarkers();
							mc = new MarkerClusterer(map, markers, {averageCenter: true, gridSize: 40});
						}
					} 
					else {   // unchecked
						var delmarkers = [];
						for (var i = 0 ; i < markers.length; i++)  {
							if (!markers[i]) {continue};
							if ( markers[i].integration =='user') {
									// remove from the map
									markers[i].setMap(null);
								
									// remove from the record of seen markers
									coordHash = markers[i].integration+markers[i].contentid;
									if(seenCoordinates[coordHash]) {
										seenCoordinates[coordHash] = null;
									}
									// remove from the markers array
									delmarkers.push( coordHash );
								}
							}
						for (var i = 0 ; i < delmarkers.length; i++)  {
							for (var j = 0 ; j < markers.length; j++)  {
								if(delmarkers[i] == markers[j].integration+markers[j].contentid)	
								{
									markers.splice(j, 1);
									if($clustering>0 && clustate==1){
										mc.clearMarkers();
										mc =  new MarkerClusterer(map, markers, {averageCenter: true, gridSize: 40});
									}
								}
							}			
						}		
					}
				});";
			}
			if($show_users &&  $custom_filter)
			{
				$script .= "jQuery('input:checkbox.custom_filter_box').on('change', function(){
					 	dropOutofCustomProfilesMarkers(map);
						 var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						  return this.value;
						}).get();
						if(jQuery.inArray('user' , integrations)>=0)
					 		loadUserMarkersFromCurrentBounds(map);
					});
					jQuery('input:checkbox.custom_allnone').click('change', function(){
					if(jQuery(this).is(':checked')){
						 var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
						  return this.value;
						}).get();
						if(jQuery.inArray('user' , integrations)>=0){
							loadUserMarkersFromCurrentBounds(map);
						}
					} else {
						var delmarkers = [];
						for (var i = 0 ; i < markers.length; i++)  {
							if (!markers[i]) {continue};
							if ( markers[i].integration =='user' && markers[i].data!==null && markers[i].data!='' ) {
									// remove from the map
									markers[i].setMap(null);
								
									// remove from the record of seen markers
									coordHash = markers[i].integration+markers[i].contentid;
									if(seenCoordinates[coordHash]) {
										seenCoordinates[coordHash] = null;
									}
									// remove from the markers array
									delmarkers.push( coordHash );
								}
							}
						for (var i = 0 ; i < delmarkers.length; i++)  {
							for (var j = 0 ; j < markers.length; j++)  {
								if(delmarkers[i] == markers[j].integration+markers[j].contentid)	
								{
									markers.splice(j, 1);
									if($clustering>0 && clustate==1){
										mc.clearMarkers();
										mc =  new MarkerClusterer(map, markers, {averageCenter: true, gridSize: 40});
									}
								}
							}			
						}	
					}	
				});";
			}
					
			$script .= "jQuery('input:checkbox.integrations_box').on('change', function(){
					 var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
					  return this.value;
					}).get();
					dropOutofIntegrationsMarkers(map);
					if (jQuery.inArray('user' , integrations)>=0)
					 	loadUserMarkersFromCurrentBounds(map);
					if (jQuery.inArray('group' , integrations)>=0)
						loadGroupMarkersFromCurrentBounds(map);
					if (jQuery.inArray('photoalbum' , integrations)>=0)
						loadPhotoalbumMarkersFromCurrentBounds(map);
					if (jQuery.inArray('photo' , integrations)>=0)
						loadPhotoMarkersFromCurrentBounds(map);
						
					if (jQuery.inArray('video' , integrations)>=0)
						loadVideoMarkersFromCurrentBounds(map);
					if (jQuery.inArray('event' , integrations)>=0)
						loadEventMarkersFromCurrentBounds(map);
					if (jQuery.inArray('easyblog' , integrations)>=0)
						loadEasyblogMarkersFromCurrentBounds(map);
					
					if (jQuery.inArray('user' , integrations)=='-1'){
						jQuery('#custom_filter').hide('fast');
						jQuery('#profiletypes_filter').hide('fast');
					}
					else
					{
						jQuery('#custom_filter').show('slow');
						jQuery('#profiletypes_filter').show('slow');	
					}
				});
				function mapSettleTime() {
					var usersUpdater;
					var groupsUpdater;
					var eventsUpdater;
					var photoalbumsUpdater;
					var photosUpdater;
					var videosUpdater;
					var easyblogsUpdater;
					var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
					  return this.value;
					}).get();
						 if($show_users && jQuery.inArray('user' , integrations)>=0){
						 	usersUpdater =setTimeout(loadUserMarkersFromCurrentBounds(map) ,200);
						 }
						if($show_groups && jQuery.inArray('group' , integrations)>=0){
							groupsUpdater =setTimeout(loadGroupMarkersFromCurrentBounds(map) ,200);
						}
						if($show_photoalbums && jQuery.inArray('photoalbum' , integrations)>=0){
							photoalbumsUpdater =setTimeout(loadPhotoalbumMarkersFromCurrentBounds(map) ,200);	
						}
						if($show_photos && jQuery.inArray('photo' , integrations)>=0){
							photosUpdater =setTimeout(loadPhotoMarkersFromCurrentBounds(map) ,200);
						}
						if($show_videos && jQuery.inArray('video' , integrations)>=0){
							videosUpdater =setTimeout(loadVideoMarkersFromCurrentBounds(map) ,200);
						}
						if($show_events && jQuery.inArray('event' , integrations)>=0){
							eventsUpdater =setTimeout(loadEventMarkersFromCurrentBounds(map) ,200);
						}
						if($show_easyblogs && jQuery.inArray('easyblog' , integrations)>=0){
							easyblogsUpdater =setTimeout(loadEasyblogMarkersFromCurrentBounds(map) ,200);
						}
						
						if($show_users && jQuery.inArray('user' , integrations)>=0){
							clearTimeout(usersUpdater);
						}
						if($show_groups && jQuery.inArray('group' , integrations)>=0){
							clearTimeout(groupsUpdater);
						}
						if($show_photoalbums && jQuery.inArray('photoalbum' , integrations)>=0){
							clearTimeout(photoalbumsUpdater);
						}
						if($show_photos && jQuery.inArray('photo' , integrations)>=0){
							clearTimeout(photosUpdater);
						}
						if($show_videos && jQuery.inArray('video' , integrations)>=0){
							clearTimeout(videosUpdater);
						}
						if($show_events && jQuery.inArray('event' , integrations)>=0){
							clearTimeout(eventsUpdater);
						}
						if($show_easyblogs && jQuery.inArray('easyblog' , integrations)>=0){
							clearTimeout(easyblogsUpdater);
						}
					}
				}
			} 
			// remove markers that aren't currently visible
			var multi_infowindow = new google.maps.InfoWindow();
			function dropOutofBoundsMarkers(map) {	
				var mapBounds = map.getBounds();
				var delmarkers = [];
				for (var i = 0 ; i < markers.length; i++)  {
					if (!markers[i]) {continue};
					if (!mapBounds.contains(markers[i].getPosition())) {
					  	// remove from the map
					  	markers[i].setMap(null);
					  	// remove from the record of seen markers
					  	coordHash = markers[i].integration+markers[i].contentid;
					  	if(seenCoordinates[coordHash]) {
							seenCoordinates[coordHash] = null;
					  	}
					  	// remove from the markers array
					  	markers.splice(i, 1);
						if($clustering>0 && clustate==1 ){
							if(markers.length==0)
								mc.clearMarkers();
							if(i==markers.length-1){
								buildMultiInfowindowCluster(map);
							}
						}
					}
				}
			}
			function buildMultiInfowindowCluster(map){
				multi_infowindow.close();
				mc.clearMarkers();
				mc =  new MarkerClusterer(map, markers);
				google.maps.event.addListener(mc, 'click', 			
					function(cluster) {
						var clickedMarkers = cluster.getMarkers();			
						// check if all cluster markers share exact same location
						for(var j=1; j<clickedMarkers.length; j++){  
							if(!clickedMarkers[j].position.equals( clickedMarkers[0].position ) )
								return false;		
						}
						var compileHtml = '';
						compileHtml += '<div class=\'fullmulticontent\' >';
						compileHtml += '<strong>'+clickedMarkers.length+' ".JText::_('COM_GEOMMUNITY3ES_MARKERSHERE') ."</strong>';
						compileHtml += '<div class=\'multicontent\'>';		
						compileHtml += '</div>';
						compileHtml += '</div>';	
						multi_infowindow.setContent(compileHtml);
						 multi_infowindow.setPosition(clickedMarkers[0].position);
						 multi_infowindow.open(map );
						 for (var j=0; j < clickedMarkers.length;j++){
						 //	compiledMarkers.push( clickedMarkers[j].integration+clickedMarkers[j].contentid );
							jQuery.ajax({
								url: 'index.php?option=com_geommunity3es&view='+ clickedMarkers[j].integration +'infowindow&format=raw' ,
								data: {contentid: clickedMarkers[j].contentid ,
								latitude:clickedMarkers[j].position.lat(),
								longitude:clickedMarkers[j].position.lng()
							},					
							success: function(Html){			
								jQuery( 'div.multicontent' ).append( '<hr />'+Html+'<div style=\'clear:both\'></div>' );
							}
						});	
					}
				}); 
			}";
		
	
		if($show_users)
		{
			$script .="function loadUserMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();";
				if( $profiletypes_filter )
				{
					$script .= "var profiles = jQuery('input:checkbox:checked.profiletypes_filter_box').map(function () {
					  return this.value;
					}).get();";
				}
				if( $custom_filter )
				{
					$script .= "var customvalues = jQuery('input:checkbox:checked.custom_filter_box').map(function () {
					  return this.value;
					}).get();";
				}
				$script .= "jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=usermarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng,";
			if( $profiletypes_filter)
				$script .= "profiles: JSON.stringify(profiles),";
			else
				$script .= "profiles: '0',";
			if( $custom_filter )
			{
				$script .= "customfilter:'".$custom_filter."',
						customvalues:JSON.stringify(customvalues)";
			}
			$script .= "},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populateUserMarkers(data, map);
    				}
				});	
			}
			function populateUserMarkers(pointData ,map, status, xhr) {		
				var userinfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].".$naming.";
					var contentid = pointData[i].userid;
					var profileid = pointData[i].profile_id;";
				$script .= "var data	= pointData[i].data; ";
				$script .= "coordHash = 'user'+pointData[i].userid;";
					if($usermarker=='1')
						$script .= "var icon = '".$juri."components/com_geommunity3es/assets/img/user.png';";
					elseif($usermarker=='2')
						$script .= "if(+pointData[i].thumb !='')
						var icon = pointData[i].thumb ;";
					elseif($usermarker=='3')
						$script .= "var icon = '".$juri."images/com_geommunity3es/profiletypes_markers/'+profileid+'.png';";
				$script .= "	marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="	icon: icon,
						title: title,
						integration: 'user',
						contentid: contentid,";
				if( $profiletypes_filter)
					$script .= "profileid: profileid,";
				if( $custom_filter )
					$script .= "data: data";
				$script .= "});
					// hash the marker position	
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						if($clustering>0 && clustate==1){
							mc.addMarker(marker);
							buildMultiInfowindowCluster(map); ////////// yeah !!!!
						}
						else
							marker.setMap(map);";
			
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							userinfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	userinfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=userinfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										userinfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}
			// remove markers that are not in the profiletypes or customfilter selection anymore
			function dropOutofCustomProfilesMarkers(map) { ";
				if( $profiletypes_filter)
				{
						$script .= "var profiles = jQuery('input:checkbox:checked.profiletypes_filter_box').map(function () {
				  return this.value;
				}).get();";
				}
				if( $custom_filter )
				{
				$script .= "var customvalues = jQuery('input:checkbox:checked.custom_filter_box').map(function () {
				  return this.value;
				}).get();";
				}
				$script .= "var delmarkers = [];
				for (var i = 0 ; i < markers.length; i++)  {
					if (!markers[i]) {continue};";
					if( $profiletypes_filter)
					{
						$script .= "if (jQuery.inArray(markers[i].profileid , profiles)=='-1' && markers[i].integration=='user') {
							// remove from the map
							markers[i].setMap(null);
							// remove from the record of seen markers
							coordHash = 'user'+markers[i].contentid;
							if(seenCoordinates[coordHash]) {
								seenCoordinates[coordHash] = null;
							}
							// remove from the markers array
							delmarkers.push( coordHash );
						}";
					}
					if( $custom_filter )
					{
						$script .= "if (  markers[i].data!='' &&    jQuery.inArray(markers[i].data , customvalues)=='-1' &&  markers[i].data!=null  && markers[i].integration=='user') {							
							// remove from the map
							markers[i].setMap(null);
							// remove from the record of seen markers
							coordHash = 'user'+markers[i].contentid;
							if(seenCoordinates[coordHash]) {
								seenCoordinates[coordHash] = null;
							}
							// remove from the markers array
							delmarkers.push( coordHash );
						}";
					}

		$script .= "}
				for (var i = 0 ; i < delmarkers.length; i++)  {
					for (var j = 0 ; j < markers.length; j++)  {
						if(delmarkers[i] == markers[j].integration+markers[j].contentid)	
						{
							markers.splice(j, 1);
							if($clustering>0 && clustate==1 ){
								//if(i==markers.length-1){
									mc.clearMarkers();
								
									buildMultiInfowindowCluster(map);
								//}
							}
							
						}
					}			
				}
			}";

	if($integrations>1)
	{	
		$script .= "// remove markers that are not in the integrations selection anymore
			function dropOutofIntegrationsMarkers(map) {  
				var integrations = jQuery('input:checkbox:checked.integrations_box').map(function () {
				  return this.value;
				}).get();
				var delmarkers = [];
				for (var i = 0 ; i < markers.length; i++)  {
					if (!markers[i]) {continue};
					if (jQuery.inArray(markers[i].integration , integrations)=='-1') {
							// remove from the map
							markers[i].setMap(null);
						
							// remove from the record of seen markers
							coordHash = markers[i].integration+markers[i].contentid;
							if(seenCoordinates[coordHash]) {
								seenCoordinates[coordHash] = null;
							}
							// remove from the markers array
							delmarkers.push( coordHash );
						}
					}
				for (var i = 0 ; i < delmarkers.length; i++)  {
					for (var j = 0 ; j < markers.length; j++)  {
						if(delmarkers[i] == markers[j].integration+markers[j].contentid)	
						{
							markers.splice(j, 1);
							if($clustering>0 && clustate==1){
								mc.clearMarkers();
								buildMultiInfowindowCluster(map);

							}
						}
					}			
				}
			}";	
			
	}
		if( $marker_search)
		{	// bounce an allready loaded marker
			$script .= "function bounceSearchResultMarker(integration,contentid) {
				if (markers){
					for (var i in markers) {
						if(markers[i].integration == integration && markers[i].contentid == contentid){ 
								markers[i].setAnimation(google.maps.Animation.BOUNCE);
						}
					}
				}
			}";
		}
		if( $profiletypes_filter)
		{	
			$script .= "function bounceProfileMarkers(attr,val, bounce) {
				if (markers){
					for (var i in markers) {
						if(markers[i].profileid == val){ 
							if ( bounce=='0') {
								markers[i].setAnimation(null);
							  }
							  else if(bounce=='1') {
								markers[i].setAnimation(google.maps.Animation.BOUNCE);
							  }
							
						}
					}
				}
			}";
		}
		if( $custom_filter )
		{
			$script .= "function bounceCustomMarkers(attr,val, bounce) {
				if (markers){
					for (var i in markers) {
						if(markers[i].data){
							var markers_data = markers[i].data.replace('[\"', '' );
							var markers_data = markers_data.replace('\"]', '' );
							if( markers_data == val){ 
								if ( bounce=='0') {
									markers[i].setAnimation(null);
								 } 
								 else if(bounce=='1') {
									
									markers[i].setAnimation(google.maps.Animation.BOUNCE);
								  }
							}
						}
					}
				}
			}";
		}	
		if( $integrations>1 )
		{
			$script .= "function bounceIntegrationMarkers(attr,val, bounce) {
					for (var i in markers) {
						if(markers[i].integration == val){
							if ( bounce=='0') {
								markers[i].setAnimation(null);
							 } 
							 else if(bounce=='1') {
								markers[i].setAnimation(google.maps.Animation.BOUNCE);
							  }
						}
					}
			}";
		}
	}
	if($show_groups)
	{
		$script .="function loadGroupMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=groupmarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populateGroupMarkers(data, map);
    				}
				});	
			}	
			function populateGroupMarkers(pointData ,map, status, xhr) {										
				var groupinfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'group'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/group.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng), ";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'group' 
					});

					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						//
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="
						google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							groupinfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	groupinfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=groupinfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										groupinfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}
	
	if($show_events)
	{
		$script .="function loadEventMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=eventmarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populateEventMarkers(data, map);
    				}
				});	
			}	
			function populateEventMarkers(pointData ,map, status, xhr) {										
				var eventinfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'event'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/event.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'event' 
					});

					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						//
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="
						google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							eventinfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	eventinfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=eventinfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										eventinfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}
	
	if($show_photoalbums)
	{
		$script .="function loadPhotoalbumMarkersFromCurrentBounds(map) {
					dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=photoalbummarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populatePhotoalbumMarkers(data, map);
    				}
				});	
			}	
			function populatePhotoalbumMarkers(pointData ,map, status, xhr) {					
				var photoalbuminfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'photoalbum'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/photoalbum.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'photoalbum' 
					});
					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							photoalbuminfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	photoalbuminfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=photoalbuminfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										photoalbuminfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}
	if($show_photos)
	{
		$script .="function loadPhotoMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=photomarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populatePhotoMarkers(data, map);
    				}
				});	
			}
			function populatePhotoMarkers(pointData ,map, status, xhr) {						
				var photoinfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'photo'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/photo.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'photo' 
					});
					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							photoinfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	photoinfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=photoinfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										photoinfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}
	
	if($show_videos)
	{
		$script .="function loadVideoMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=videomarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populateVideoMarkers(data, map);
    				}
				});	
			}
			function populateVideoMarkers(pointData ,map, status, xhr) {						
				var videoinfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'video'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/video.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'video' 
					});
					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							videoinfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	videoinfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=videoinfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										videoinfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}
	if($show_easyblogs)
	{
		$script .="function loadEasyblogMarkersFromCurrentBounds(map) {
				dropOutofBoundsMarkers(map);
  				var bounds = map.getBounds();
				var swPoint = bounds.getSouthWest();
				var nePoint = bounds.getNorthEast();
				var swLat = swPoint.lat();
				var swLng = swPoint.lng();
				var neLat = nePoint.lat();
				var neLng = nePoint.lng();
				jQuery.ajax({
					url: 'index.php?option=com_geommunity3es&view=easyblogmarkers&format=json' ,
					data: {
						mapid: ".$mapid.",
						swLat: swLat,
						swLng: swLng,
						neLat: neLat,
						neLng: neLng
					},
					type: 'POST',
					dataType: 'json',
					success: function (data) {
						populateEasyblogMarkers(data, map);
    				}
				});	
			}
			function populateEasyblogMarkers(pointData ,map, status, xhr) {						
				var easybloginfowindow = new google.maps.InfoWindow({
				  	content: '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />'
				});				
				for (var i = 0 ;  i <  pointData.length ; i++)  {
					var lat = pointData[i].latitude;
					var lng = pointData[i].longitude;
					var title = pointData[i].title;
					var contentid = pointData[i].id;
					coordHash = 'easyblog'+pointData[i].id;
					var icon = '".$juri."components/com_geommunity3es/assets/img/easyblog.png';
					marker = new google.maps.Marker({		
						position: new google.maps.LatLng(lat, lng),";
				if($drop_markers)
					$script .= "animation: google.maps.Animation.DROP, ";
				$script .="icon: icon,
						title: title,
						contentid: contentid,
						integration: 'easyblog' 
					});
					// hash the marker position
					if(seenCoordinates[coordHash] == null) {
						seenCoordinates[coordHash] = 1;
						markers.push(marker);
						if($clustering>0 && clustate==1)
							mc.addMarker(marker);
						else
							marker.setMap(map);";
					if($marker_search)
					{
						$script .="if(marker.position.equals(map.getCenter()) ){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}";
					}
					$script .="google.maps.event.addListener(marker, 'click', (function( marker , i ) {
						return function() {
							easybloginfowindow.setContent( '<img src=\'".$juri."components/com_geommunity3es/assets/img/loader.gif\' />' );
						  	easybloginfowindow.open(map, marker);
						  	jQuery.ajax({
									url: 'index.php?option=com_geommunity3es&view=easybloginfowindow&format=raw' ,
									data: {contentid: marker.contentid ,
											latitude:marker.position.lat(),
											longitude:marker.position.lng()
											},
									success: function(html){
										easybloginfowindow.setContent( html );
									}
								});
							}
					  	})(marker, i));
					}		
				}
			}";
	}

$script .= "function calcRoute(endlat,endlng,modeoftravel) {
		document.getElementById('routelat').value = endlat;
		document.getElementById('routelng').value = endlng;
		var start;
		if(!modeoftravel )
			modeoftravel = document.getElementById('mode').value;		
		if(navigator.geolocation  ) {
			navigator.geolocation.getCurrentPosition(function(position) {
				start = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);			
				var end		=new google.maps.LatLng(endlat,endlng);
				var request = {
						  origin: start,
						  destination: end,
						  travelMode: google.maps.DirectionsTravelMode[modeoftravel],
						  unitSystem: google.maps.DirectionsUnitSystem.".$unitsystem."
				};		
				directionsService.route(request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
								document.getElementById('directionspanel').style.display='block';
								directionsDisplay.setDirections(response);
					}
					else
						alert('".JText::_('COM_GEOMMUNITY3ES_NOROUTEDATA')."');
				});	
			});
		}
     }
	 google.maps.event.addDomListener(window, 'load', initialize);";
$doc->addScriptDeclaration( $script );
require JModuleHelper::getLayoutPath('mod_geommunity3es', $params->get('layout', 'default'));