<?php

/**
 * @version     1.0.0
 * @package     com_geommunity3es
 * @copyright   Copyright (C) 2014. Adrien ROUSSEL Nordmograph.com All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nordmograph <contact@nordmograph.com> - http://www.nordmograph.com./extensions
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Geommunity3es records.
 */
class Geommunity3esModelMaps extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'def_lat', 'a.def_lat',
                'def_lng', 'a.def_lng',
                'show_users', 'a.show_users',
                'users_addressfield_id', 'a.users_addressfield_id',
                'profiletypes', 'a.profiletypes',
                'onlineonly', 'a.onlineonly',
                'avatarmarker', 'a.avatarmarker',
                'show_photoalbums', 'a.show_photoalbums',
                'show_photos', 'a.show_photos',
				 'show_videos', 'a.show_videos',
                'show_groups', 'a.show_groups',
                'groups_addressfield_id', 'a.groups_addressfield_id',
                'show_events', 'a.show_events',
                'events_addressfield_id', 'a.events_addressfield_id',
				'show_easyblogs', 'a.show_easyblogs',
                'kmlurl', 'a.kmlurl',
                'privacyaware', 'a.privacyaware',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        
		//Filtering show_users
		$this->setState('filter.show_users', $app->getUserStateFromRequest($this->context.'.filter.show_users', 'filter_show_users', '', 'string'));

		//Filtering onlineonly
		$this->setState('filter.onlineonly', $app->getUserStateFromRequest($this->context.'.filter.onlineonly', 'filter_onlineonly', '', 'string'));

		//Filtering show_photoalbums
		$this->setState('filter.show_photoalbums', $app->getUserStateFromRequest($this->context.'.filter.show_photoalbums', 'filter_show_photoalbums', '', 'string'));

		//Filtering show_photos
		$this->setState('filter.show_photos', $app->getUserStateFromRequest($this->context.'.filter.show_photos', 'filter_show_photos', '', 'string'));
		
		//Filtering show_videos
		$this->setState('filter.show_videos', $app->getUserStateFromRequest($this->context.'.filter.show_videos', 'filter_show_videos', '', 'string'));

		//Filtering show_groups
		$this->setState('filter.show_groups', $app->getUserStateFromRequest($this->context.'.filter.show_groups', 'filter_show_groups', '', 'string'));

		//Filtering show_events
		$this->setState('filter.show_events', $app->getUserStateFromRequest($this->context.'.filter.show_events', 'filter_show_events', '', 'string'));
		
		//Filtering show_easyblogs
		$this->setState('filter.show_easyblogs', $app->getUserStateFromRequest($this->context.'.filter.show_easyblogs', 'filter_show_easyblogs', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_geommunity3es');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__geommunity3es_maps` AS a');

        
		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		// Join over the user field 'created_by'
		$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                
            }
        }

        

		//Filtering show_users
		$filter_show_users = $this->state->get("filter.show_users");
		if ($filter_show_users != '') {
			$query->where("a.show_users = '".$db->escape($filter_show_users)."'");
		}

		//Filtering onlineonly
		$filter_onlineonly = $this->state->get("filter.onlineonly");
		if ($filter_onlineonly != '') {
			$query->where("a.onlineonly = '".$db->escape($filter_onlineonly)."'");
		}

		//Filtering show_photoalbums
		$filter_show_photoalbums = $this->state->get("filter.show_photoalbums");
		if ($filter_show_photoalbums != '') {
			$query->where("a.show_photoalbums = '".$db->escape($filter_show_photoalbums)."'");
		}

		//Filtering show_photos
		$filter_show_photos = $this->state->get("filter.show_photos");
		if ($filter_show_photos != '') {
			$query->where("a.show_photos = '".$db->escape($filter_show_photos)."'");
		}
		
		//Filtering show_videos
		$filter_show_videos = $this->state->get("filter.show_videos");
		if ($filter_show_videos != '') {
			$query->where("a.show_videos = '".$db->escape($filter_show_videos)."'");
		}

		//Filtering show_groups
		$filter_show_groups = $this->state->get("filter.show_groups");
		if ($filter_show_groups != '') {
			$query->where("a.show_groups = '".$db->escape($filter_show_groups)."'");
		}

		//Filtering show_events
		$filter_show_events = $this->state->get("filter.show_events");
		if ($filter_show_events != '') {
			$query->where("a.show_events = '".$db->escape($filter_show_events)."'");
		}
		
		//Filtering show_easyblogs
		$filter_show_easyblogs = $this->state->get("filter.show_easyblogs");
		if ($filter_show_easyblogs != '') {
			$query->where("a.show_easyblogs = '".$db->escape($filter_show_easyblogs)."'");
		}


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }

}
