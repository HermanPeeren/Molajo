<?php
/**
 * @version		$Id: installed.php 21343 2011-05-12 10:56:24Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Molajo
defined('_JEXEC') or die;

/**
 * Languages Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.5
 */
class LanguagesControllerInstalled extends JController
{
	/**
	 * task to set the default language
	 */
	function setDefault()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(MolajoText::_('JInvalid_Token'));
		$cid = JRequest::getCmd('cid', '');
		$model = $this->getModel('installed');
		if ($model->publish($cid))
		{
			$msg = MolajoText::_('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED');
			$type = 'message';
		}
		else
		{
			$msg = $this->getError();
			$type = 'error';
		}
		$application = $model->getApplication();
		$applicationId = $model->getState('filter.application_id');
		$this->setredirect('index.php?option=com_languages&view=installed&application='.$applicationId,$msg,$type);
	}
}
