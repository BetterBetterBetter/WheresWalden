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
class Geommunity3esModelgroupinfowindow extends JModelLegacy
{
	protected $infowindow;
	static function getGroupinfowindow( ) 
    {
        $db 		= JFactory::getDBO();
		$jinput 	= JFactory::getApplication()->input;
		$groupid	= $jinput->get('contentid',null, 'int');
		$q ="SELECT sc.title, sc.alias as titlealias, sc.creator_uid ,sc.category_id, sc.description,sc.hits,sc.type,
		sa.small , sa.medium ,  spm.value AS cover ,
		cov.x , cov.y ,
		 scc.title as cattitle ,scc.alias as catalias, 
		(SELECT COUNT(*) FROM #__social_clusters_nodes WHERE type='user' AND cluster_id=sc.id AND state='1') AS memberscount , 
		 u.name, u.username 
		 FROM #__social_clusters sc 
		 JOIN  #__users AS u ON sc.creator_uid = u.id 
		 JOIN #__social_clusters_categories scc ON scc.id = sc.category_id
		 LEFT JOIN #__social_avatars sa ON sa.uid = sc.id AND sa.type='group' 
		 LEFT JOIN #__social_covers cov ON cov.uid = sc.id AND cov.type='group' 
		 LEFT JOIN #__social_photos_meta spm ON spm.photo_id=cov.photo_id AND spm.property='large' 

		 WHERE  sc.id ='".$groupid."' ";
		$db->setQuery($q);
		$infowindow = $db->loadObject();
		return $infowindow;
	}
}