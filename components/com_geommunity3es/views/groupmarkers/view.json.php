<?php
/* @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewGroupmarkers extends JViewLegacy
{
	function display($tpl = null) 
    {
		$this->groupmarkers = json_encode($this->get('Groupmarkers') );
        parent::display($tpl);
	}
}