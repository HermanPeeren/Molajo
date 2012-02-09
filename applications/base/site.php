<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoSite
 *
 * Acts as a Factory class for site specific functions and API options
 */
class MolajoSite
{
    /**
     * $instance
     *
     * @var    object
     * @since  1.0
     */
    public static $instance = null;

    /**
     * $config
     *
     * @var    integer
     * @since  1.0
     */
    protected $config = null;

    /**
     * $applications
     *
     * Applications the site is authorized to access
     *
     * @var    array
     * @since  1.0
     */
    protected $applications = null;

    /**
     * $parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * $custom_fields
     *
     * @var    array
     * @since  1.0
     */
    protected $custom_fields = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @return  site  object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance) {
        } else {
            self::$instance = new MolajoSite ();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * load
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @since 1.0
     */
    public function load()
    {

       $app = Molajo::Application();

        $app->load();
        $this->_setPaths();

        $info = SiteHelper::getSiteInfo();
        if ($info === false) {
            return false;
        }
        /** is site authorised? */
        $sc = new MolajoSite ();
        $authorise = $sc->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        $this->_custom_fields = new Registry;
        $this->_custom_fields->loadString($this->_siteQueryResults->custom_fields);

        $this->_parameters = new Registry;
        $this->_parameters->loadString($this->_siteQueryResults->parameters);

        $this->_metadata = new Registry;
        $this->_metadata->loadString($this->_siteQueryResults->metadata);

        $this->_base_url = $this->_siteQueryResults->base_url;
        echo 'here';
        die;


    }

    /**
     * authorise
     *
     * Check if the site is authorized for this application
     *
     * @param $application_id
     * @return boolean
     */
    public function authorise($application_id)
    {
        $this->_applications = SiteHelper::getSiteApplications();
        if ($this->_applications === false) {
            return false;
        }

        $found = false;
        foreach ($this->_applications as $single) {
            if ($single->application_id == $application_id) {
                $found = true;
            }
        }
        if ($found === true) {
            return true;
        }

        MolajoError::raiseError(403, TextServices::_('SITE_NOT_AUTHORIZED_FOR_APPLICATION'));
        return false;
    }

    /**
     * _setPaths
     *
     * Retrieves site configuration information and sets paths for site file locations
     *
     * @results  null
     * @since    1.0
     */
    protected function _setPaths()
    {
        if (defined('MOLAJO_SITE_NAME')) {
        } else {
            define('MOLAJO_SITE_NAME', self::get('site_name', MOLAJO_SITE_ID));
        }
        if (defined('MOLAJO_SITE_CACHE_FOLDER')) {
        } else {
            define('MOLAJO_SITE_CACHE_FOLDER', self::get('cache_path', MOLAJO_SITE_FOLDER_PATH . '/cache'));
        }
        if (defined('MOLAJO_SITE_LOGS_FOLDER')) {
        } else {
            define('MOLAJO_SITE_LOGS_FOLDER', self::get('logs_path', MOLAJO_SITE_FOLDER_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('MOLAJO_SITE_MEDIA_FOLDER')) {
        } else {
            define('MOLAJO_SITE_MEDIA_FOLDER', self::get('media_path', MOLAJO_SITE_FOLDER_PATH . '/media'));
        }
        if (defined('MOLAJO_SITE_MEDIA_URL')) {
        } else {
            define('MOLAJO_SITE_MEDIA_URL', MOLAJO_BASE_URL . self::get('media_url', MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/media'));
        }
        if (defined('MOLAJO_SITE_TEMP_FOLDER')) {
        } else {
            define('MOLAJO_SITE_TEMP_FOLDER', self::get('temp_path', MOLAJO_SITE_FOLDER_PATH . '/temp'));
        }
        if (defined('MOLAJO_SITE_TEMP_URL')) {
        } else {
            define('MOLAJO_SITE_TEMP_URL', MOLAJO_BASE_URL . self::get('temp_url', MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/temp'));
        }

        return;
    }

    /**
     * siteConfig
     *
     * Creates the Site Configuration object.
     *
     * return  null
     * @since  1.0
     */
    public function siteConfig()
    {
        $siteConfig = new ConfigurationService ();
        $data = $siteConfig->site();

        if (is_array($data)) {
            $this->_config->loadArray($data);

        } elseif (is_object($data)) {
            $this->_config->loadObject($data);
        }

        return;
    }

    /**
     * get
     *
     * Returns a property of the Application object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);
        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);
        } else {
            return $this->_config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property of the Application object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);
        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $value);
        } else {
            return $this->_config->get($key, $value);
        }
    }
}
