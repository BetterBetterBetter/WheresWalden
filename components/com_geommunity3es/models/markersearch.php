<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelMarkersearch extends JModelLegacy
{
	protected $markers;
	protected $friends;
	public function getMarkers() 
    {
        $db 		= JFactory::getDBO();
		$user 		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;		
		$mapid		= $jinput->post->get('mapid','1');
		$term		= $jinput->post->get('term',null,'string') ;

		//map params
		$q = "SELECT show_users,
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
		 show_easyblogs,
		 kmlurl, privacyaware
		  FROM #__geommunity3es_maps WHERE id='".$mapid."' ";
		$db->setQuery($q);
		$map_params 			= $db->loadObject();
		$show_users				= $map_params->show_users;
		$show_groups			= $map_params->show_groups;
		$show_events			= $map_params->show_events;
		$users_addressfield_id	= $map_params->users_addressfield_id;
		$groups_addressfield_id	= $map_params->groups_addressfield_id;
		$events_addressfield_id	= $map_params->events_addressfield_id;
		$show_photos			= $map_params->show_photos;
		$show_photoalbums		= $map_params->show_photoalbums;
		$show_easyblogs			= $map_params->show_easyblogs;
		$profiletypes 			= $map_params->profiletypes;
		$profiletypes_array 	= explode(',' , $profiletypes  );
		$onlineonly				= $map_params->onlineonly;
		$usermarker 			= $map_params->usermarker;
		$privacyaware			= $map_params->privacyaware;
		
		
		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $file ) )
		{
			return;
		}
		require_once $file;
		$config 	= Foundry::config();
		$naming = $config->get( 'users.displayName' );  // username or realname
		if($naming=='realname')
			$naming = 'name';
		
		$search_result = array();
		$i = 0;


		if($show_users)
		{
			$q ="SELECT u.".$naming." ,
			sfd.uid AS id , 
			sfd.data  AS latitude , 
			sfd2.data AS longitude  ";
			 if($privacyaware)
				$q .=", spi.value AS privacy_value ";
			$q .=" FROM #__users u 
			JOIN #__social_fields_data sfd ON sfd.uid = u.id  
			JOIN #__social_fields_data AS sfd2 ON sfd.field_id = sfd2.field_id AND sfd.uid = u.id AND sfd2.uid = u.id 
			JOIN #__social_profiles_maps spm ON spm.user_id = u.id AND spm.state='1' ";
			if($privacyaware)
				$q .=" LEFT JOIN #__social_privacy_items spi ON spi.type='field' AND spi.user_id= sfd.uid AND spi.uid=sfd.field_id ";
			if($onlineonly)
				$q .=" JOIN #__session AS s ON s.userid =  sfd.uid ";
			$q .=" WHERE u.".$naming." LIKE '%".$term."%' 
			AND u.block ='0' 
			AND sfd.type='user' AND sfd.datakey='latitude' 
			AND sfd2.type='user' AND sfd2.datakey='longitude' ";
			
			
			if($users_addressfield_id)
				$q .= " AND sfd.field_id='".$users_addressfield_id."'  ";
			if($onlineonly) 
				$q .=" AND s.client_id ='0' ";
			if($privacyaware){	
				if($user->id =='0')
					$q .=" AND (spi.value='0' OR spi.value IS NULL) "; // only show public albums
				else{
					$getfriends= Geommunity3esModelMarkersearch::getFriends($user->id);
					$friends_ids = array($user->id);
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
						$q .=" OR (spi.value='30' AND ( sfd.uid IN(".$friends_ids.") OR sfd.uid ='".$user->id."'  ) )";
					$q .=" OR (spi.value='40' AND sfd.uid ='".$user->id."') 
						OR (spi.value='100')
					) "; 
					/////////////////
				}
			}
			
			if($profiletypes!='')
				$q .=" AND ( spm.profile_id='".$profiletypes."' OR spm.profile_id IN( ".implode(',',$profiletypes_array)." ) ) ";
			$q .= " HAVING latitude !='' AND longitude !='' ";
			$db->setQuery($q);
			$members = $db->loadObjectList();
			
			foreach($members as $member)
			{
				$search_result[$i] =  array('title'=>$member->$naming , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_USER') , 
											'integration'=>'user' ,  
											'coords'=>$member->latitude.','.$member->longitude,
											'contentid'=>$member->id );
				$i++;
			}
		}
		if($show_groups)
		{
			$q ="SELECT DISTINCT(sfd.uid) AS id , 
				sfd.data AS latitude , 
			 sfd2.data AS longitude ,
					  sc.title, 
			sa.small  
			FROM #__social_fields_data AS sfd 
			JOIN #__social_clusters sc ON sc.id = sfd.uid 
			JOIN #__social_fields_data sfd2 ON sfd.field_id = sfd2.field_id AND sfd.uid = sfd2.uid 
			LEFT JOIN #__social_avatars sa ON sa.uid = sfd.uid 
			WHERE  sfd.type='group' AND sc.state='1' 
			AND sfd.datakey='latitude' AND sfd2.type='group' AND sfd2.datakey='longitude' 
			AND sc.title LIKE '%".$term."%' 
			";
			
			if($groups_addressfield_id)
				$q .= " AND sfd.field_id='".$groups_addressfield_id."' ";
	
			$q .= "GROUP BY sfd.uid 
			HAVING latitude !='' AND longitude !='' 
			";	
			$db->setQuery($q);
			$groups = $db->loadObjectList();
			
			foreach($groups as $group)
			{
				$search_result[$i] =  array('title'=>$group->title , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_GROUP') , 
											'integration'=>'group' ,  
											'coords'=>$group->latitude.','.$group->longitude,
											'contentid'=>$group->id );
				$i++;
			}
		}
		
		
		
		if($show_events)
		{

			$q ="SELECT DISTINCT(sfd.uid) AS id , sfd.data AS latitude , 
		sfd2.data AS longitude , 
		sc.title, 
		sa.small  
		 FROM #__social_fields_data AS sfd
		 JOIN #__social_fields_data AS sfd2 ON sfd.field_id = sfd2.field_id AND sfd.uid = sfd2.uid 
		 JOIN #__social_clusters sc ON sc.id = sfd.uid 
		 JOIN #__social_events_meta sem ON sem.cluster_id = sfd.uid AND (sem.start >= NOW() OR (sem.start <= NOW() AND sem.end >= NOW() ) )
		 LEFT JOIN #__social_avatars sa ON sa.uid = sfd.uid 
		 WHERE  sfd.type='event' AND sfd2.type='event' AND sc.state='1' 
		 AND sfd.datakey='latitude' AND sfd2.datakey='longitude' AND sc.title LIKE '%".$term."%'  ";
	
			if($events_addressfield_id)
				$q .= " AND sfd.field_id='".$events_addressfield_id."' ";
	
			$q .= "GROUP BY sfd.uid  
			HAVING latitude !='' AND longitude !=''  ";	
			
	
			$db->setQuery($q);
			$groups = $db->loadObjectList();
			
			foreach($groups as $group)
			{
				$search_result[$i] =  array('title'=>$group->title , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_EVENT') , 
											'integration'=>'group' ,  
											'coords'=>$group->latitude.','.$group->longitude,
											'contentid'=>$group->id );
				$i++;
			}
		}
		
		
		
		if($show_photoalbums)
		{
			$q = "SELECT sa.id ,  sa.title ,  
			sl.latitude, sl.longitude , 	
			spi.value AS privacy_value 
			FROM #__social_albums sa 
			JOIN #__social_locations sl ON sl.uid = sa.id 
			LEFT JOIN #__social_privacy_items spi ON spi.uid = sa.id AND spi.type='albums' AND spi.user_id = sa.user_id 
			WHERE   sl.type='albums' AND sa.title LIKE '%".$term."%' ";
			if($user->id =='0')
				$q .=" AND (spi.value='0' OR spi.value IS NULL) "; // only show public albums
			else{
				$getfriends= Geommunity3esModelMarkersearch::getFriends($user->id);
				$friends_ids = array();
				foreach ($getfriends as $getfriend)
				{
					$friends_ids[] = $getfriend->user_id;
				}			
				if(count($friends_ids)>0)
					$friends_ids =  implode(',',$friends_ids );
				$q .=" AND 
				( spi.value='0'
					OR spi.value IS NULL
				  OR spi.value='10' ";
				if(count($friends_ids)>0)
					$q .=" OR (spi.value='30' AND (sl.user_id IN(".$friends_ids.") OR sl.user_id='".$user->id."'  ) )";
				$q .=" OR (spi.value='40' AND sl.user_id='".$user->id."') 
					OR (spi.value='100')
				) "; 
			}
			$q .= " HAVING sl.latitude !='' AND sl.longitude !=''   ";
			$db->setQuery($q);
			$photoalbums = $db->loadObjectList();
			foreach($photoalbums as $photoalbum)
			{
				$search_result[$i] =  array('title'=>$photoalbum->title , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_PHOTOALBUM') , 
											'integration'=>'photoalbum' ,  
											'coords'=>$photoalbum->latitude.','.$photoalbum->longitude,
											'contentid'=>$photoalbum->id );
				$i++;
			}
		}
		
		if($show_photos)
		{
			$q = "SELECT sp.id ,  sp.uid , sp.title, 
			sl.latitude, sl.longitude , 
			spi.value AS privacy_value 
			FROM #__social_photos sp 
			JOIN #__social_locations sl ON sl.uid = sp.id
			LEFT JOIN #__users u ON u.id= sp.uid 
			LEFT JOIN #__social_privacy_items spi ON spi.uid = sp.id  AND spi.user_id = sp.uid 
			WHERE sp.state='1' 
			AND sl.type='photos' 
			AND sp.title LIKE '%".$term."%' ";
			if($user->id =='0')
				$q .=" AND (spi.value='0' OR spi.value IS NULL) "; // only show public photos
			else
			{
				$getfriends= Geommunity3esModelMarkersearch::getFriends($user->id);
				$friends_ids = array();
				foreach ($getfriends as $getfriend)
				{
					$friends_ids[] = $getfriend->user_id;
				}			
				if(count($friends_ids)>0)
					$friends_ids =  implode(',',$friends_ids );
				$q .=" AND 
				( spi.value='0' 
					OR spi.value IS NULL
				  OR spi.value='10' ";
				if(count($friends_ids)>0)
					$q .=" OR (spi.value='30' AND (sl.user_id IN(".$friends_ids.") OR sl.user_id='".$user->id."'  ) )";
				$q .=" OR (spi.value='40' AND sl.user_id='".$user->id."') 
					OR (spi.value='100')
				) "; 
			}
			$q .= " HAVING sl.latitude !='' AND sl.longitude !='' ";
			$db->setQuery($q);
			$photos = $db->loadObjectList();
			foreach($photos as $photo)
			{
				$search_result[$i] =  array('title'=>$photo->title , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_PHOTO') , 
											'integration'=>'photo' ,  
											'coords'=>$photo->latitude.','.$photo->longitude,
											'contentid'=>$photo->id );
				$i++;
			}	
		}
		
		
		if($show_easyblogs)
		{
			$q = "SELECT id,title,latitude,longitude 
			FROM #__easyblog_post 
			WHERE published>0 
			AND latitude !='' AND longitude !='' 
			AND title LIKE '%".$term."%' ";
			$db->setQuery($q);
			$easyblogs = $db->loadObjectList();
				
			foreach($easyblogs as $easyblog)
			{
				$search_result[$i] =  array('title'=>$easyblog->title , 
											'label'=>JText::_('COM_GEOMMUNITY3ES_INT_EASYBLOG') , 
											'integration'=>'easyblog' ,  
											'coords'=>$easyblog->latitude.','.$easyblog->longitude,
											'contentid'=>$easyblog->id );
				$i++;
			}
		}
		
		sort($search_result);
		return $search_result;
	}

	static function getFriends($user_id)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$q ="SELECT sf.id ,sl.user_id , sl.latitude , sl.longitude 
		FROM #__social_friends sf 
		LEFT JOIN #__social_locations sl ON ( (sl.user_id !='".$user_id."'  AND sl.user_id=sf.actor_id )OR (sl.user_id !='".$user_id."'  AND sl.user_id=sf.target_id) ) 
		WHERE sf.state='1' AND sl.type='users' 
		AND (sf.actor_id = '".$user_id."' OR sf.target_id='".$user_id."' ) ";
		$db->setQuery($q);
        $friends = $db->loadObjectList();
		return $friends;	
	}
}