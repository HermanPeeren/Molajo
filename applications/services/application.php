<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoApplicationService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Input
     *
     * @var    object
     * @since  1.0
     */
    public $input;

    /**
     * Configuration for Site and Application
     *
     * @static
     * @var    $connection
     * @since  1.0
     */
    public $configuration = array();

    /**
     * Custom Fields
     *
     * @static
     * @var    $custom_fields
     * @since  1.0
     */
    public $custom_fields = array();

    /**
     * Metadata
     *
     * @static
     * @var    $metadata
     * @since  1.0
     */
    public $metadata = array();

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoApplicationService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * @return  object
     * @throws  RuntimeException
     * @since   1.0
     */
    public function __construct()
    {

    }

    /**
     * __construct
     *
     * Retrieves and combines site and application configuration objects
     *
     * @return  object
     * @throws  RuntimeException
     * @since   1.0
     */
    public function connect()
    {
        $this->configuration = new Registry;
        $siteData = new Registry;

        /** Site Configuration: php file */
        $siteData = $this->getSite();
        foreach ($siteData as $key => $value) {
            $this->set($key, $value);
        }

        /** Application Table entry for each application - parameters field has config */
        $appConfig = $this->getApplicationInfo();

        $this->metadata = new Registry;
        $this->metadata->loadString($appConfig->metadata, 'JSON');

        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($appConfig->custom_fields, 'JSON');

        // todo: amy check this after the interface is working and not test data
        $parameters = substr($appConfig->parameters, 1, strlen($appConfig->parameters) - 2);
        $parameters = substr($parameters, 0, strlen($parameters) - 1);
        $parmArray = array();
        $parmArray = explode(',', $parameters);
        foreach ($parmArray as $entry) {
            $pair = explode(':', $entry);
            $key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
            if (trim($pair[0]) == '') {
            } else {
                $value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * getSite
     *
     * retrieve site configuration object from ini file
     *
     * @return object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getSite()
    {
        $siteConfigData = array();

        $file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $siteConfigData = new MolajoSiteConfiguration();
        return $siteConfigData;
    }

    /**
     * getApplicationInfo
     *
     * @return  boolean
     * @since   1.0
     */
    public function getApplicationInfo()
    {
        $row = new stdClass();
        $id = 0;

        if (MOLAJO_APPLICATION == 'installation') {

            $id = 0;
            $row->id = 0;
            $row->name = MOLAJO_APPLICATION;
            $row->path = MOLAJO_APPLICATION;
            $row->asset_type_id = MOLAJO_ASSET_TYPE_BASE_APPLICATION;
            $row->description = '';
            $row->custom_fields = '';
            $row->parameters = '';
            $row->metadata = '';

        } else {

            $db = Molajo::Services()->connect('jdb');
            $query = $db->getQuery(true);

            $query->select($db->namequote('id'));
            $query->select($db->namequote('asset_type_id'));
            $query->select($db->namequote('name'));
            $query->select($db->namequote('path'));
            $query->select($db->namequote('description'));
            $query->select($db->namequote('custom_fields'));
            $query->select($db->namequote('parameters'));
            $query->select($db->namequote('metadata'));
            $query->from($db->namequote('#__applications'));
            $query->where($db->namequote('name') .
                ' = ' . $db->quote(MOLAJO_APPLICATION));
            $db->setQuery($query->__toString());
            $results = $db->loadObjectList();

            if ($db->getErrorNum()) {
                return new MolajoException($db->getErrorMsg());
            }

            if (count($results) == 0) {
                // todo: amy error;
            }

            foreach ($results as $result) {
                $row->id = $result->id;
                $id = $result->id;
                $row->name = $result->name;
                $row->path = $result->path;
                $row->asset_type_id = $result->asset_type_id;
                $row->description = $result->description;
                $row->custom_fields = $result->custom_fields;
                $row->parameters = $result->parameters;
                $row->metadata = $result->metadata;
            }
        }

        if (defined('MOLAJO_APPLICATION_ID')) {
        } else {
            define('MOLAJO_APPLICATION_ID', $id);
        }

        return $row;
    }

    /**
     * get
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null, $type = null)
    {
        return $this->configuration->get($key, $default);
    }

    /**
     * set
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $type = null)
    {
        return $this->configuration->set($key, $value);
    }
}

