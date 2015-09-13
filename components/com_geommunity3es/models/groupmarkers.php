<?php
/* @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelGroupmarkers extends JModelLegacy
{
	protected $markers;
	public function getGroupmarkers() 
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
		$groups_addressfield_id	= $map_params->groups_addressfield_id;
		$privacyaware			= $map_params->privacyaware;

		$q ="SELECT DISTINCT(sfd.uid) AS id , 
				sfd.data  AS latitude , 
			 sfd2.data  AS longitude ,
					  sc.title, 
			sa.small  
		 	FROM #__social_fields_data AS sfd 
		 	JOIN #__social_clusters sc ON sc.id = sfd.uid 
		 	JOIN #__social_fields_data AS sfd2 ON sfd.field_id = sfd2.field_id AND sfd.uid = sfd2.uid 
		 	LEFT JOIN #__social_avatars sa ON sa.uid = sfd.uid 
			WHERE  sfd.type='group' AND sc.state='1'
			AND sfd.datakey='latitude' AND sfd2.type='group' AND sfd2.datakey='longitude' 
			GROUP BY sfd.uid ";
		
		if($groups_addressfield_id)
		 	$q .= " AND sfd.field_id='".$groups_addressfield_id."' ";

		$q .= " HAVING latitude !='' AND longitude !='' AND latitude !='0' AND longitude !='0' 
		AND  ( $condition1 ) AND ( $condition2 ) 
		";	

		$db->setQuery($q);
		$markers = $db->loadObjectList();
		return $markers;
	}
}