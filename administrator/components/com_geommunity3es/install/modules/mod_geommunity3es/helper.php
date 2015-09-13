<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_geommunity3es
 * @copyright   Copyright (C) 2005 - 2014 Nordmograph.com. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
class modGeommunity3esHelper {
	static function getProfileTypes($mapid)
	{
		$db = JFactory::getDBO();
		//map params
		$q = "SELECT profiletypes FROM #__geommunity3es_maps WHERE id='".$mapid."' ";
		$db->setQuery($q);
		$profiletypes 	= $db->loadResult();
		$profiletypes_array 	= explode(',' , $profiletypes  );
			
		$q = "SELECT id, title, description FROM #__social_profiles WHERE state='1' ";	
		if(count($profiletypes_array>1) && $profiletypes!='')
			$q .="AND id IN(".implode(',',$profiletypes_array).") ";		
		$db->setQuery($q);
		$profiles = $db->loadObjectList();
		if( count($profiles)>1 )
			return $profiles;
		else
			return '';
	}
	
	static function getCustomFilterOptions( $custom_filter )
	{
		$db = JFactory::getDBO();	
		$q ="SELECT title, app_id FROM #__social_fields WHERE unique_key='".$custom_filter."' ";
		$db->setQuery($q);
		$app = $db->loadobject();
		$title 	= @$app->title;
		$app_id = @$app->app_id;
		$q = "SELECT DISTINCT(sfd.data), sfd.raw 
		FROM #__social_fields_data sfd 
		JOIN #__social_fields sf ON sfd.field_id = sf.id
		WHERE sf.unique_key ='".$custom_filter."' AND sfd.raw !='' 
		GROUP BY sfd.data 
		ORDER BY sfd.data ASC ";		
		$db->setQuery($q);
		$values = $db->loadObjectList();
		$return = array();
		$j = 0;
		foreach($values as $value)
		{
			if(	strpos( $value->data , '["' ) !==false)
			{
				$opts = str_replace( array('"','[',']'), '', $value->data);
				$opts = explode(',', $opts);
				for($i = 0;$i<count($opts);$i++)
				{
					@$return[$j]->data = $opts[$i];
				}
			}
			else
			@$return[$j]->data = $value->data;
			$j++;
		}
		$return[] = $title;
		$return[] = $app_id;
		return $return;
		
	}
	
	static function getMemberCoords($addressfield_id)
	{
		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		$coords = array();
		$q = "SELECT lat.data AS latitude, lng.data AS longitude 
		FROM #__social_fields_data lat 
		JOIN  #__social_fields_data lng 
		ON lat.field_id = lng.field_id AND lat.type = lng.type AND lat.uid = lng.uid 
		JOIN #__social_fields sf ON sf.id = lat.field_id AND sf.unique_key = '".$addressfield_id."'
		WHERE lat.datakey='latitude' AND lng.datakey='longitude' 
		AND lat.type='user'
		AND lat.uid='".$user->id."' ";
		$db->setQuery($q);
		$data = $db->loadObject();
		if($data)
		{
			$coords[] = $data->latitude;
			$coords[] = $data->longitude;
			return $coords;
		}
	}
}