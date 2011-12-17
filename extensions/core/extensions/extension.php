<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Extension Class
 *
 * Base class
 */
class MolajoExtension
{
    /**
     * Instance
     *
     * @var string
     * @since  1.0
     */
    protected static $instance;

    /**
     * Configuration
     *
     * @var    integer
     * @since  1.0
     */
    protected $config = null;

    /**
     * @var    Document  The application document object.
     * @since  11.3
     */
    protected $document;

    /**
     *  User
     *
     * @var string
     * @since 1.0
     */
    protected $user = null;

    /**
     * Template
     *
     * @var object
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   mixed  $document    An optional argument to provide dependency injection for the application's
     *                              document object.  If the argument is a document object that object will become
     *                              the application's document object, if it is false then there will be no document
     *                              object, and if it is null then the default document object will be created based
     *                              on the application's loadDocument() method.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($id = null, $config = array(), $document = null)
    {

        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
        }
        $this->getConfig();

        if ($document instanceof MolajoDocument) {
            $this->document = $document;

        } elseif ($document === false) {

        } else {
            $this->loadDocument();
        }

       // $this->loadUser();

        /** todo: asset record */

        /** todo: primary category */

        /** todo: component */

        /** todo: authorized */

        /** todo: template */

        /** todo: page */

        /** todo: menu item */

