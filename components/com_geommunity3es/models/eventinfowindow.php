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
class Geommunity3esModeleventinfowindow extends JModelLegacy
{
	protected $infowindow;
	static function getEventinfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$eventid	= $jinput->get('contentid',null, 'int');
		$q ="SELECT sc.title, sc.alias as titlealias, sc.creator_uid ,sc.category_id, sc.description,sc.hits,sc.type,
		sa.small , sa.medium , spm.value AS cover ,
		sem.start, sem.end , 
		cov.x , cov.y ,
		 scc.title as cattitle ,scc.alias as catalias, 
		 ( SELECT COUNT(*) FROM #__social_clusters_nodes WHERE state='1' AND type='user' AND cluster_id= sc.id ) AS attendees ,

		 u.name, u.username 
		 FROM #__social_clusters sc 
		 JOIN  #__users AS u ON sc.creator_uid = u.id 
		 JOIN #__social_clusters_categories scc ON scc.id = sc.category_id 
		 JOIN #__social_events_meta sem ON sem.cluster_id = sc.id 
		 LEFT JOIN #__social_avatars sa ON sa.uid = sc.id 
		 LEFT JOIN #__social_covers cov ON cov.uid = sc.id AND cov.type='event' 
		 LEFT JOIN #__social_photos_meta spm ON spm.photo_id=cov.photo_id AND spm.property='large' 

		 WHERE  sc.id ='".$eventid."' ";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	}
}