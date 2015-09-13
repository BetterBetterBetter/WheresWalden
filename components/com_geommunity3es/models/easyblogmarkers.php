<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelEasyblogmarkers extends JModelLegacy
{
	protected $markers;
	public function getEasyblogmarkers() 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;		
		$a = $swLat	= $jinput->post->get('swLat');
		$b = $swLng	= $jinput->post->get('swLng');
		$c = $neLat	= $jinput->post->get('neLat');
		$d = $neLng	= $jinput->post->get('neLng');
		$condition1 = $a < $c ? "ebp.latitude BETWEEN $a AND $c":"ebp.latitude BETWEEN $c AND $a";
		$condition2 = $b < $d ? "ebp.longitude BETWEEN $b AND $d":"ebp.longitude BETWEEN $d AND $b";
		
		
		$q = " SELECT ebp.id,  ebp.title , ebp.latitude, ebp.longitude 
		, u.username, u.name 
		FROM #__easyblog_post ebp 
		JOIN #__users u ON u.id = ebp.created_by 
		WHERE ebp.published > 0  
		AND ebp.latitude !='' AND ebp.longitude !=''  AND ebp.latitude !='0' AND ebp.longitude !='0' ";
		$q .= " HAVING   ( $condition1 ) AND ( $condition2 ) ";
		$db->setQuery($q);
		$markers = $db->loadObjectList();
		return $markers;
	}	
}