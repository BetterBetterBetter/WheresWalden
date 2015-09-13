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
class Geommunity3esModelEasybloginfowindow extends JModelLegacy
{
	protected $infowindow;
 
        /**
         * Get the message
         * @return string The message to be displayed to the user
         */

	
	static function getEasybloginfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$postid		= $jinput->get('contentid',null, 'int');

		
	$q = " SELECT ebp.id, ebp.created_by , ebp.publish_up, ebp.title , ebp.category_id , ebp.vote, ebp.hits, ebp.latitude, ebp.longitude, ebp.image 
		, cat.id as catid, cat.title as cattitle
		, u.username, u.name 
		FROM #__easyblog_post ebp 
		JOIN #__easyblog_category cat ON cat.id = ebp.category_id 
		JOIN #__users u ON u.id = ebp.created_by 
		
		WHERE ebp.id='".$postid."'  " ;
		
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	
	}
}