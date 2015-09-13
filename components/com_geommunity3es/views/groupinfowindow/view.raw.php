<?php
/* @version     1.0.0
 * @component com_geommunity3es
 * @copyright Copyright (C) 2010-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewGroupinfowindow extends JViewLegacy
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
		
		$cparams 			= JComponentHelper::getParams('com_geommunity3es');
		$profile_itemid		= $cparams->get('profile_itemid');
		$group_itemid		= $cparams->get('group_itemid');
					  
		$this->infowindow  	= $this->get('Groupinfowindow');
		$infowindow			= $this->infowindow;
		$this->username		= $infowindow->username;
		if($naming=='realname')
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
		
		$this->cover		= $infowindow->cover;
		$this->x			= $infowindow->x;
		$this->y			= $infowindow->y;
		
		$this->memberscount	= $infowindow->memberscount;
			
		if (strlen($infowindow->description) > 50)
			$this->description = substr( $infowindow->description, 0, 47) . '...';
		else
			$this->description	= $infowindow->description;
		$this->hits	= $infowindow->hits;
		$this->type	= $infowindow->type;
			
		$this->profile_url 	= JRoute::_('index.php?option=com_easysocial&amp;view=profile&amp;id='.$this->creator_uid.':'.$this->username.'&amp;Itemid='.$profile_itemid );
		$this->group_url 	= JRoute::_('index.php?option=com_easysocial&view=groups&id='.$this->id.':'.$this->alias.'&layout=item&Itemid='.$group_itemid );
						
		if(!$this->small)
			$this->avatar 	= $juri.'media/com_easysocial/defaults/avatars/group/small.png';
		else
			$this->avatar 	= $juri.'media/com_easysocial/avatars/group/'.$this->id.'/'.$this->medium;
			
		if(!$this->cover)
			$this->cover	= $juri.'media/com_easysocial/defaults/covers/user/default.jpg';
		else
			$this->cover		= $juri.$this->cover;
			
			
		parent::display($tpl);
	}
}