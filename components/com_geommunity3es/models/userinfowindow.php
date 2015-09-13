<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HelloWorld Model
 */
class Geommunity3esModeluserinfowindow extends JModelLegacy
{
	protected $infowindow;
 
        /**
         * Get the message
         * @return string The message to be displayed to the user
         */

	
	static function getUserinfowindow( ) 
    {
        $user = JFactory::getUser();
		$db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$userid		= $jinput->get('contentid',null, 'int');

		
		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $file ) )
		{
			return;
		}
		require_once $file;
		$config 	= Foundry::config();
		$photos_enabled = $config->get( 'photos.enabled' );  
		//$videos_enabled = $config->get( 'videos.enabled' );  
		$points_enabled = $config->get( 'points.enabled' ); 
		$followers_enabled = $config->get( 'followers.enabled' );  
		
	// u , sa , sc , sp , spm , sf
		$q = "SELECT u.name, u.username , u.lastvisitDate 
		, spm.value AS cover 
		, sc.photo_id , sc.x , sc.y  
		, sf.state as isfriend 
		, (SELECT COUNT(*) FROM #__session WHERE userid=u.id AND client_id='0') AS onlinestatus ";
		if($photos_enabled)
			$q .= " , (SELECT COUNT(*) FROM #__social_albums WHERE user_id=u.id AND type='user' AND core='0') AS photoalbumscount ";
		if($points_enabled)
			$q .= " , (SELECT SUM(points) FROM #__social_points_history WHERE user_id=u.id AND state='1') AS points ";
		if($followers_enabled)
			$q .= " , (SELECT COUNT(*) FROM #__social_subscriptions WHERE uid=u.id AND type='user.user') AS followerscount ";
			
		$q .= " , (SELECT COUNT(*) FROM #__social_friends WHERE (actor_id=u.id OR  target_id=u.id ) AND state='1') AS friendscount ";
		//if($videos_enabled)
			//$q .= " , (SELECT COUNT(*) FROM #__social_videos WHERE user_id=u.id AND type='user' AND state='1') AS videoscount ";
		$q .= " FROM #__users u  
		LEFT JOIN #__social_covers sc ON sc.uid=u.id AND sc.type='user' 
		LEFT JOIN #__social_photos_meta spm ON spm.photo_id=sc.photo_id AND spm.property='large' 
		LEFT JOIN #__social_friends sf ON (sf.actor_id ='".$user->id."' AND sf.target_id=u.id AND sf.state='-1') OR (sf.actor_id ='".$user->id."' AND sf.target_id=u.id AND sf.state='1') OR  (sf.actor_id =u.id AND sf.target_id='".$user->id."' AND sf.state='1')
		WHERE u.id='".$userid."'";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		//echo $infowinfow["medium"]  = Foundry::user( $user->id )->getAvatar();
		//$infowinfow->medium  = Foundry::user( $user->id )->getAvatar();
		//$infowinfow->offsetSet("medium",Foundry::user( $user->id )->getAvatar() ); 
		//var_dump($infowindow);
		return $infowindow;
	
	}
}