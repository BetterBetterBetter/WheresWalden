<?php
/* @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelVideomarkers extends JModelLegacy
{
	protected $markers;
	protected $friends;
	public function getVideomarkers() 
    {
        $db 		= JFactory::getDBO();
		$user 		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;		
		$mapid		= $jinput->get('mapid');

		$a = $swLat	= $jinput->get('swLat');
		$b = $swLng	= $jinput->get('swLng');
		$c = $neLat	= $jinput->get('neLat');
		$d = $neLng	= $jinput->get('neLng');
		$condition1 = $a < $c ? "sl.latitude BETWEEN $a AND $c":"sl.latitude BETWEEN $c AND $a";
		$condition2 = $b < $d ? "sl.longitude BETWEEN $b AND $d":"sl.longitude BETWEEN $d AND $b";


		$q = "SELECT sv.id ,  sv.uid ,  sv.title, 
		 sl.latitude, sl.longitude , 
		
		u.username , u.name , "; 
		// privacy values
		// 0: public
		// 10: registred
		// 30: friends
		// 40: only me
		// 100: custom
	
		$q .= "svm.value AS thumb ,
		spi.value AS privacy_value 
		FROM #__social_videos sv 
		JOIN #__social_locations sl ON sl.uid = sv.id
		LEFT JOIN #__social_videos_meta svm ON svm.photo_id = sv.id
		LEFT JOIN #__users u ON u.id= sv.uid 
		LEFT JOIN #__social_privacy_items spi ON spi.uid = sv.id  AND spi.user_id = sv.uid ";
		$q .= "WHERE sv.state='1' 
		AND svm.property='square'
		AND sl.type='photos' ";
		if($user->id =='0')
			$q .=" AND (spi.value='0' OR spi.value IS NULL) "; // only show public photos
		else{
			$getfriends= Geommunity3esModelPhotomarkers::getFriends($user->id);
			$friends_ids = array();
			foreach ($getfriends as $getfriend)
			{
				$friends_ids[] = $getfriend->user_id;
			}			
			if(count($friends_ids)>0)
				$friends_ids =  implode(',',$friends_ids );
			/////////////////////
			$q .=" AND 
			( spi.value='0' 
				OR spi.value IS NULL
			  OR spi.value='10' ";
			if(count($friends_ids)>0)
				$q .=" OR (spi.value='30' AND (sl.user_id IN(".$friends_ids.") OR sl.user_id='".$user->id."'  ) )";
			$q .=" OR (spi.value='40' AND sl.user_id='".$user->id."') 
				OR (spi.value='100')
			) "; 
			/////////////////
		}

		$q .= " HAVING sl.latitude !='' AND sl.longitude !='' AND latitude !='0' AND longitude !='0' ";
		$q .= "AND  ( $condition1 ) AND ( $condition2 ) ";

		$db->setQuery($q);
		$markers = $db->loadObjectList();
        return $markers;
	}
	
	static function getFriends($user_id)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$q ="SELECT sf.id ";
		$q .= " ,sl.user_id , sl.latitude , sl.longitude";
		$q .= " FROM #__social_friends  sf ";
		$q .= " LEFT JOIN #__social_locations sl ON ( (sl.user_id !='".$user_id."'  AND sl.user_id=sf.actor_id )OR (sl.user_id !='".$user_id."'  AND sl.user_id=sf.target_id) )";
		$q .= " WHERE sf.state='1' AND sl.type='users' ";
		$q .= " AND (sf.actor_id = '".$user_id."' OR sf.target_id='".$user_id."' ) ";
		$db->setQuery($q);
        $friends = $db->loadObjectList();
		return $friends;	
	}
}