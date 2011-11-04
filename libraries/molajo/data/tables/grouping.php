<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Groupings table class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @version		1.0
 */
class MolajoTableGrouping extends MolajoTable
{
	/**
	 * Constructor
	 *
	 * @param   object  Database object
	 *
	 * @return  MolajoTableGrouping
	 *
	 * @since   11.1
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__view_groups', 'id', $db);
	}

	/**
	 * Method to bind the data.
	 *
	 * @param   array  $array   The data to bind.
	 * @param   mixed  $ignore  An array or space separated list of fields to ignore.
	 *
	 * @return  bool  True on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	/**
	 * Method to check the current record to save
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function check()
	{
		// Validate the title.
		if ((trim($this->title)) == '') {
			$this->setError(MolajoText::_('MOLAJO_DATABASE_ERROR_GROUPING'));
			return false;
		}

		return true;
	}
}
