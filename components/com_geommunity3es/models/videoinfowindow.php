<?php
/*
 * @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
class Geommunity3esModelvideoinfowindow extends JModelLegacy
{
	protected $infowindow;
	static function getVideoinfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$videoid		= $jinput->get('contentid',null, 'int');
		$q = "SELECT sv.id ,   sv.title, sv.caption ,  sv.storage, sv.user_id ,
		u.username , u.name , 
		svm.value AS thumb 
		FROM #__social_videos sv 
		
		LEFT JOIN #__social_videos_meta svm ON svm.video_id = sv.id
		LEFT JOIN #__users u ON u.id= sp.uid ";
		$q .= "WHERE sv.state='1' 
		AND svm.property='square' ";
		$q .= " AND sv.id='".$videoid."'";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	}
}