<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewUserinfowindow extends JViewLegacy
{
	function display($tpl = null) 
	{
		$juri				= JURI::Base(); 
		$jinput 			= JFactory::getApplication()->input;
		$this->userid		= $jinput->get('contentid',null, 'int');
		$this->latitude		= $jinput->get('latitude');
		$this->longitude	= $jinput->get('longitude');
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
			
		$this->photos_enabled = $config->get( 'photos.enabled' );  
		$this->videos_enabled = $config->get( 'videos.enabled' );  
		$this->points_enabled = $config->get( 'points.enabled' ); 
		$this->followers_enabled = $config->get( 'followers.enabled' );
		
		$this->storage_photos 	= $config->get( 'storage.photos' );
		$this->amazon_bucket 	= $config->get( 'storage.amazon.bucket' ); 
		$this->amazon_ssl 		= $config->get( 'storage.amazon.ssl' ); 
		$this->amazon_region 	= $config->get( 'storage.amazon.region' );  
		
		
		
		$cparams 			= JComponentHelper::getParams('com_geommunity3es');
		$profile_itemid		= $cparams->get('profile_itemid');

		$this->infowindow  	= $this->get('Userinfowindow');
		$infowindow			= $this->infowindow;
		$this->naming		= $infowindow->$naming;	
		$this->username		= $infowindow->username;

		$user_foundry 		= Foundry::user( $this->userid );
		$this->avatar 		= $user_foundry->getAvatar('medium');
		$this->cover		= $user_foundry->getCover('large');
		
		$this->x			= $infowindow->x;
		$this->y			= $infowindow->y;
		
		$this->points			= $infowindow->points;
		
		$lastvisitDate		= $infowindow->lastvisitDate;
		$this->daysdiff			=  number_format( ( time() - strtotime( $lastvisitDate ) ) /(60*60*24) ) ;
		
		if($infowindow->onlinestatus=='0')
			$this->onlinestatus='off';
		else
			$this->onlinestatus='on';
			
		$this->photoalbumscount		= $infowindow->photoalbumscount;
		if($this->videos_enabled)
			$this->videoscount		= $infowindow->videoscount;
		if($this->points_enabled)
			$this->points		= $infowindow->points;
		if($this->followers_enabled)
			$this->followerscount	= $infowindow->followerscount;
		$this->friendcount	= $infowindow->friendscount;
		$this->isfriend		= $infowindow->isfriend;
		
		
		$this->profile_url 	= JRoute::_('index.php?option=com_easysocial&amp;view=profile&amp;id='.$this->userid.':'.$this->username.'&amp;Itemid='.$profile_itemid );
			
		
		parent::display($tpl);
	}
}