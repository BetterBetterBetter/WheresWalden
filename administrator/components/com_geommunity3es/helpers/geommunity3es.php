<?php

/**
 * @version     1.0.0
 * @package     com_geommunity3es
 * @copyright   Copyright (C) 2014. Adrien ROUSSEL Nordmograph.com All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nordmograph <contact@nordmograph.com> - http://www.nordmograph.com./extensions
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Geommunity3es helper.
 */
class Geommunity3esBackendHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        		JHtmlSidebar::addEntry(
			JText::_('COM_GEOMMUNITY3ES_TITLE_MAPS'),
			'index.php?option=com_geommunity3es&view=maps',
			$vName == 'maps'
		);

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_geommunity3es';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }


}
