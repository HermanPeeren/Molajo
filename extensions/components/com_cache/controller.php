<?php
/**
 * @version		$Id: controller.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Cache Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since 1.6
 */
class CacheController extends JController
{
	/**
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.0
	 */
	public function display($cachable = false, $urlparameters = false)
	{
		require_once JPATH_COMPONENT.'/helpers/cache.php';

		// Get the document object.
		$document	= MolajoFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getCmd('view', 'cache');
		$vFormat	= $document->getType();
		$lName		= JRequest::getCmd('layout', 'default');

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				case 'purge':
					break;
				case 'cache':
				default:
					$model = $this->getModel($vName);
					$view->setModel($model, true);
					break;
			}

			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			// Load the submenu.
			CacheHelper::addSubmenu(JRequest::getCmd('view', 'cache'));

			$view->display();
		}
	}

	public function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(MolajoText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		$model = $this->getModel('cache');

		if(empty($cid)) {
			MolajoError::raiseWarning(500, MolajoText::_('JERROR_NO_ITEMS_SELECTED'));
		} else {
			$model->cleanlist($cid);
		}

		$this->setRedirect('index.php?option=com_cache&application='.$model->getApp()->id);
	}

	public function purge()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(MolajoText::_('JInvalid_Token'));

		$model = $this->getModel('cache');
		$ret = $model->purge();

		$msg = MolajoText::_('COM_CACHE_EXPIRED_ITEMS_HAVE_BEEN_PURGED');
		$msgType = 'message';

		if ($ret === false) {
			$msg = MolajoText::_('Error purging expired items');
			$msgType = 'error';
		}

		$this->setRedirect('index.php?option=com_cache&view=purge', $msg, $msgType);
	}
}