        /** Event */
     //   MolajoPlugin::importPlugin('system');
     //   MolajoFactory::getApplication()->triggerEvent('onAfterInitialise');
    }

    /**
     * Execute Extension
     *
     * @return  void
     *
     * @since   1.0
     */
    public function execute()
    {
        MolajoFactory::getApplication()->triggerEvent('onBeforeExecute');
        MolajoFactory::getApplication()->triggerEvent('onAfterExecute');
    }

    /**
     * Render Extensions
     *
     * @return  void
     *
     * @since   1.0
     */
    public function render()
    {
        MolajoFactory::getApplication()->triggerEvent('onBeforeRender');
        MolajoFactory::getApplication()->triggerEvent('onAfterRender');
    }

    /**
     * route
     *
     * Route the application.
     *
     * Routing is the process of examining the request environment to determine which
     * component should receive the request. The component optional parameters
     * are then set in the request object to be processed when the application is being
     * dispatched.
     *
     * @return  void;
     * @since  1.0
     */
    public function route()
    {
        /** todo: amy 404 processing */
        if ($itemid = JRequest::getInt('Itemid')) {
            $this->authorise($itemid);
        }
        $uri = JURI::getInstance();

        $router = $this->getRouter();
        $result = $router->parse($uri);

        JRequest::set($result, 'get', false);

        if ($this->get('force_ssl') >= 1
            && strtolower($uri->getScheme()) != 'https'
        ) {
            $uri->setScheme('https');
            $this->redirect((string)$uri);
        }

        /** trigger onAfterRoute Event */
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * authorise
     *
     * Check if the user can access the application
     *
     * @param $itemid
     * @return booleon
     */
    public function authorise($itemid)
    {
        $menus = $this->getMenu();

        if ($menus == null) {
            return false;
        }

        if ($menus->authorise($itemid)) {
            return true;
        }

        /** Not authorized */
        if (MolajoFactory::getUser()->get('guest')) {
            $uri = MolajoFactory::getURI();
            $return = (string)$uri;
            $url = 'index.php?option=users&view=login&return=' . $return;
            $url = MolajoRouteHelper::_($url, false);
            $this->redirect($url, MolajoTextHelper::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));

            return false;
        }

        MolajoError::raiseError(403, MolajoTextHelper::_('ERROR_NOT_AUTHORIZED'));
        return false;
    }

    /**
     * getRouter
     *
     * Returns the application router object.
     *
     * @param   string  $name     The name of the application.
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  router object
     *
     * @since  1.0
     */
    static public function getRouter($name = null, array $options = array())
    {
        if (isset($name)) {
        } else {
            $name = MOLAJO_EXTENSION;
        }

        $router = MolajoRouter::getInstance($name, $options);
        if (MolajoError::isError($router)) {
            return null;
        }

        return $router;
    }

    /**
     * getMenu
     *
     * Returns the Menu object.
     *
     * @param   string  $name     The name of the application
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  menu object.
     *
     * @since  1.0
     */
    public function getMenu($name = null, $options = array())
    {
        $menu = MolajoMenu::getInstance($name, $options);

        if (MolajoError::isError($menu)) {
            return null;
        }
        return $menu;
    }

    /**
     * Returns the application pathway object.
     *
     * @param   string    $name     The name of the application.
     * @param   array     $options  An optional associative array of configuration settings.
     *
     * @return  object  A pathway object
     *
     * @since   11.1
     */
    public function getPathway($name = null, $options = array())
    {
        if (isset($name)) {
        } else {
            $name = MOLAJO_EXTENSION;
        }

        $pathway = MolajoPathway::getInstance($name, $options);

        if (MolajoError::isError($pathway)) {
            return null;
        }

        return $pathway;
    }

    /**
     * getTemplate
     *
     * return the folder name of the template
     * @param $template
     * @return string
     */
    function getTemplate()
    {
        return MolajoTemplate::getTemplate();
    }

    /**
     * Overrides the default template that would be used
     *
     * @param string The template name
     */
    public function setTemplate($template)
    {
        if (is_dir(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template)) {
            $this->template = new stdClass();
            $this->template->parameters = new JRegistry;
            $this->template->template = $template;
        }
    }

    /**
     *  loadUser
     *
     */
    private function loadUser()
    {
        $this->user = MolajoFactory::getUser();
    }

    /**
     * getUserState
     *
     * Gets a user state.
     *
     * @param   string  The path of the state.
     * @param   mixed   Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     *
     * @since  1.0
     */
    public function getUserState($key, $default = null)
    {
        $session = MolajoFactory::getSession();

        $registry = $session->get('registry');

        if (is_null($registry)) {
        } else {
            return $registry->get($key, $default);
        }

        return $default;
    }

    /**
     * setUserState
     *
     * Sets the value of a user state variable.
     *
     * @param   string  The path of the state.
     * @param   string  The value of the variable.
     *
     * @return  mixed   The previous state, if one existed.
     *
     * @since  1.0
     */
    public function setUserState($key, $value)
    {
        $session = MolajoFactory::getSession();
        $registry = $session->get('registry');

        if (is_null($registry)) {
        } else {
            return $registry->set($key, $value);
        }

        return null;
    }

    /**
     * getUserStateFromRequest
     *
     * Gets the value of a user state variable.
     *
     * @param   string   $key      The key of the user state variable.
     * @param   string   $request  The name of the variable passed in a request.
     * @param   string   $default  The default value for the variable if not found. Optional.
     * @param   string   $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
     *
     * @return  The request user state.
     *
     * @since  1.0
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = JRequest::getVar($request, null, 'default', $type);

        // Save the new value only if it was set in this request.
        if ($new_state == null) {
            $new_state = $cur_state;
        } else {
            $this->setUserState($key, $new_state);
        }

        return $new_state;
    }

    /**
     * Registers a handler to a particular event group.
     *
     * @param   string    $event    The event name.
     * @param   callback  $handler  The handler, a function or an instance of a event object.
     *
     * @return  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function registerEvent($event, $handler)
    {
        return MolajoFactory::getApplication()->register($event, $handler);
    }

    /**
     * Calls all handlers associated with an event group.
     *
     * @param   string  $event  The event name.
     * @param   array   $args   An array of arguments (optional).
     *
     * @return  array   An array of results from each function call, or null if no dispatcher is defined.
     *
     * @since   11.3
     */
    public function triggerEvent($event, array $args = null)
    {
        MolajoFactory::getApplication()->trigger($event, $args);
        return null;
    }

    /**
     *  CONFIGURATION
     */

    /**
     * getConfig
     *
     * Creates the Extension configuration object.
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    public function getConfig()
    {
        $configData = array();

        $file = MOLAJO_EXTENSIONS_CORE . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Extension Configuration File does not exist');
        }

        $configData = new MolajoExtensionConfiguration();

        if (is_array($configData)) {
            $this->config->loadArray($configData);

        } elseif (is_object($configData)) {
            $this->config->loadObject($configData);
        }

        return;
    }

    /**
     * get
     *
     * Returns a property of the Extension object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   11.3
     */
    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Extension object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   11.3
     */
    public function set($key, $value = null)
    {
        $this->config->set($key, $value);
    }

//** todo: shit can document */

        /**
         * Method to get the application document object.
         *
         * @return  MolajoDocument  The document object
         *
         * @since   11.3
         */
        public function getDocument()
        {
            return $this->document;
        }

        /**
         * Method to create a document for the Web application.  The logic and options for creating this
         * object are adequately generic for default cases but for many applications it will make sense
         * to override this method and create document objects based on more specific needs.
         *
         * @return  void
         *
         * @since   11.3
         */
        protected function loadDocument()
        {
            $this->document = JFactory::getDocument();
        }

}