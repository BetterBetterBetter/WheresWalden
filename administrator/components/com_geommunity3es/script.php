<?php
/**
 * @version     1.0.0
 * @package     com_geommunity3es
 * @copyright   Copyright (C) 2014. Adrien ROUSSEL Nordmograph.com All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      Nordmograph <contact@nordmograph.com> - http://www.nordmograph.com./extensions
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
/**
* Method to install the component
* 
* @param  mixed    $parent     The class calling this method
* @return void
*/

// Text should use language file strings which are defined in the administrator languages folder section in the XX-XX.com_lendr.sys.ini
class com_geommunity3esInstallerScript
{
	
	function install($parent) 
	{
		echo '<h1><img src="components/com_geommunity3es/assets/img/logo_90x90.png" alt="logo" width="90" height="90" style="vertical-align:center" /> Geommunity3es Component Installation</h1>';	
		$app = JFactory::getApplication();			
		$error = 0;		
		$cache =  JFactory::getCache();
		$cache->clean( null, 'com_geommunity3es' );
		$db	= JFactory::getDBO();
		jimport('joomla.filesystem.folder');
				jimport('joomla.filesystem.file');		
					
		/************************************************************************
		 *
		 *                              START INSTALL
		 *
		 *************************************************************************/
		$install = '<table class="table table-condensed table-striped"><tbody>
		<tr><td><span class="icon-ok"></span> GEOMMUNITY3ES Component installed successfully</td></tr>';
		
		
		
		
		
		
		$module_installer = new JInstaller;
		$file_origin = JPATH_ADMINISTRATOR.'/components/com_geommunity3es/install/modules/mod_geommunity3es';
		if( $module_installer->install( $file_origin ) )
		{
			$q = "UPDATE #__modules SET ordering='1', published='1' WHERE `module`='mod_geommunity3es'";
			$db->setQuery( $q );
			$db->query();	
			$install .= '<tr><td><span class="icon-ok"></span> GEOMMUNITY3ES module Installed successfully.</td></tr>';
		} else $error++;

		
		$install .= '</tbody></table>';
		
		$install .='<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FNordmograph-Web-marketing-and-Joomla-expertise%2F368385633962&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=739550822721946" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>';
		
		$install .='<div style="text-align:center;padding:0 0 100px; 0"><h3>Start here:</h3><br /><a href="index.php?option=com_config&view=component&component=com_geommunity3es" class="btn btn-success btn-large"><span class="icon-cog"></span> Geommunity3ES Component Settings</a></div>';
		
		echo $install;
	}
	/**
	* Method to update the component
	* 
	* @param  mixed  $parent   The class calling this method
	* @return void
	*/
function update($parent) 
{  
	$app = JFactory::getApplication();			
		$error = 0;		
		$cache =  JFactory::getCache();
		$cache->clean( null, 'com_geommunity3es' );
		$db	= JFactory::getDBO();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');	
		
		$q = "SELECT * FROM #__update_sites WHERE location LIKE 'http://www.nordmograph.com%' ";
		$db->setQuery($q);
		$sites = $db->loadObjectList();
		foreach($sites as $site)
		{
			$old_location = $site->location;
			$new_location = str_replace('http:', 'https:', $old_location);
			$q = "UPDATE #__update_sites SET location ='".$new_location."' WHERE location='".$old_location."' ";
			$db->setQuery($q);
			$db->query();
			$app->enqueueMessage('Update site location updated: '.$new_location);
		}		
				
		$update ='';
	
		$module_installer = new JInstaller;
		$file_origin = JPATH_ADMINISTRATOR.'/components/com_geommunity3es/install/modules/mod_geommunity3es';
		if( $module_installer->install( $file_origin ) )
		{
			$q = "UPDATE #__modules SET ordering='1', published='1' WHERE `module`='mod_geommunity3es'";
			$db->setQuery( $q );
			$db->query();	
			$update .= '<div class="alert alert-success" >Installing/updating module was also successfull.</div>';
		} else $error++;



 
  
  //echo JText::_('COM_GEOMMUNITY3ES_UPDATE_SUCCESSFULL');
  $update .='<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FNordmograph-Web-marketing-and-Joomla-expertise%2F368385633962&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=739550822721946" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>';
  $update .='<div style="text-align:center;padding:0 0 100px; 0"><br /><a href="index.php?option=com_geommunity3es" class="btn btn-success btn-large"><span class="icon-location"></span> Geommunity3ES Maps Manager</a></div>';
  echo $update;
}
/**
* method to run before an install/update/uninstall method
*
* @param  mixed  $parent   The class calling this method
* @return void
*/
function preflight($type, $parent) 
{
 // ...
}
 
function postflight($type, $parent)
{
  //...
}

}