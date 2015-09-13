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

jimport('joomla.application.component.view');

/**
 * View class for a list of Geommunity3es.
 */
class Geommunity3esViewMaps extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		Geommunity3esBackendHelper::addSubmenu('maps');
        
		$this->addToolbar();
        
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/geommunity3es.php';

		$state	= $this->get('State');
		$canDo	= Geommunity3esBackendHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_GEOMMUNITY3ES_TITLE_MAPS'), 'maps.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/map';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('map.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('map.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('maps.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('maps.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'maps.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('maps.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('maps.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'maps.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('maps.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_geommunity3es');
		}
        
        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_geommunity3es&view=maps');
        
        $this->extra_sidebar = '';
        
		//Filter for the field show_users
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Users');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_show_users',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.show_users'), true)
		);

		//Filter for the field onlineonly
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Online Only');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_onlineonly',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.onlineonly'), true)
		);

		//Filter for the field show_photoalbums
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Photo Albums');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_show_photoalbums',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.show_photoalbums'), true)
		);

		//Filter for the field show_photos
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Photos');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_show_photos',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.show_photos'), true)
		);

		//Filter for the field show_groups
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Groups');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_show_groups',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.show_groups'), true)
		);

		//Filter for the field show_events
		$select_label = JText::sprintf('COM_GEOMMUNITY3ES_FILTER_SELECT_LABEL', 'Events');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0";
		$options[0]->text = "JNo";
		$options[1] = new stdClass();
		$options[1]->value = "1";
		$options[1]->text = "JYes";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_show_events',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.show_events'), true)
		);

		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

        
	}
    
	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.title' => JText::_('COM_GEOMMUNITY3ES_MAPS_TITLE'),
		'a.def_lat' => JText::_('COM_GEOMMUNITY3ES_MAPS_DEF_LAT'),
		'a.def_lng' => JText::_('COM_GEOMMUNITY3ES_MAPS_DEF_LNG'),
		'a.show_users' => JText::_('COM_GEOMMUNITY3ES_MAPS_SHOW_USERS'),
		'a.show_photoalbums' => JText::_('COM_GEOMMUNITY3ES_MAPS_SHOW_PHOTOALBUMS'),
		'a.show_photos' => JText::_('COM_GEOMMUNITY3ES_MAPS_SHOW_PHOTOS'),
		'a.show_groups' => JText::_('COM_GEOMMUNITY3ES_MAPS_SHOW_GROUPS'),
		'a.show_events' => JText::_('COM_GEOMMUNITY3ES_MAPS_SHOW_EVENTS'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		'a.checked_out' => JText::_('COM_GEOMMUNITY3ES_MAPS_CHECKED_OUT'),
		'a.checked_out_time' => JText::_('COM_GEOMMUNITY3ES_MAPS_CHECKED_OUT_TIME'),
		'a.created_by' => JText::_('COM_GEOMMUNITY3ES_MAPS_CREATED_BY'),
		);
	}

    
}
