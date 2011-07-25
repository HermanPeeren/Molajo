<?php
/**
 * @version		$Id: templates.php 20442 2011-01-26 08:09:30Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of template extension records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesModelTemplates extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'folder', 'a.folder',
				'element', 'a.element',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'enabled', 'a.enabled',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'application_id', 'a.application_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Override parent getItems to add extra XML metadata.
	 *
	 * @since	1.6
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as &$item) {
			$application = JApplicationHelper::getApplicationInfo($item->application_id);
			$item->xmldata = TemplatesHelper::parseXMLTemplateFile($application->path, $item->element);
		}
		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id, a.name, a.element, a.application_id'
			)
		);
		$query->from('`#__extensions` AS a');

		// Filter by extension type.
		$query->where('`type` = '.$db->quote('template'));

		// Filter by application.
		$applicationId = $this->getState('filter.application_id');
		if (is_numeric($applicationId)) {
			$query->where('a.application_id = '.(int) $applicationId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.element LIKE '.$search.' OR a.name LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.folder')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		return $query;
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
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.application_id');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$applicationId = $this->getUserStateFromRequest($this->context.'.filter.application_id', 'filter_application_id', null);
		$this->setState('filter.application_id', $applicationId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_templates');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.element', 'asc');
	}
}
