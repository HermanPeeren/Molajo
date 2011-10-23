<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Site
 *
 * Interacts with the Application Class for the Site Application
 *
 * @package		Molajo
 * @subpackage	Application
 */
class MolajoSite extends MolajoApplication
{
	/**
	 * @var object $template
     * 
     * @since 1.0
	 */
	private $template = null;
    
    /**
     * @var bool $_language_filter
     * 
     * @since 1.0
     */
	private $_language_filter = false;

    /**
     * @var bool $_detect_browser
     * 
     * @since 1.0
     */
	private $_detect_browser = false;

	/**
	 * __construct
     * 
     * Class constructor
	 *
     * @param array $config An optional associative array of configuration settings.
     * 
     * @since 1.0
     */
	public function __construct($config = array())
	{
		$config['applicationId'] = 0;
		parent::__construct($config);
	}

	/**
     * initialise
     *
	 * Initialise the application.
	 *
	 * @param	array
     * 
     * @since 1.0
     */
	public function initialise($options = array())
	{
		$config = MolajoFactory::getConfig();

		// if a language was specified it has priority
		// otherwise use user or default language settings
		MolajoPluginHelper::importPlugin('system', 'languagefilter');

		if (empty($options['language'])) {
			$lang = JRequest::getString('language', null);
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if ($this->_language_filter 
            && empty($options['language'])) {
			// Detect cookie language
			$lang = JRequest::getString(JUtility::getHash('language'), null ,'cookie');
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if (empty($options['language'])) {
			// Detect user language
			$lang = MolajoFactory::getUser()->getParam('language');
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if ($this->_detect_browser && empty($options['language'])) {
			// Detect browser language
			$lang = MolajoLanguageHelper::detectLanguage();
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if (empty($options['language'])) {
			// Detect default language
			$params = MolajoComponentHelper::getParams('com_languages');
			$application	= MolajoApplicationHelper::getApplicationInfo($this->getApplicationId());
			$options['language'] = $params->get($application->name, $config->get('language', 'en-GB'));
		}

		// One last check to make sure we have something
		if (!JLanguage::exists($options['language'])) {
			$lang = $config->get('language','en-GB');
			if (JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
			else {
				$options['language'] = 'en-GB'; 
			}
		}

		// Execute the parent initialise method.
		parent::initialise($options);
 
		// Load Library language
		$lang = MolajoFactory::getLanguage();
		$lang->load('lib_joomla', MOLAJO_PATH_BASE)
		|| $lang->load('lib_joomla', MOLAJO_PATH_SITE)
		|| $lang->load('lib_joomla', MOLAJO_PATH_ADMINISTRATOR);

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
	public function route()
	{
		parent::route();

		$Itemid = JRequest::getInt('Itemid');
		$this->authorise($Itemid);
	}

	/**
	 * dispatch
     * 
     * Dispatch the application
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
			if (!$component) {
				$component = JRequest::getCmd('option');
			}

			$document	= MolajoFactory::getDocument();
			$user		= MolajoFactory::getUser();
			$router		= $this->getRouter();
			$params		= $this->getParams();
            $option     = $component;

			switch($document->getType())
			{
				case 'html':
					// Get language
					$lang_code = MolajoFactory::getLanguage()->getTag();
					$languages = MolajoLanguageHelper::getLanguages('lang_code');

					// Set metadata
					if (isset($languages[$lang_code]) && $languages[$lang_code]->metakey) {
						$document->setMetaData('keywords', $languages[$lang_code]->metakey);
					} else {
						$document->setMetaData('keywords', $this->getCfg('MetaKeys'));
					}
					$document->setMetaData('rights', $this->getCfg('MetaRights'));
					$document->setMetaData('language', $lang_code);
					if ($router->getMode() == JROUTER_MODE_SEF) {
						$document->setBase(JURI::current());
					}
					break;

				case 'feed':
					$document->setBase(JURI::current());
					break;
			}

            $request = parent::getRequest();

			$document->setTitle($params->get('page_title'));
			$document->setDescription($params->get('page_description'));

            /** render the component */
			$contents = MolajoComponentHelper::renderComponent($request);
			$document->setBuffer($contents, 'component');

			// Trigger the onAfterDispatch event.
			MolajoPluginHelper::importPlugin('system');
			$this->triggerEvent('onAfterDispatch');
		}
		// Mop up any uncaught exceptions.
		catch (Exception $e)
		{
			$code = $e->getCode();
			JError::raiseError($code ? $code : 500, $e->getMessage());
		}
	}

    /**
     * render
     * 
     * execute the component and render the results
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
			case 'feed':
				$params = array();
				break;

			case 'html':
			default:
				$template	= $this->getTemplate(true);
				$file		= JRequest::getCmd('tmpl', 'index');

				if ($this->getCfg('offline')) {
                    if ($user->authorise('admin')) {
                    } else {
                        $uri		= MolajoFactory::getURI();
                        $return		= (string)$uri;
                        $this->setUserState('users.login.form.data',array( 'return' => $return ) );
                        $file = 'offline';
                        JResponse::setHeader('Status', '503 Service Temporarily Unavailable', 'true');
                    }
				} else {
                    if ($file == 'offline') {
                        $file = 'index';
                    }
                    if (is_dir(MOLAJO_PATH_THEMES.DS.$template->template)) {
                    } else {
                        $file = 'component';
                    }
                }
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

		// Trigger the onBeforeRender event.
		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onBeforeRender');

		$caching = false;
		if ($this->getCfg('caching') && $this->getCfg('caching', 2) == 2 && !$user->get('id')) {
			$caching = true;
		}

		// Render the document.
		JResponse::setBody($document->render($caching, $params));

		// Trigger the onAfterRender event.
		$this->triggerEvent('onAfterRender');
	}

    /**
     * authorise
     * 
     * Check if the user can access the application
     * 
     * @param $itemid
     * @return void
     */
	public function authorise($itemid)
	{
		$menus	= $this->getMenu();
		$user	= MolajoFactory::getUser();

		if (!$menus->authorise($itemid))
		{
			if ($user->get('id') == 0)
			{
				// Redirect to login
				$uri		= MolajoFactory::getURI();
				$return		= (string)$uri;

				$this->setUserState('users.login.form.data',array( 'return' => $return ) );

				$url	= 'index.php?option=com_users&view=login';
				$url	= MolajoRoute::_($url, false);

				$this->redirect($url, JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
			} else {
				JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
	}

	/**
     * getParams
     * 
	 * Get the application parameters
	 *
	 * @param	string	The component option
	 * @return	object	The parameters object
	 * @since	1.0
	 */
	public function getParams($option = null)
	{
		static $params = array();

		$hash = '__default';
		if (empty($option)) {
        } else {
			$hash = $option;
		}
		if (isset($params[$hash])) {
        } else {
			// Get component parameters
			if ($option) {
            } else {
				$option = JRequest::getCmd('option');
			}
			// Get new instance of component global parameters
			$params[$hash] = clone MolajoComponentHelper::getParams($option);

			// Get menu parameters
			$menus	= $this->getMenu();
			$menu	= $menus->getActive();

			// Get language
			$lang_code = MolajoFactory::getLanguage()->getTag();
			$languages = MolajoLanguageHelper::getLanguages('lang_code');

			$title = $this->getCfg('sitename');
			if (isset($languages[$lang_code]) && $languages[$lang_code]->metadesc) {
				$description = $languages[$lang_code]->metadesc;
			} else {
				$description = $this->getCfg('MetaDesc');
			}
			$rights = $this->getCfg('MetaRights');

			/** cascade menu item parameters */
			if (is_object($menu)) {
				$temp = new JRegistry;
				$temp->loadJSON($menu->params);
				$params[$hash]->merge($temp);
				$title = $menu->title;
			}

			$params[$hash]->def('page_title', $title);
			$params[$hash]->def('page_description', $description);
			$params[$hash]->def('page_rights', $rights);
		}

		return $params[$hash];
	}

	/**
	 * Get the application parameters
	 *
	 * @param	string	The component option
	 *
	 * @return	object	The parameters object
	 * @since	1.5
	 */
	public function getPageParameters($option = null)
	{
		return $this->getParams($option);
	}

	/**
	 * Get the template
	 *
	 * @return string The template name
	 * @since 1.0
	 */
	public function getTemplate($params = false)
	{
		if(is_object($this->template)) {
			if ($params) {
				return $this->template;
			}
			return $this->template->template;
		}

		// Get the id of the active menu item
		$menu = $this->getMenu();
		$item = $menu->getActive();
		if (!$item) {
			$item = $menu->getItem(JRequest::getInt('Itemid'));
		}

		$id = 0;
		if (is_object($item)) { // valid item retrieved
			$id = $item->template_style_id;
		}
		$condition = '';

		$tid = JRequest::getVar('template', 0);
		if (is_int($tid) && $tid > 0) {
			$id = (int) $tid;
		}

		$cache = MolajoFactory::getCache('com_templates', '');
		if ($this->_language_filter) {
			$tag = MolajoFactory::getLanguage()->getTag();
		} else {
			$tag ='';
		}
		if ($templates = $cache->get('templates0'.$tag)) {
        } else {
			$db = MolajoFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, home, template, params');
			$query->from('#__template_styles');
			$query->where('application_id = '.MOLAJO_APPLICATION_ID);
			$db->setQuery($query);

			$templates = $db->loadObjectList('id');

			foreach($templates as &$template) {
				$registry = new JRegistry;
				$registry->loadJSON($template->params);
				$template->params = $registry;

				// Create home element
				if ($template->home == '1' && !isset($templates[0])
                    || $this->_language_filter && $template->home == $tag) {
					$templates[0] = clone $template;
				}
			}
			$cache->store($templates, 'templates0'.$tag);
		}

		$template = $templates[$id];

		// Allows for overriding the active template from the request
		$template->template = JRequest::getCmd('template', $template->template);
		$template->template = JFilterInput::getInstance()->clean($template->template, 'cmd'); // need to filter the default value as well

		// Fallback template
		if (!file_exists(MOLAJO_PATH_THEMES.DS.$template->template.DS.'index.php')) {
			JError::raiseWarning(0, JText::_('JERROR_ALERTNOTEMPLATE'));
		    $template->template = MOLAJO_APPLICATION_DEFAULT_TEMPLATE;
		    if (file_exists(MOLAJO_PATH_THEMES.'/'.MOLAJO_APPLICATION_DEFAULT_TEMPLATE.'/index.php')) {
            } else {
		    	$template->template = '';
		    }
		}

		// Cache the result
		$this->template = $template;
		if ($params) {
			return $template;
		}
		return $template->template;
	}

	/**
	 * Overrides the default template that would be used
	 *
	 * @param string The template name
	 */
	public function setTemplate($template)
	{
		if (is_dir(MOLAJO_PATH_THEMES.DS.$template)) {
			$this->template = new stdClass();
			$this->template->params = new JRegistry;
			$this->template->template = $template;
		}
	}

	/**
	 * Return a reference to the Menu object.
	 *
	 * @param	string	$name		The name of the application/application.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	object	MolajoMenu.
	 * @since	1.5
	 */
	public function getMenu($name = null, $options = array())
	{
		$options	= array();
		$menu		= parent::getMenu('site', $options);
		return $menu;
	}

	/**
	 * Return a reference to the MolajoPathway object.
	 *
	 * @param	string	$name		The name of the application.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	object MolajoPathway.
	 * @since	1.5
	 */
	public function getPathway($name = null, $options = array())
	{
		$options = array();
		$pathway = parent::getPathway('site', $options);
		return $pathway;
	}

	/**
	 * Return a reference to the MolajoRouter object.
	 *
	 * @param	string	$name		The name of the application.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	MolajoRouter
	 * @since	1.5
	 */
	static public function getRouter($name = null, array $options = array())
	{
		$config = MolajoFactory::getConfig();
		$options['mode'] = $config->get('sef');
		$router = parent::getRouter('site', $options);
		return $router;
	}

	/**
	 * Return the current state of the language filter.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getLanguageFilter()
	{
		return $this->_language_filter;
	}

	/**
	 * Set the current state of the language filter.
	 *
	 * @return	boolean	The old state
	 * @since	1.6
	 */
	public function setLanguageFilter($state=false)
	{
		$old = $this->_language_filter;
		$this->_language_filter=$state;
		return $old;
	}
	/**
	 * Return the current state of the detect browser option.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getDetectBrowser()
	{
		return $this->_detect_browser;
	}

	/**
	 * Set the current state of the detect browser option.
	 *
	 * @return	boolean	The old state
	 * @since	1.6
	 */
	public function setDetectBrowser($state=false)
	{
		$old = $this->_detect_browser;
		$this->_detect_browser=$state;
		return $old;
	}

	/**
	 * Redirect to another URL.
	 *
	 * Optionally enqueues a message in the system message queue (which will be displayed
	 * the next time a page is loaded) using the enqueueMessage method. If the headers have
	 * not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param	string	The URL to redirect to. Can only be http/https URL
	 * @param	string	An optional message to display on redirect.
	 * @param	string  An optional message type.
	 * @param	boolean	True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 * @param	boolean	True if the enqueued messages are passed to the redirection, false else.
	 * @return	none; calls exit().
	 * @since	1.5
	 * @see		JApplication::enqueueMessage()
	 */
	public function redirect($url, $msg='', $msgType='message', $moved = false, $persistMsg = true)
	{
		if (!$persistMsg) {
			$this->_messageQueue = array();
		}
		parent::redirect($url, $msg, $msgType, $moved);
	}


    /**
     * Deprecated
     */
	public function getClientId()
	{
		return parent::getApplicationId();
	}
}
