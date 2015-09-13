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
class Geommunity3esModelphotoalbuminfowindow extends JModelLegacy
{
	protected $infowindow;
	static function getPhotoalbuminfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$albumid		= $jinput->get('contentid',null, 'int');
		$q = "SELECT sa.id , sa.cover_id, sa.user_id, sa.title AS albumname, sa.caption, sa.hits ,

		u.username , u.name , 
		sp.title, sp.storage , 
		COUNT(DISTINCT(sp2.id)) AS albumphotocount ,
		spm.value AS thumb ,
		spi.value AS privacy_value 
		FROM #__social_albums sa 
		LEFT JOIN #__social_photos sp ON sa.cover_id = sp.id AND sp.state='1' 
		LEFT JOIN #__social_photos sp2 ON sa.id = sp2.album_id 
		LEFT JOIN #__social_photos_meta spm ON spm.photo_id = sp.id AND spm.value!='0' AND spm.property='square' AND spm.group='path' 
		LEFT JOIN #__users u ON u.id= sa.user_id 
		LEFT JOIN #__social_privacy_items spi ON spi.uid = sa.id AND spi.type='albums' AND spi.user_id = sa.user_id 
		WHERE  sa.id='".$albumid."'";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	}
}