<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class geommunity3esViewEasybloginfowindow extends JViewLegacy
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

		$cparams 			= JComponentHelper::getParams('com_geommunity3es');
		$easyblog_itemid		= $cparams->get('easyblog_itemid');
			  
		$this->infowindow  	= $this->get('Easybloginfowindow');
		$infowindow			= $this->infowindow;
		$this->username		= $infowindow->username;
		$this->user_id		= $infowindow->created_by;
		if($naming=='realname')
			$this->naming 		= $infowindow->name;
		else
			$this->naming 		= $this->username;
		$this->title		= $infowindow->title;
		$this->publish_up	= $infowindow->publish_up;
		$this->cattitle		= $infowindow->cattitle;
		$this->catid		= $infowindow->catid;
		$this->image		= $infowindow->image;
	
		$im_obj = json_decode($infowindow->image);	//http://localhost/easysocial3/images/easyblog_images/547/b2ap3_thumbnail_vlcsnap-2014-04-16-22h33m23s146.png
		if($this->image)
			$this->image		= JURI::Base().'images/easyblog_images/'.$this->user_id.'/'.$im_obj->{'title'};
		

		$this->easyblog_url 	= JRoute::_('index.php?option=com_easyblog&view=entry&id='.$this->id.'&Itemid='.$easyblog_itemid);
		$this->easyblog_caturl 	= JRoute::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$this->catid.'&Itemid='.$easyblog_itemid );
		
		
		parent::display($tpl);
	}
}