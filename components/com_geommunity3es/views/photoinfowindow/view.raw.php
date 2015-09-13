<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class geommunity3esViewPhotoinfowindow extends JViewLegacy
{
	function display($tpl = null) 
	{
		$jinput 			= JFactory::getApplication()->input;
		$this->id			= $jinput->get('contentid',null, 'int');
		$this->latitude		= $jinput->get('latitude');
		$this->longitude	= $jinput->get('longitude');
		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $file ) )
		{
			return;
		}
		require_once $file ;
		
		$config 		= Foundry::config();
		$naming 		= $config->get( 'users.displayName' );  // username or realname
		if($naming == 'realname')
			$naming = 'name';
		
		$this->storage_photos 	= $config->get( 'storage.photos' );
		$this->amazon_bucket 	= $config->get( 'storage.amazon.bucket' ); 
		$this->amazon_ssl 		= $config->get( 'storage.amazon.ssl' ); 
		$this->amazon_region 	= $config->get( 'storage.amazon.region' );

		$cparams 			= JComponentHelper::getParams('com_geommunity3es');
		$photo_itemid		= $cparams->get('photo_itemid');
			  
		$this->infowindow  	= $this->get('Photoinfowindow');
		$infowindow			= $this->infowindow;
		$this->username		= $infowindow->username;
		if($naming=='realname')
			$this->naming 		= $infowindow->name;
		else
			$this->naming 		= $this->username;
		$this->title		= $infowindow->title;
		if (strlen($infowindow->caption) > 50)
   			$this->caption = substr( $infowindow->caption, 0, 47) . '...';
		else
			$this->caption	= $infowindow->caption;
		//$this->thumb		= JURI::Base().$infowindow->thumb;
		
		
		$this->thumb			= $infowindow->thumb;
		
		if($this->storage_photos=='joomla')
		{
			if( strpos( $this->thumb , 'media/com_easysocial/photos' ) !== false )
				$this->thumb		= $juri.$this->thumb;
			else
				$this->thumb		= $juri.'media/com_easysocial/photos'.$this->thumb;
			
		}
		elseif($this->storage_photos=='amazon') 
		{ // amazon s3 storage for user cover
			if($this->amazon_ssl)
				$thumb		= 'https://';
			else
				$thumb		= 'http://';
			
			$thumb		.= 's3-'.$this->amazon_region;
			$thumb		.= '.amazonaws.com/'.$this->amazon_bucket.'/media/com_easysocial/photos';
			
			$thumb		.= $this->thumb;
			$this->thumb		= $thumb;
		}
		
		
		
		$this->user_id		= $infowindow->user_id;
		$this->album_id		= $infowindow->album_id;
		$this->albumname	= $infowindow->albumname;
		$this->album_url 	= JRoute::_('index.php?option=com_easysocial&view=albums&id='.$this->album_id.':'.JFilterOutput::stringURLSafe($this->albumname).'&layout=item&uid='.$this->user_id.':'.$this->naming.'&type=user&Itemid='.$photo_itemid );	
		$this->photo_url 	= JRoute::_('index.php?option=com_easysocial&view=photos&layout=item&id='.$this->id.':'.JFilterOutput::stringURLSafe($this->title).'&type=user&uid='.$this->user_id.':'.$this->naming.'&Itemid='.$photo_itemid );
		parent::display($tpl);
	}
}