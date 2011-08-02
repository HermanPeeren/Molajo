<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Setup controller for the Installer.
 * - JSON Protocol -
 *
 * @package		Molajo
 * @sub_package Installation
 * @since		1.0
 */
class MolajoInstallationControllerSetup extends JController
{
	/**
     * setlanguage
     *
	 * Method to set the setup language for the application.
	 *
	 * @return	void
	 * @since	1.7
	 */
	public function setlanguage()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;
		
		// Get the application object.
		$app = MolajoFactory::getApplication();

		// Check for potentially unwritable session
		$session = MolajoFactory::getSession();

		if ($session->isNew()) {
			$this->sendResponse(new JException(JText::_('INSTL_COOKIES_NOT_ENABLED'), 500));
		}

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Get the posted values from the request and validate them.
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$return	= $model->validate($data, 'language');

		$r = new JObject();
		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the language selection screen.
			$r->view = 'language';
			$this->sendResponse($r);
			return false;
		}

		// Store the options in the session.
		$vars = $model->storeOptions($return);

		// Redirect to the next page.
		$r->view = 'preinstall';
		$this->sendResponse($r);
	}
	
	/**
     * database
     *
	 * @return	void
	 * @since	1.7
	 */
	public function database()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Get the application object.
		$app = MolajoFactory::getApplication();

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Get the posted values from the request and validate them.
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$return	= $model->validate($data, 'database');

		$r = new JObject();
		// Check for validation errors.
		if ($return === false) {
			// Store the options in the session.
			$vars = $model->storeOptions($data);

			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the database selection screen.
			$r->view = 'database';
			$this->sendResponse($r);

			return false;
		}

		// Store the options in the session.
		$vars = $model->storeOptions($return);

		// Get the database model.
		$database = $this->getModel('Database', 'MolajoInstallationModel', array('dbo' => null));

		// Attempt to initialise the database.
		$return = $database->initialise($vars);

		// Check if the databasa was initialised
		if (!$return) {
			$app->enqueueMessage($database->getError(), 'notice');
			$r->view = 'database';
			$this->sendResponse($r);
		} else {
			// Mark sample content as not installed yet
			$data = array(
				'sample_installed' => '0'
			);
			$dummy = $model->storeOptions($data);

			$r->view = 'filesystem';
			$this->sendResponse($r);
		}
	}

	/**
     * filesystem
     *
	 * @return	void
	 * @since	1.7
	 */
	public function filesystem()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Get the application object.
		$app = MolajoFactory::getApplication();

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Get the posted values from the request and validate them.
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$return	= $model->validate($data, 'filesystem');

		$r = new JObject();
		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the database selection screen.
			$r->view = 'filesystem';
			$this->sendResponse($r);

			return false;
		}

		// Store the options in the session.
		$vars = $model->storeOptions($return);

		$r->view = 'site';
		$this->sendResponse($r);
	}

	/**
     * saveconfig
     *
	 * @return	void
	 * @since	1.7
	 */
	public function saveconfig()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Get the application object.
		$app = MolajoFactory::getApplication();

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Get the posted values from the request and validate them.
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$return	= $model->validate($data, 'site');

		// Attempt to save the data before validation
		$form = $model->getForm();
		$data = $form->filter($data);
		unset($data['admin_password2']);
		$model->storeOptions($data);

		$r = new JObject();
		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the configuration screen.
			$r->view = 'site';
			$this->sendResponse($r);

			return false;
		}

		// Store the options in the session.
		unset($return['admin_password2']);
		$vars = $model->storeOptions($return);

		// Get the configuration model.
		$configuration = $this->getModel('Configuration', 'MolajoInstallationModel', array('dbo' => null));

		// Attempt to setup the configuration.
		$return = $configuration->setup($vars);

		// Ensure a language was set.
		if (!$return) {
			$app->enqueueMessage($configuration->getError(), 'notice');
			$r->view = 'site';
		} else {
			$r->view = 'complete';
		}
		$this->sendResponse($r);
	}
	
	/**
     * loadSampleData
     *
	 * @return	void
	 * @since	1.6
	 */
	public function loadSampleData()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or die();

		// Get the posted config options.
		$vars = JRequest::getVar('jform', array());

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Get the options from the session.
		$vars = $model->storeOptions($vars);

		// Get the database model.
		$database = $this->getModel('Database', 'MolajoInstallationModel', array('dbo' => null));

		// Attempt to load the database sample data.
		$return = $database->installSampleData($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($database->getError(), 500));
		} else {
			// Mark sample content as installed
			$data = array(
				'sample_installed' => '1'
			);
			$dummy = $model->storeOptions($data);
		}

		// Create a response body.
		$r = new JObject();
		$r->text = JText::_('INSTL_SITE_SAMPLE_LOADED');

		// Send the response.
		$this->sendResponse($r);
	}

	/**
     * detectFtpRoot
     *
	 * @return	void
	 * @since	1.6
	 */
	public function detectFtpRoot()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or die();

		// Get the posted config options.
		$vars = JRequest::getVar('jform', array());

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Store the options in the session.
		$vars = $model->storeOptions($vars);

		// Get the database model.
		$filesystem = $this->getModel('Filesystem', 'MolajoInstallationModel', array('dbo' => null));

		// Attempt to detect the Joomla root from the ftp account.
		$return = $filesystem->detectFtpRoot($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($filesystem->getError(), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->root = $return;

		// Send the response.
		$this->sendResponse($r);
	}

	/**
     * verifyFtpSettings
     *
	 * @return	void
	 * @since	1.6
	 */
	public function verifyFtpSettings()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or die;

		// Get the posted config options.
		$vars = JRequest::getVar('jform', array());

		// Get the setup model.
		$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));

		// Store the options in the session.
		$vars = $model->storeOptions($vars);

		// Get the database model.
		$filesystem = $this->getModel('Filesystem', 'MolajoInstallationModel', array('dbo' => null));

		// Verify the FTP settings.
		$return = $filesystem->verifyFtpSettings($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($filesystem->getError(), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->valid = $return;

		// Send the response.
		$this->sendResponse($r);
	}

	/**
     * removeFolder
     *
	 * @return	void
	 * @since	1.6
	 */
	public function removeFolder()
	{

		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or die;

		// Get the posted config options.
		$vars = JRequest::getVar('jform', array());

		$path = MOLAJO_PATH_INSTALLATION;
		//check whether the folder still exists
		if (!file_exists($path)) {
			$this->sendResponse(new JException(JText::sprintf('INSTL_COMPLETE_ERROR_FOLDER_ALREADY_REMOVED'), 500));
		}

		// check whether we need to use FTP
		$useFTP = false;
		if ((file_exists($path) && !is_writable($path))) {
			$useFTP = true;
		}

		// Check for safe mode
		if (ini_get('safe_mode')) {
			$useFTP = true;
		}

		// Enable/Disable override
		if (!isset($options->ftpEnable) || ($options->ftpEnable != 1)) {
			$useFTP = false;
		}

		if ($useFTP == true) {

			$ftp = JFTP::getInstance($options->ftp_host, $options->ftp_port);
			$ftp->login($options->ftp_user, $options->ftp_pass);

			// Translate path for the FTP account
			$file = JPath::clean(str_replace(MOLAJO_PATH_CONFIGURATION, $options->ftp_root, $path), '/');
			$return = $ftp->delete($file);

			// Delete the extra XML file while we're at it
			if ($return) {
				$file = JPath::clean($options->ftp_root.'/molajo.xml');
				if (file_exists($file)) {
					$return = $ftp->delete($file);
				}
			}

			$ftp->quit();
		} else {
			// Try to delete the folder.
			// We use output buffering so that any error message echoed JFolder::delete
			// doesn't land in our JSON output.
			ob_start();
			$return = JFolder::delete($path) && (!file_exists(MOLAJO_PATH_ROOT.'/molajo.xml') || JFile::delete(MOLAJO_PATH_ROOT.'/molajo.xml'));
			ob_end_clean();
		}

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException(JText::_('INSTL_COMPLETE_ERROR_FOLDER_DELETE'), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->text = JText::_('INSTL_COMPLETE_FOLDER_REMOVED');

		// Send the response.
		$this->sendResponse($r);
	}

	/**
     * sendResponse
     *
	 * Method to handle a send a JSON response. The data parameter
	 * can be a JException object for when an error has occurred or
	 * a JObject for a good response.
	 *
	 * @param	object	$response	JObject on success, JException on failure.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function sendResponse($response)
	{
		// Check if we need to send an error code.
		if (JError::isError($response)) {
			// Send the appropriate error code response.
			JResponse::setHeader('status', $response->getCode());
			JResponse::setHeader('Content-Type', 'application/json; charset=utf-8');
			JResponse::sendHeaders();
		}

		// Send the JSON response.
		echo json_encode(new MolajoInstallationJsonResponse($response));

		// Close the application.
		$app = MolajoFactory::getApplication();
		$app->close();
	}
}

/**
 * MolajoInstallationJsonResponse
 *
 * Core Installation JSON Response Class
 *
 * @package		Joomla.Installation
 * @since		1.6
 */
class MolajoInstallationJsonResponse
{
	function __construct($state)
	{
		// The old token is invalid so send a new one.
		$this->token = JUtility::getToken(true);
		
		// Get the language and send it's code along
		$lang = MolajoFactory::getLanguage();
		$this->lang = $lang->getTag();

		// Get the message queue
		$messages = MolajoFactory::getApplication()->getMessageQueue();

		// Build the sorted message list
		if (is_array($messages) && count($messages)) {
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message'])) {
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}

		// If messages exist add them to the output
		if (isset($lists) && is_array($lists)) {
			$this->messages = $lists;
		}

		// Check if we are dealing with an error.
		if (JError::isError($state)) {
			// Prepare the error response.
			$this->error	= true;
			$this->header	= JText::_('INSTL_HEADER_ERROR');
			$this->message	= $state->getMessage();
		} else {
			// Prepare the response data.
			$this->error	= false;
			$this->data		= $state;
		}
	}
}

// Set the error handler.
//JError::setErrorHandling(E_ALL, 'callback', array('MolajoInstallationControllerSetup', 'sendResponse'));
