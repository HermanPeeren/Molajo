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
 * Application class
 *
 * Provide many supporting API functions
 *
 * @package		Molajo
 * @subpackage  Installation
 * @since       1.0
 *
 */
class MolajoInstallation extends MolajoApplication
{
	/**
	 * The url of the site
	 *
	 * @var string
	 */
	protected $_siteURL = null;

	/**
	* Class constructor
	*
	* @param	array $config	An optional associative array of configuration settings.
	*
	* @return	void
	*/
	public function __construct(array $config = array())
	{
		$config['applicationId'] = 2;
		parent::__construct($config);
		$this->_createConfiguration();
		JURI::root(null, str_replace('/'.$this->getName(), '', JURI::base(true)));
	}

    /**
     * Initialise application.
     *
     * @param	array	$options
     *
     * @return	void
     */
    public function initialise($options = array())
    {
        //Get the localisation information provided in the localise.xml file.
        $forced = $this->getLocalise();

        // Check the request data for the language.
        if (empty($options['language'])) {
            $requestLang = JRequest::getCmd('language', null);
            if (!is_null($requestLang)) {
                $options['language'] = $requestLang;
            }
        }

        // Check the session for the language.
        if (empty($options['language'])) {
            $sessionLang = MolajoFactory::getSession()->get('setup.language');
            if (!is_null($sessionLang)) {
                $options['language'] = $sessionLang;
            }
        }

        // This could be a first-time visit - try to determine what the application accepts.
        if (empty($options['language'])) {
            if (!empty($forced['language'])) {
                $options['language'] = $forced['language'];
            } else {
                $options['language'] = MolajoLanguageHelper::detectLanguage();
                if (empty($options['language'])) {
                    $options['language'] = 'en-GB';
                }
            }
        }

        // Give the user English
        if (empty($options['language'])) {
            $options['language'] = 'en-GB';
        }

        // Set the language in the class
        $conf = MolajoFactory::getConfig();
        $conf->set('language', $options['language']);
        $conf->set('debug_lang', $forced['debug']);
        $conf->set('sampledata', $forced['sampledata']);
    }

	/**
     * route
     *
	 * Route the application.
     *
     * @return void
     *
     * @since 1.0
     */
	public function route() {}

    /**
     * dispatch
     *
     * Execute the Component and render the results
     *
     * @param null $component
     *
     * @return void
     *
     * @since 1.0
     */
    public function dispatch($component = null)
    {
        try
        {
            // Get the component if not set.
            if ($component == null) {
                $component = JRequest::getCmd('option', 'com_installer');
            }

            JRequest::setVar('option', $component);
            $request = $this->componentRequest();

            $document	= MolajoFactory::getDocument();
            $user		= MolajoFactory::getUser();

            switch($document->getType())
            {
                case 'html' :
                    $document->setTitle(MolajoText::_($request['title']));
                    break;
                default :
                    break;
            }

            /** render the component */
            $contents = MolajoComponentHelper::renderComponent($request, $params = array());

            $document->setBuffer($contents, 'component');
        }

        // Mop up any uncaught exceptions.
        catch (Exception $e)
        {
            $code = $e->getCode();
            JError::raiseError($code ? $code : 500, $e->getMessage());
        }
    }

