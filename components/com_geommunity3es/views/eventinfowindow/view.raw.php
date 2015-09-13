<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewEventinfowindow extends JViewLegacy
{
    function display($tpl = null) 
	{
		$juri				= JURI::Base(); 
		$jinput 			= JFactory::getApplication()->input;
		$this->id			= $jinput->get('contentid',null, 'int');	
		$this->latitude		= $jinput->get('latitude');
		$this->longitude	= $jinput->get('longitude');

		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $file ) )
			return;
		require_once $file ;
		$config 	= Foundry::config();
		$naming 	= $config->get( 'users.displayName' );  // username or realname
		if($naming == 'realname')
			$naming = 'name';
			
	//	$this->storage_avatars 	= $config->get( 'storage.avatars' );
		$this->storage_photos 	= $config->get( 'storage.photos' );
		$this->amazon_bucket 	= $config->get( 'storage.amazon.bucket' ); 
		$this->amazon_ssl 		= $config->get( 'storage.amazon.ssl' ); 
		$this->amazon_region 	= $config->get( 'storage.amazon.region' );
		
		$cparams 			= JComponentHelper::getParams('com_geommunity3es');
		$profile_itemid		= $cparams->get('profile_itemid');
		$event_itemid		= $cparams->get('event_itemid');
					  
		$this->infowindow  	= $this->get('Eventinfowindow');
		$infowindow			= $this->infowindow;
		$this->username		= $infowindow->username;
		if($naming=='name')
			$this->naming = $infowindow->name;
		else
			$this->naming = $this->username;

		$this->small 		= $infowindow->small;
		$this->medium 		= $infowindow->medium;
											
		$this->title		= $infowindow->title;
		$this->alias		= $infowindow->titlealias;	
		$this->creator_uid	= $infowindow->creator_uid;
		$this->cattitle		= $infowindow->cattitle;
		$this->catid		= $infowindow->category_id;
		
		$this->start		= $infowindow->start;
		$this->end			= $infowindow->end;
		$this->attendees	= $infowindow->attendees;
		
		$this->cover		= $infowindow->cover;
		$this->x			= $infowindow->x;
		$this->y			= $infowindow->y;
			
		if (strlen($infowindow->description) > 50)
			$this->description = substr( $infowindow->description, 0, 47) . '...';
		else
			$this->description	= $infowindow->description;
		$this->hits	= $infowindow->hits;
		$this->type	= $infowindow->type;
			
		$this->profile_url 	= JRoute::_('index.php?option=com_easysocial&amp;view=profile&amp;id='.$this->creator_uid.':'.$this->username.'&amp;Itemid='.$profile_itemid );
		$this->event_url 	= JRoute::_('index.php?option=com_easysocial&view=events&id='.$this->id.':'.$this->alias.'&layout=item&Itemid='.$event_itemid );
					
					
		$this->storage_photos;	
		if(!$this->medium)
			$this->avatar 	= $juri.'media/com_easysocial/defaults/avatars/event/small.png';
		elseif($this->storage_photos=='joomla')
		{
			$this->avatar 	= $juri.'media/com_easysocial/avatars/event/'.$this->id.'/'.$this->medium;
		}
		elseif($this->storage_photos=='amazon') 
		{ // amazon s3 storage for user cover
			if($this->amazon_ssl)
				$avatar		= 'https://';
			else
				$avatar		= 'http://';
			$avatar		.= 's3-'.$this->amazon_region;
			$avatar		.= '.amazonaws.com/'.$this->amazon_bucket.'/media/com_easysocial/avatars/event/';
			$avatar		.= $this->id.'/'.$this->medium;
			$this->avatar		= $avatar;
		}
		
		
			
		if(!$this->cover)
			$this->cover	= $juri.'media/com_easysocial/defaults/covers/user/default.jpg';
		elseif($this->storage_photos=='joomla')
		{
			if( strpos( $this->cover , 'media/com_easysocial/photos' ) !== false )
				$this->cover		= $juri.$this->cover;
			else
				$this->cover		= $juri.'media/com_easysocial/photos'.$this->cover;
			
		}
		elseif($this->storage_photos=='amazon') 
		{ // amazon s3 storage for user cover
			if($this->amazon_ssl)
				$cover		= 'https://';
			else
				$cover		= 'http://';
			$cover		.= $this->amazon_bucket.'.s3-'.$this->amazon_region;
			$cover		.= '.amazonaws.com/media/com_easysocial/photos';
			$cover		.= $this->cover;
			$this->cover		= $cover;
		}
			
			
		parent::display($tpl);
	}
}