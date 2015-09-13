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
class Geommunity3esModelphotoinfowindow extends JModelLegacy
{
	protected $infowindow;
	static function getPhotoinfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$photoid		= $jinput->get('contentid',null, 'int');
		$q = "SELECT sp.id ,   sp.title, sp.caption ,  sp.storage, sp.user_id ,sp.album_id,
		 sa.title AS albumname,
		u.username , u.name , 
		spm.value AS thumb 
		FROM #__social_photos sp 
		JOIN #__social_albums sa ON sa.id = sp.album_id
		LEFT JOIN #__social_photos_meta spm ON spm.photo_id = sp.id AND spm.property='square' AND spm.group='path' 
		LEFT JOIN #__users u ON u.id= sp.uid 
		WHERE sp.state='1' AND sp.id='".$photoid."'";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	}
}