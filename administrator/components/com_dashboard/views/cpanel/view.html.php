<?php
/**
 * @version		$Id: view.html.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_dashboard
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Molajo
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.application.module.helper');

/**
 * HTML View class for the Cpanel component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_dashboard
 * @since 1.0
 */
class CpanelViewCpanel extends JView
{
	protected $modules = null;

	public function display($tpl = null)
	{
		// Set toolbar items for the page
		MolajoToolbarHelper::title(JText::_('COM_CPANEL'), 'cpanel.png');
		MolajoToolbarHelper::help('screen.cpanel');

		/*
		 * Set the template - this will display cpanel.php
		 * from the selected admin template.
		 */
		JRequest::setVar('tmpl', 'cpanel');

		// Display the cpanel modules
		$this->modules = MolajoModuleHelper::getModules('cpanel');

		parent::display($tpl);
	}
}