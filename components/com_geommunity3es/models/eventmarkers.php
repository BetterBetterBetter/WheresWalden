<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelEventmarkers extends JModelLegacy
{
	protected $markers;
	public function getEventmarkers() 
    {
        $db 		= JFactory::getDBO();
		$user 		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;		
		$mapid		= $jinput->get('mapid');
		
		
		
		$a = $swLat	= $jinput->post->get('swLat');
		$b = $swLng	= $jinput->post->get('swLng');
		$c = $neLat	= $jinput->post->get('neLat');
		$d = $neLng	= $jinput->post->get('neLng');
		$condition1 = $a < $c ? "latitude BETWEEN $a AND $c":"latitude BETWEEN $c AND $a";
		$condition2 = $b < $d ? "longitude BETWEEN $b AND $d":"longitude BETWEEN $d AND $b";

		$swLat	= 20;
		$swLng	= 0;
		$neLat	= 60;
		$neLng	= 10;
		
		
		$cparams 	= JComponentHelper::getParams('com_geommunity3es');

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
		 events_addressfield_id,
		 kmlurl, privacyaware
		  FROM #__geommunity3es_maps WHERE id='".$mapid."' ";
		$db->setQuery($q);
		$map_params 			= $db->loadObject();
		$integrations 			= 0;
		$title 					= $map_params->title;
		$def_lat				= $map_params->def_lat;
		$del_lng				= $map_params->def_lng;
		$show_groups			= $map_params->show_groups;
		$events_addressfield_id	= $map_params->events_addressfield_id;
		$privacyaware			= $map_params->privacyaware;



		$q ="SELECT DISTINCT(sfd1.uid) AS id , sfd1.raw AS latitude , 
		sfd2.raw AS longitude , 
		sc.title, 
		sa.small  
		 FROM #__social_fields_data AS sfd1
		 JOIN #__social_fields_data AS sfd2 ON sfd1.field_id = sfd2.field_id AND sfd1.uid = sfd2.uid 
		 JOIN #__social_clusters sc ON sc.id = sfd1.uid 
		 JOIN #__social_events_meta sem ON sem.cluster_id = sfd1.uid AND (sem.start >= NOW() OR (sem.start <= NOW() AND sem.end >= NOW() ) )
		 LEFT JOIN #__social_avatars sa ON sa.uid = sfd1.uid 
		 WHERE  sfd1.type='event' AND sfd2.type='event' AND sc.state='1' 
		 AND sfd1.datakey='latitude' AND sfd2.datakey='longitude' 
		  ";
		 if($events_addressfield_id)
		 	$q .= " AND sfd1.field_id='".$events_addressfield_id."' AND sfd2.field_id='".$events_addressfield_id."' ";
		 
		 $q .= "GROUP BY sfd1.uid
		  HAVING latitude !='' AND longitude !='' AND latitude !='0' AND longitude !='0' 
		AND  ( $condition1 ) AND ( $condition2 ) 
		";	

		$db->setQuery($q);
		$markers = $db->loadObjectList();
		return $markers;
	}

}