    /**
     * componentRequest
     *
     * populate the request object for the MVC
     *
     * @return array
     *
     * @since 1.0
     */
    private function componentRequest()
    {
        $request = array();

        $request['application_id'] = MOLAJO_APPLICATION_ID;
        $request['controller'] = 'display';
        $request['extension_type'] = 'component';

        $request['option'] = JRequest::getCmd('option', 'com_installer');
        $request['no_com_option'] = substr($request['option'], 4, 9999);
        $request['view'] = JRequest::getCmd('view', 'display');
        $request['layout'] = JRequest::getCmd('layout', 'installer_step1');
        $request['model'] = JRequest::getCmd('model', 'display');
        $request['task'] = JRequest::getCmd('task', 'display');
        $request['format'] = JRequest::getCmd('format', 'html');

        $request['wrap'] = JRequest::getCmd('wrap', 'none');
        $request['wrap_id'] = JRequest::getCmd('wrap_id', '');
        $request['wrap_class'] = JRequest::getCmd('wrap_class', '');
        $request['wrap_title'] = '';
        $request['wrap_subtitle'] = '';
        $request['wrap_date'] = '';
        $request['wrap_author'] = '';
        $request['wrap_more_array'] = array();

        $request['plugin_type'] = JRequest::getCmd('plugin_type', '');

        $request['id'] = 0;
        $request['cid'] = 0;
        $request['catid'] = 0;
        $request['params'] = array();
        $request['extension'] = 'component';
        $request['component_specific'] = '';

        $request['current_url'] = JURI::base();
        $request['component_path'] = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH.'/components/'.$request['option'];
        DEFINE('JPATH_COMPONENT', $request['component_path']) ;
        $request['base_url'] = MOLAJO_PATH_BASE;
        $request['item_id'] = null;

        $request['acl_implementation'] = 'core';
        $request['component_table'] = '__dummy';
        $request['filter_fieldname'] = '';
        $request['select_fieldname'] = '';

        $request['title'] = 'Molajo Installer: Step '.substr($request['layout'], -1);
        $request['subtitle'] = '';
        $request['metakey'] = '';
        $request['metadesc'] = '';
        $request['metadata'] = '';
        $request['position'] = '';

        JRequest::setVar('option', $request['option']);
        JRequest::setVar('no_com_option', $request['no_com_option']);
        JRequest::setVar('view', $request['view']);
        JRequest::setVar('layout', $request['layout']);
        JRequest::setVar('model', $request['model']);
        JRequest::setVar('task', $request['task']);
        JRequest::setVar('format', $request['format']);
        JRequest::setVar('wrap', $request['wrap']);
        JRequest::setVar('wrap_id', $request['wrap_id']);
        JRequest::setVar('wrap_class', $request['wrap_class']);
        JRequest::setVar('plugin_type', $request['plugin_type']);

        return $request;
    }

    /**
     * render
     *
     * Parse the Template and generate the JDoc statements
     *
     * @return void
     *
     * @since 1.0
     */
    public function render()
    {
        $document	= MolajoFactory::getDocument();
        $user		= MolajoFactory::getUser();

        // get the format to render
        $format = $document->getType();

        switch ($format)
        {
            case 'html':
            default:
                $template	= $this->getTemplate(true);
                $file		= JRequest::getCmd('tmpl', 'index');
                $params = array(
                    'template'	=> $template->template,
                    'file'		=> $file.'.php',
                    'directory'	=> MOLAJO_PATH_THEMES,
                    'params'	=> $template->params
                );
                break;
        }

        // Parse the document.
        $document = MolajoFactory::getDocument();
        $document->parse($params);

        $caching = false;

        // Render the document.
        JResponse::setBody($document->render($caching, $params));
    }

	/**
     * debugLanguage
     *
	 * @return	void
	 */
	public static function debugLanguage()
	{
		ob_start();
		$lang = MolajoFactory::getLanguage();
		echo '<h4>Parsing errors in language files</h4>';
		$errorfiles = $lang->getErrorFiles();

		if (count($errorfiles)) {
			echo '<ul>';

			foreach ($errorfiles as $file => $error)
			{
				echo "<li>$error</li>";
			}
			echo '</ul>';
		}
		else {
			echo '<pre>None</pre>';
		}

		echo '<h4>Untranslated Strings</h4>';
		echo '<pre>';
		$orphans = $lang->getOrphans();

		if (count($orphans)) {
			ksort($orphans, SORT_STRING);

			foreach ($orphans as $key => $occurance)
			{
				$guess = str_replace('_', ' ', $key);

				$parts = explode(' ', $guess);
				if (count($parts) > 1) {
					array_shift($parts);
					$guess = implode(' ', $parts);
				}

				$guess = trim($guess);


				$key = trim(strtoupper($key));
				$key = preg_replace('#\s+#', '_', $key);
				$key = preg_replace('#\W#', '', $key);

				// Prepare the text
				$guesses[] = $key.'="'.$guess.'"';
			}

			echo "\n\n# ".$file."\n\n";
			echo implode("\n", $guesses);
		}
		else {
			echo 'None';
		}
		echo '</pre>';
		$debug = ob_get_clean();
		JResponse::appendBody($debug);
	}

	/**
	 * getPathway
     *
     * Returns the application MolajoPathway object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  MolajoPathway  A MolajoPathway object
	 *
	 * @since  1.0
	 */
	public function getPathway($name = null, $options = array())
	{
		return null;
	}

