<?php
/* @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewPhotoalbummarkers extends JViewLegacy
{
	function display($tpl = null) 
    {
		$this->photoalbummarkers = json_encode($this->get('Photoalbummarkers') );
		parent::display($tpl);
	}
}