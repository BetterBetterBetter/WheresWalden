<?php
/* @component com_geommunity3es
 * @copyright Copyright (C) 2008-2014 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : http://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class Geommunity3esViewEasyblogmarkers extends JViewLegacy
{
	function display($tpl = null) 
	{
		$this->easyblogmarkers = json_encode($this->get('Easyblogmarkers') );
		parent::display($tpl);
	}
}