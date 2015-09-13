<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2015 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelUsermarkers extends JModelLegacy
{
	protected $markers;
	protected $friends;
	public function getUsermarkers() 
    {
        //ini_set('display_errors', 1);  error_reporting(E_ALL); 

		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $file ) )
		{
			return;
		}
		require_once( $file );// Include main Easysocial engine

		$db 		= JFactory::getDBO();
		$user 		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;		
		$mapid		= $jinput->post->getInt('mapid');
		//$mapid		= $jinput->getInt('mapid');

		$profiles	= $jinput->post->get('profiles',null,'string') ;
		//$profiles	= $jinput->get('profiles',null,'string') ;
		if($profiles!='0')
			$profiles 	= implode(',', json_decode($profiles) );
		
		$custom_filter	= strtoupper($jinput->post->get('customfilter',null,'string')) ;
		//$custom_filter	= strtoupper($jinput->get('customfilter',null,'string')) ;
		$custom_values	= $jinput->post->get('customvalues',null,'raw') ;
		//$custom_values	= $jinput->get('customvalues',null,'raw') ;

		$a = $swLat	= $jinput->post->get('swLat');
		$b = $swLng	= $jinput->post->get('swLng');
		$c = $neLat	= $jinput->post->get('neLat');
		$d = $neLng	= $jinput->post->get('neLng');

		//$a = $swLat	= $jinput->get('swLat');
		//$b = $swLng	= $jinput->get('swLng');
		//$c = $neLat	= $jinput->get('neLat');
		//$d = $neLng	= $jinput->get('neLng');


		$condition1 = $a < $c ? "latitude BETWEEN $a AND $c":"latitude BETWEEN $c AND $a";
		$condition2 = $b < $d ? "longitude BETWEEN $b AND $d":"longitude BETWEEN $d AND $b";

		//map params
		$q = "SELECT show_users,
		users_addressfield_id,
		profiletypes,
		onlineonly,
		usermarker,
		privacyaware 
		FROM #__geommunity3es_maps WHERE id='".$mapid."' ";
		$db->setQuery($q);
		$map_params 			= $db->loadObject();
		$users_addressfield_id	= strtoupper( $map_params->users_addressfield_id) ;
		$profiletypes 			= $map_params->profiletypes;
		$profiletypes_array 	= explode(',' , $profiletypes  );
		$onlineonly				= $map_params->onlineonly;
		$usermarker 			= $map_params->usermarker;
		$privacyaware			= $map_params->privacyaware;

		$q ="SELECT DISTINCT(sfd.uid) AS userid , 
		sfd.data  AS latitude , 
		sfd2.data AS longitude , ";
		if($privacyaware)
			$q .=" spi.value AS privacy_value ,";
		if($custom_filter)
			$q .=" sfd3.data AS data ,";
		$q .= " u.name, u.username , spm.profile_id 
		 FROM #__social_fields_data AS sfd  ";
		 if($users_addressfield_id)
		 	 $q .=" JOIN #__social_fields sf ON sf.id = sfd.field_id AND sf.unique_key='".$users_addressfield_id."' ";
		 
		 $q .=" JOIN #__social_fields_data AS sfd2 ON sfd.field_id = sfd2.field_id AND sfd.uid = sfd2.uid 
		 JOIN  #__users AS u ON sfd.uid = u.id 
		 JOIN #__social_profiles_maps spm ON spm.user_id = u.id AND spm.state='1' ";
		if($onlineonly)
			$q .=" JOIN #__session AS s ON s.userid =  sfd.uid ";
		if($usermarker=='2')
			$q .=" LEFT JOIN #__social_avatars sa ON sa.uid = sfd.uid ";
		if($custom_filter)
		{
			$q .=" LEFT JOIN #__social_fields sf3 ON sf3.unique_key='".$custom_filter."' 
				LEFT JOIN #__social_fields_data sfd3  ON sfd3.uid = sfd.uid  AND sfd3.type='user' AND  sf3.id = sfd3.field_id 	 ";
		}
		if($privacyaware)
			$q .=" LEFT JOIN #__social_privacy_items spi ON spi.type='field' AND spi.user_id= sfd.uid AND spi.uid=sfd.field_id ";

		$q .= " WHERE  sfd.type='user' AND sfd.datakey='latitude' AND sfd2.type='user' AND sfd2.datakey='longitude' ";
		if($custom_filter && strpos($custom_values,'["')===false ) 
		{
			 $custom_values	= json_decode($custom_values);
			 if($custom_filter)
				$custom_values 	= '"'. implode('","', $custom_values ) .'"';
			 $q .= " AND ( sfd3.data IN(".$custom_values.")   OR sfd3.data=''  
			 OR NOT EXISTS(
			 		SELECT sfdx.data 
					FROM #__social_fields_data sfdx 
					LEFT JOIN #__social_fields sfx ON sfx.id = sfdx.field_id 
					WHERE sfdx.uid=sfd.uid AND sfdx.type='user' AND sfx.unique_key='".$custom_filter."' )
			  ) ";
		}
		elseif($custom_filter ) // multiple options custom fields
		{
			$custom_values = str_replace( array('[',']','"'), '' , $custom_values );
			
				$custom_values = explode (',' , $custom_values);
				if($custom_values[0]!='' && count($custom_values)>0)
				{
					$q .= " AND (   ";
					for($i=0;$i<count($custom_values);$i++)
					{
						if($custom_values[$i]!='')
						{
							$q .= " sfd3.data LIKE '%".$custom_values[$i]."%' ";
							if( $i<count($custom_values) -1 )
								$q .= " OR ";
						}
					}
					  $q .= ") ";
				}	
		}
		$q .= " AND u.block !=1  ";
		if($onlineonly) 
			$q .=" AND s.client_id ='0' ";
		if($privacyaware){	
			if($user->id =='0')
				$q .=" AND (spi.value='0' OR spi.value IS NULL) "; // only show public albums
			else{
				$getfriends= Geommunity3esModelUsermarkers::getFriends($user->id);
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

		if(count($profiles)>0 && $profiles!='' && $profiles!='0')
			$q .= " AND spm.profile_id IN( $profiles ) ";
		elseif($profiles=='')
			$q .= " AND 1=2 "; // display nothing*/
			
		if($profiletypes!='')
			$q .=" AND ( spm.profile_id='".$profiletypes."' OR spm.profile_id IN( ".implode(',',$profiletypes_array)." ) ) ";	
			
		$q .= " GROUP BY sfd.uid  
		HAVING latitude !='' AND longitude !='' AND latitude !='0' AND longitude !='0' ";
		$q .= " AND  ( $condition1 ) AND ( $condition2 )  ";
		$db->setQuery($q);
		$markers = $db->loadObjectList();
		$markerz = array();
		$i = 0;
		foreach($markers as $marker)
		{
			$markerz[$i]['userid'] = $marker->userid;
			$markerz[$i]['latitude'] = $marker->latitude;
			$markerz[$i]['longitude'] = $marker->longitude;
			if($privacyaware)
				$markerz[$i]['privacy_value'] = $marker->privacy_value;
			$markerz[$i]['thumb'] = Foundry::user( $marker->userid )->getAvatar('small') ;
			if($custom_filter)
				$markerz[$i]['data'] = $marker->data;
			$markerz[$i]['name'] = $marker->name;
			$markerz[$i]['username'] = $marker->username;
			$markerz[$i]['profile_id'] = $marker->profile_id;
			$i++;
		}
		return $markerz;
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