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
     *  Request formatted for query
     *
     * @var string
     * @since 1.0
     */
    protected $query_request = null;

    /**
     *  Request
     *
     * @var string
     * @since 1.0
     */
    public $request = null;

    /**
     *  SEF Request
     *
     * @var string
     * @since 1.0
     */
    public $sef_request = null;

    /**
     *  Home
     *
     * @var boolean
     * @since 1.0
     */
    public $home = null;

    /**
     *  Option
     *
     * @var string
     * @since 1.0
     */
    public $option = null;

    /**
     *  Task
     *
     * @var string
     * @since 1.0
     */
    public $task = null;

    /**
     *  View
     *
     * @var string
     * @since 1.0
     */
    public $view = null;

    /**
     *  Wrap
     *
     * @var string
     * @since 1.0
     */
    public $wrap = null;

    /**
     *  Format
     *
     * @var string
     * @since 1.0
     */
    public $format = null;

    /**
     *  Template
     *
     * @var integer
     * @since 1.0
     */
    public $template = null;

    /**
     *  Template Page
     *
     * @var string
     * @since 1.0
     */
    public $page = null;

    /**
     *  Static
     *
     * @var integer
     * @since 1.0
     */
    public $static = null;

    /**
     *  Id
     *
     * @var integer
     * @since 1.0
     */
    public $id = null;

    /**
     *  Ids
     *
     * @var integer
     * @since 1.0
     */
    public $ids = null;

    /**
     *  Message
     *
     * @var integer
     * @since 1.0
     */
    public $message = null;

    /**
     *  Asset
     *
     * @var integer
     * @since 1.0
     */
    public $asset_id = null;

    /**
     *  Asset Type ID
     *
     * @var integer
     * @since 1.0
     */
    public $asset_type_id = null;

    /**
     *  Source Table
     *
     * @var string
     * @since 1.0
     */
    public $source_table = null;

    /**
     *  Source ID
     *
     * @var integer
     * @since 1.0
     */
    public $source_id = null;

    /**
     *  Source Parameters
     *
     * @var integer
     * @since 1.0
     */
    public $source_parameters = null;

    /**
     *  Component
     *
     * @var integer
     * @since 1.0
     */
    public $component_id = null;

    /**
     *  Component
     *
     * @var integer
     * @since 1.0
     */
    public $component_parameters = null;

    /**
     *  Language
     *
     * @var string
     * @since 1.0
     */
    public $language = null;

    /**
     *  Translation of ID
     *
     * @var integer
     * @since 1.0
     */
    public $translation_of_id = null;

    /**
     *  Redirect to ID
     *
     * @var integer
     * @since 1.0
     */
    public $redirect_to_id = null;

    /**
     *  View Group ID
     *
     * @var integer
     * @since 1.0
     */
    public $view_group_id = null;

    /**
     *  Primary Category ID
     *
     * @var integer
     * @since 1.0
     */
    public $primary_category_id = null;

    /**
     *  Title
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_title = null;

    /**
     *  Meta Description
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_description = null;

    /**
     *  Meta Keywords
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_keywords = null;

    /**
     *  Meta Author
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_author = null;

    /**
     *  Meta Content Rights
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_content_rights = null;

    /**
     *  Meta Robots
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_robots = null;

    /**
     *  Found
     *
     * @var boolean
     * @since 1.0
     */
    public $found = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $request    An optional argument to provide dependency injection for the asset
     * @param   null    $asset_id   An optional argument to provide dependency injection for the asset
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($request = null, $asset_id = null)
    {
        /** Specific URL path (less host, path and application) */
        if ($request == null) {
        } else {
            $this->query_request = $request;
        }

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->asset_id = 0;
        } else {
            $this->asset_id = $asset_id;
        }
    }

    /**
     * load
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output
     *
     * @return bool|null
     */
    public function load()
    {
        /** Request */
        $this->_getSEFOptions();

        /** home: duplicate content - redirect */
        if ($this->query_request == 'index.php'
            || $this->query_request == 'index.php/'
            || $this->query_request == 'index.php?'
            || $this->query_request == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('home_asset_id'), 301);
            return;
        }

        /** Home */
        if ($this->query_request == ''
            && (int)$this->asset_id == 0
        ) {
            $this->asset_id = MolajoController::getApplication()->get('home_asset_id', 0);
            $this->home = true;
        }

        /** Site offline */
        if (MolajoController::getApplication()->get('offline', 0) == 1) {
            MolajoController::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', 'true');
            $this->asset_id = MolajoController::getApplication()->get('asset_id', 0);
            $this->template = MolajoController::getApplication()->get('offline_template', 'system');
            $this->page = MolajoController::getApplication()->get('offline_page', 'full');
            $this->view = MolajoController::getApplication()->get('offline_view', 'offline');
            $this->wrap = MolajoController::getApplication()->get('offline_wrap', 'div');
            $this->format = MolajoController::getApplication()->get('offline_format', 'static');
            $this->message = MolajoController::getApplication()->get('offline_message', 'This site is not available.<br /> Please check back again soon.');
        }

        /** Get Asset Information */
        $this->_getAsset();

        /** Logged on Requirement */
        if (MolajoController::getApplication()->get('logon_requirement', 0) > 0
            && MolajoController::getUser()->get('guest', true) === true
            && $this->asset_id <> MolajoController::getApplication()->get('logon_requirement', 0)
        ) {
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('logon_requirement', 0), 303);
            return;
        }

        /** Route */
        //        $this->_route($this);

        /** 404 Not Found */
        if ($this->found === false) {
            MolajoController::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $this->template = MolajoController::getApplication()->get('error_template', 'system');
            $this->page = MolajoController::getApplication()->get('error_page', 'print');
            $this->view = MolajoController::getApplication()->get('error_view', 'error');
            $this->wrap = MolajoController::getApplication()->get('error_wrap', 'none');
        }

        /** act on redirect_to_id */
        if ($this->redirect_to_id == 0) {
        } else {
            MolajoController::getApplication()->redirect($this->redirect_to_id, 301);
            return;
        }

        /** acl check */
        $this->_authorise();

        /** 403 Not Found */
        if ($this->found === false) {
            MolajoController::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $this->template = MolajoController::getApplication()->get('error_template', 'system');
            $this->page = MolajoController::getApplication()->get('error_page', 'print');
            $this->view = MolajoController::getApplication()->get('error_view', 'error');
            $this->wrap = MolajoController::getApplication()->get('error_wrap', 'none');
        }

        /** retrieve metadata and parameters */
        $this->_getMetaData();

        /** render output */
        $this->_setMetaData();
        //echo '<pre>';var_dump($this);echo '</pre>';
        $this->_renderDocumentType();

        /** return to application */
        return;
    }

    /**
     * _getSEFOptions
     *
     * Request is stripped of Host, Folder, and Application
     *  Path ex. index.php?option=login or access/groups
     *
     * @param null $request
     * @return mixed
     */
    protected function _getSEFOptions($request = null)
    {
        /** Application SEF Options */
        $sef = MolajoController::getApplication()->get('sef', 1);
        $sef_rewrite = MolajoController::getApplication()->get('sef_rewrite', 0);
        $sef_suffix = MolajoController::getApplication()->get('sef_suffix', 0);
        $unicodeslugs = MolajoController::getApplication()->get('unicodeslugs', 0);
        $force_ssl = MolajoController::getApplication()->get('force_ssl', 0);

        /** Path ex. index.php?option=login or access/groups */
        if ($request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $request;
        }

        /** duplicate content: URLs without the .html */
        $sef_suffix = 1;
        if ($sef_suffix == 1 && substr($path, -11) == '/index.html') {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($sef_suffix == 1 && substr($path, -5) == '.html') {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->query_request = $path;

        return;
    }

    /**
     * _getAsset
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getAsset()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_id'));
        $query->select('a.' . $db->nameQuote('template_page'));
        $query->select('a.' . $db->nameQuote('language'));
        $query->select('a.' . $db->nameQuote('translation_of_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$this->asset_id == 0) {
            if (MolajoController::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($this->query_request));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($this->query_request));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->asset_id);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), MOLAJO_MESSAGE_TYPE_ERROR);
            return false;
        }

        if (count($results) == 0) {
            $this->found = false;

        } else {
            $this->found = true;
            foreach ($results as $result) {

                if ($this->asset_id == MolajoController::getApplication()->get('home_asset_id')) {
                    $this->home = true;
                } else {
                    $this->home = false;
                }
                $this->option = $result->option;
                $template_id = $result->template_id;
                if ((int)$template_id == 0) {
                    $this->template = $this->_getTemplate($template_id);
                }
                $this->page = $result->template_page;
                $this->asset_id = $result->asset_id;
                $this->asset_type_id = $result->asset_type_id;
                $this->source_table = $result->source_table;
                $this->source_id = $result->source_id;
                $this->language = $result->language;
                $this->translation_of_id = $result->translation_of_id;
                $this->redirect_to_id = $result->redirect_to_id;
                $this->view_group_id = $result->view_group_id;
                $this->primary_category_id = $result->primary_category_id;

                $this->request = $result->request;
                $this->sef_request = $result->sef_request;

                $parameterArray = array();
                $temp = substr($this->request, 10, (strlen($this->request) - 10));
                $parameterArray = explode('&', $temp);

                foreach ($parameterArray as $parameter) {

                    $pair = explode('=', $parameter);

                    if ($pair[0] == 'task') {
                        $this->task = $pair[1];

                    } elseif ($pair[0] == 'format') {
                        $this->format = $pair[1];

                    } elseif ($pair[0] == 'option') {
                        $this->option = $pair[1];

                    } elseif ($pair[0] == 'view') {
                        $this->view = $pair[1];

                    } elseif ($pair[0] == 'wrap') {
                        $this->wrap = $pair[1];

                    } elseif ($pair[0] == 'template') {
                        $this->template = $pair[1];

                    } elseif ($pair[0] == 'page') {
                        $this->page = $pair[1];

                    } elseif ($pair[0] == 'static') {
                        $this->static = $pair[1];

                    } elseif ($pair[0] == 'ids') {
                        $this->ids = $pair[1];
                        
                    } elseif ($pair[0] == 'id') {
                        $this->id = $pair[1];
                    }
                }
            }
        }
    }

    /**
     * _getTemplate
     *
     * Get Template Name using the Template ID
     *
     * @param $template_id
     */
    protected function _getTemplate($template_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('title'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$template_id);

        $db->setQuery($query->__toString());

        $result = $db->loadResult();
    }

    /**
     *  _getMetaData
     *
     *  Retrieves metadata for page
     */
    protected function _getMetaData()
    {
        /** need to know if this is for edit or display - list or item - or is it a static page */
        /** Priority 1: Request Override */
        $this->_getSEFOptionsParameters();

        /** Priority 2: Asset */
        // already collected in getAsset

        /** Priority 3: Source Table ID */
        $this->_getSourceData();

        /** Priority 4: Menu Item */

        /** Priority 5: Primary List Category ID */
        if ((int)$this->primary_category_id == 0) {
        } else {
            $this->getPrimaryCategory();
        }

        /** Priority 6: Component */
        $this->_getComponent();

        /** Priority 7: Application (static, items, item, edit) */
        $this->application_template = MolajoController::getApplication()->get('default_template');
        $this->application_page = MolajoController::getApplication()->get('default_page');
        $this->default_view_items = MolajoController::getApplication()->get('default_view_items');
        $this->default_wrap_items = MolajoController::getApplication()->get('default_wrap_items');

        /** Priority 8: System-defined */
        if ($this->template == null) {
            $this->template = MolajoController::getApplication()->get('default_template');
        }
        if ($this->page == null) {
            $this->page = MolajoController::getApplication()->get('default_page');
        }
    }

    /**
     *  _getSEFOptionsParameters
     *
     *  Retrieve Template and Template Page overrides from URL
     *  todo: amy add parameter to turn this off in the template manager
     */
    protected function _getSEFOptionsParameters()
    {
        $parameterArray = array();
        $temp = substr(MOLAJO_PAGE_REQUEST, 10, (strlen(MOLAJO_PAGE_REQUEST) - 10));
        $parameterArray = explode('&', $temp);

        foreach ($parameterArray as $parameter) {
            $pair = explode('=', $parameter);
            if ($pair[0] == 'view') {
                $this->view = $pair[1];
            } elseif ($pair[0] == 'wrap') {
                $this->wrap = $pair[1];
            } elseif ($pair[0] == 'template') {
                $this->template = $pair[1];
            } elseif ($pair[0] == 'page') {
                $this->page = $pair[1];
            }
        }
    }

    /**
     * _getSourceData
     *
     * Retrieve Parameters and MetaData for Source Detail Row
     *
     * @return  array
     * @since   1.0
     */
    protected function _getSourceData()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('extension_instance_id'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#' . $this->source_table) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->source_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->source_parameters = $parameters;

                $this->component_id = $result->extension_instance_id;
            }
        }
        //    echo '<pre>';var_dump($this->source_parameters);'</pre>';
        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * getPrimaryCategory
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  array
     * @since   1.0
     */
    protected function getPrimaryCategory()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__content') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->primary_category_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
            }
        }

        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * _getComponent
     *
     * Retrieve the Parameters and Meta Data for Component
     *
     * @return  array
     * @since   1.0
     */
    protected function _getComponent()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->component_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->component_parameters = $parameters;
            }
        }
        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (template, page, view, wrap, and various metadata)
     *
     * @param null $sourceParameters
     * @param null $sourceMetadata
     */
    protected function _setPageValues($sourceParameters = null, $sourceMetadata = null)
    {
        $parameters = new JRegistry;
        $parameters->loadString($sourceParameters);

        if ($this->template == '' || $this->template == null) {
            $this->template = $parameters->get('template', '');
        }
        if ($this->page == '' || $this->page == null) {
            $this->page = $parameters->get('page', '');
        }
        if ($this->view == '' || $this->view == null) {
            $this->view = $parameters->get('view', '');
        }
        if ($this->wrap == '' || $this->wrap == null) {
            $this->wrap = $parameters->get('wrap', '');
        }

        $metadata = new JRegistry;
        $metadata->loadString($sourceMetadata);

        if ($this->meta_title == '' || $this->meta_title == null) {
            $this->meta_title = $metadata->get('meta_title', '');
        }
        if ($this->meta_description == '' || $this->meta_description == null) {
            $this->meta_description = $metadata->get('meta_description', '');
        }
        if ($this->meta_keywords == '' || $this->meta_keywords == null) {
            $this->meta_keywords = $metadata->get('meta_keywords', '');
        }
        if ($this->meta_author == '' || $this->meta_author == null) {
            $this->meta_author = $metadata->get('meta_author', '');
        }
        if ($this->meta_content_rights == '' || $this->meta_content_rights == null) {
            $this->meta_content_rights = $metadata->get('meta_content_rights', '');
        }
        if ($this->meta_robots == '' || $this->meta_robots == null) {
            $this->meta_robots = $metadata->get('meta_robots', '');
        }
    }

    /**
     * getRedirectURL
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  boolean
     * @since   1.0
     */
    public static function getRedirectURL($asset_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        if ((int)$asset_id == MolajoController::getApplication()->get('home_asset_id', 0)) {
            return '';
        }

        if (MolajoController::getApplication()->get('sef', 1) == 0) {
            $query->select('a.' . $db->nameQuote('sef_request'));
        } else {
            $query->select('a.' . $db->nameQuote('request'));
        }

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);

        $db->setQuery($query->__toString());

        return $db->loadResult();

        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     *  _setMetaData
     *
     * Establish the meta data for this web page
     */
    protected function _setMetaData()
    {
        MolajoController::getApplication()->setTitle($this->meta_title);
        MolajoController::getApplication()->setDescription($this->meta_description);
        MolajoController::getApplication()->setMetaData('meta_keywords', $this->meta_keywords);
        MolajoController::getApplication()->setMetaData('meta_author', $this->meta_author);
        MolajoController::getApplication()->setMetaData('meta_content_rights', $this->meta_content_rights);
        MolajoController::getApplication()->setMetaData('meta_robots', $this->meta_robots);
    }

    /**
     * _route
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
    private function _route()
    {
        MolajoPluginHelper::importPlugin('system');
        MolajoController::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * Execute Extension
     *
     * @return  boolean
     *
     * @since   1.0
     */
    protected function _authorise()
    {
        if (in_array($this->view_group_id, MolajoController::getUser()->view_groups)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * _renderDocumentType
     *
     * Get Header information for Page
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function _renderDocumentType()
    {
        $documentTypeClass = 'Molajo' . ucfirst($this->format) . 'Format';
        $results = new $documentTypeClass ($this);
    }


    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    public function request()
    {
        /** from #__extension_options */
        $template = $this->template;
        $page = $this->page;
        $option = $this->option;
        $format = $this->format;
        $task = $this->task;
        $view = $this->view;
        $wrap = $this->wrap;
        $static = $this->static;

        $id = $this->id;
        $ids = $this->ids;
        $category = $this->primary_category_id;

        $component_path = '';
        $controller = '';
        $model = '';
        $plugin_type = '';
        $acl_implementation = '';
        $component_table = '';

        /** from template <include:type attr1=xyz attr2=xyz ... attrN=xyz /> */
        // $attributes = array();

        /** configuration model */
        $configModel = new MolajoModelConfiguration ($option);

        /** 1. Component Path */
        $component_path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $option;

        /** 2. Task */
        if ($task == '' || $task == null) {
            $task = 'display';
        }

        /** 3. Retrieve Controller while validating Task */
        $controller = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER, $task);
        if ($controller === false) {
            MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_INVALID_TASK_CONTROLLER') . ' ' . $task);
            return false;
        }

        /** 4. id, ids, category */
        if ($task == 'add') {

            if ((int)$id == 0 && count($ids) == 0) {
            } else {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_ADD_TASK_MUST_NOT_HAVE_ID'));
                return false;
            }

        } else if ($task == 'edit' || $task == 'restore') {

            if ($id > 0 && count($ids) == 0) {

            } else if ((int)$id == 0 && count($ids) == 1) {
                $id = $ids[0];
                $ids = array();

            } else if ((int)$id == 0 && count($ids) == 0) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_EDIT_TASK_MUST_HAVE_ID'));
                return false;

            } else if (count($ids) > 1) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_IDS'));
                return false;
            }
        }

        /** 5. model */
        if ($controller == 'display') {
            if ($static === true) {
                $model = 'dummy';
            } else {
                $model = 'display';
            }

        } else {
            $model = 'edit';
        }

        if ($controller == 'display') {

            /** 6. Format */
            if ($format == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS, $format);
            }
            /** get default format */
            if ($results === false) {
                $format = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT);
                if ($format === false) {
                    $format = MolajoController::getApplication()->get('default_format', 'html');
                }
            }

            /** 7. View **/
            if ($static === true) {
                $option = 3300;

            } else if ($id > 0) {
                if ($task == 'display') {
                    $option = 3110;
                    /** item */
                } else {
                    $option = 3310;
                    /** edit */
                }
            } else {
                $option = 3210;
                /** items */
            }

            if ($view == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $view);
            }

            $option = $option + 10;
            if ($results === false) {
                $view = $configModel->getOptionValue($option);
                if ($view === false) {
                    MolajoController::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                    return false;
                }
            }

            /** 8. Wrap **/
            $option = $option + 10;
            if ($wrap == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $wrap);
            }

            $option = $option + 10;
            if ($results === false) {
                $wrap = $configModel->getOptionValue($option);
                if ($wrap === false) {
                    $wrap = 'none';
                }
            }

            /** 9. Page **/
            $option = $option + 10;
            if ($page == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $page);
            }

            $option = $option + 10;
            if ($results === false) {
                $page = $configModel->getOptionValue($option);
                if ($page === false) {
                    $page = 'full';
                }
            }
        }
        /** todo: amy: come back and get redirect */

        /** 11. acl implementation */
        $acl_implementation = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION);
        if ($acl_implementation === false) {
            $acl_implementation = 'core';
        }

        /** 12. component table */
        $component_table = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE);
        if ($component_table === false) {
            $component_table = '__content';
        }

        /** 13. plugin helper */
        $plugin_type = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE);
        if ($plugin_type === false) {
            $plugin_type = 'content';
        }

        $this->setRequest();
    }


    /**
     * @param $option
     * @param $component_path
     * @param $model
     * @param $view
     * @param $controller
     * @param $task
     * @param $template
     * @param $page
     * @param $format
     * @param $attributes
     * @param $plugin_type
     * @param $id
     * @param $ids
     * @param $category_id
     * @param $acl_implementation
     * @param $component_table
     * @return array|string
     */
    public function setRequest($option, $component_path, $model, $view, $controller, $task,
                               $template, $page, $format, $attributes,
                               $plugin_type, $id, $ids, $category_id,
                               $acl_implementation, $component_table)
    {
        /** MVC Request Variables */
        $request = array();

        $request['current_url'] = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH;
        if (MOLAJO_PAGE_REQUEST == '') {
        } else {
            $request['current_url'] .= '/' . MOLAJO_PAGE_REQUEST;
        }
        $request['base_url'] = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH;
        $request['component_path'] = $component_path;

        $request['extension_type'] = $this->name;
        $request['option'] = $this->option;
        $request['extension'] = $this->option;

        $request['controller'] = $controller;
        $request['model'] = $model;
        $request['task'] = $this->task;

        $request['template'] = $this->template;
        $request['page'] = $this->page;
        $request['view'] = $this->view;
        $request['view_type'] = 'extensions';
        $request['format'] = $this->format;
        $request['wrap'] = $this->wrap;
        $request['wrap_id'] = '';
        $request['wrap_class'] = '';

        $request['plugin_type'] = $plugin_type;

        $request['id'] = (int)$this->id;
        $request['ids'] = (array)$this->ids;
        $request['category_id'] = (int)$this->primary_category_id;

        $request['parameters'] = $this->parameters;

        $request['acl_implementation'] = $acl_implementation;
        $request['component_table'] = $component_table;
        $request['filter_name'] = 'config_manager_list_filters';
        $request['select_name'] = 'config_manager_grid_column';

        return $request;
    }
}