	/**
	 * getMenu
     *
     * Returns the Menu object.
	 *
	 * @param   string  $name     The name of the application/application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  MolajoMenu  MolajoMenu object.
	 *
	 * @since  1.0
	 */
	public function getMenu($name = null, $options = array())
	{
		return null;
	}

	/**
     * setCfg
     *
	 * Set configuration values
	 *
	 * @param	array	$vars		Array of configuration values
	 * @param	string	$namespace	The namespace
	 *
	 * @return	void
	 */
	public function setCfg(array $vars = array(), $namespace = 'config')
	{
		$this->_registry->loadArray($vars, $namespace);
	}

	/**
	 * _createConfiguration
     *
     * Create the configuration registry
	 *
	 * @return	void
	 */
	public function _createConfiguration($file = null)
	{
		$this->_registry = new JRegistry('config');
	}

    /**
     * getTemplate
     *
     * Get the Template for the Application
     *
     * @param bool $params
     * @return stdClass|string
     */
	public function getTemplate($params = false)
	{
		if ((bool) $params) {
			$template = new stdClass();
			$template->template = 'install';
			$template->params = new JRegistry;
			return $template;
		}
		return 'install';
	}

	/**
     * _createSession
     *
	 * Create the user session
	 *
	 * @param	string	$name	The sessions name
	 *
	 * @return	object	JSession
	 */
	public function & _createSession($name)
	{
		$options = array();
		$options['name'] = $name;

		$session = MolajoFactory::getSession($options);
		if (is_a($session->get('registry'), 'JRegistry')) {
        } else {
			$session->set('registry', new JRegistry('session'));
		}

		return $session;
	}

	/**
     * getLocalise
     *
	 * Returns the language code and help url set in the localise.xml file.
	 * Used for forcing a particular language in localised releases.
	 *
	 * @return	bool|array	False on failure, array on success.
	 */
	public function getLocalise()
	{
		$xml = MolajoFactory::getXML(MOLAJO_PATH_SITE . '/installation/localise.xml');

		if ($xml) {
        } else {
			return false;
		}

		$ret = array();

		$ret['language'] = (string)$xml->forceLang;
		$ret['helpurl'] = (string)$xml->helpurl;
		$ret['debug'] = (string)$xml->debug;
		$ret['sampledata'] = (string)$xml->sampledata;

        /**
        <?xml version="1.0" encoding="utf-8"?>
        <localise version="1.6" client="installation" >
         <forceLang>da-DK</forceLang>
         <helpurl></helpurl>
         <debug>0</debug>
         <sampledata>sample_data_da.sql</sampledata>
         <params/>
        </localise>
         */
		return $ret;
	}

    /**
     * getLocaliseAdmin
     *
     * Returns the installed language files in the administrative and
     * front-end area.
     *
     * @param	boolean	$db
     *
     * @return array Array with installed language packs in admin and site area
     */
 	public function getLocaliseAdmin($db=false)
 	{
 		// Read the files in the admin area
 		$path = JLanguage::getLanguagePath(MOLAJO_PATH_SITE . '/administrator');
 		$langfiles['admin'] = JFolder::folders($path);

 		// Read the files in the site area
 		$path = JLanguage::getLanguagePath(MOLAJO_PATH_SITE);
 		$langfiles['site'] = JFolder::folders($path);

 		if ($db) {
 			$langfiles_disk = $langfiles;
 			$langfiles = Array();
 			$langfiles['admin'] = Array();
 			$langfiles['site'] = Array();
 			$query = $db->getQuery(true);
 			$query->select('element, application_id');
 			$query->from('#__extensions');
 			$query->where('type = '.$db->quote('language'));
 			$db->setQuery($query);
 			$langs = $db->loadObjectList();
 			foreach ($langs as $lang)
 			{
 				switch($lang->application_id)
 				{
 					case 0: // site
 						if (in_array($lang->element, $langfiles_disk['site'])) {
 							$langfiles['site'][] = $lang->element;
 						}
 						break;
 					case 1: // administrator
 						if (in_array($lang->element, $langfiles_disk['admin'])) {
 							$langfiles['admin'][] = $lang->element;
 						}
 						break;
 				}
 			}
 		}

 		return $langfiles;
 	}
}