<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo View
 *
 * @package      Molajo
 * @subpackage   View
 * @since        1.0
 */
class MolajoView extends JObject
{
    /**
     * @var object $request
     * @since 1.0
     */
    public $request;

    /**
     * @var object $state
     * @since 1.0
     */
    public $state;

    /**
     * @var object $parameters
     * @since 1.0
     */
    public $parameters;

    /**
     * @var object $template
     * @since 1.0
     */
    public $template;

    /**
     * @var object $page
     * @since 1.0
     */
    public $page;

    /**
     * @var object $layout_type
     * @since 1.0
     */
    public $layout_type;

    /**
     * @var object $layout
     * @since 1.0
     */
    public $layout;

    /**
     * @var object $wrap
     * @since 1.0
     */
    public $wrap;

    /**
     * @var object $wrap_id
     * @since 1.0
     */
    public $wrap_id;

    /**
     * @var object $wrap_class
     * @since 1.0
     */
    public $wrap_class;

    /**
     * @var object $rowset
     * @since 1.0
     */
    public $rowset;

    /**
     * @var object $row
     * @since 1.0
     */
    public $row;

    /**
     * @var object $pagination
     * @since 1.0
     */
    public $pagination;

    /**
     * @var object $layout_path
     * @since 1.0
     */
    public $layout_path;

    /**
     * @var object $layout_path_url
     * @since 1.0
     */
    public $layout_path_url;

    /**
     * renderModulePosition
     *
     * usage in layout:
     *
     * $this->renderModulePosition ('position-name', array('wrap' => 'none');
     *
     * @param $position
     * @param array $options
     * @return void

    public function renderModulePosition($position, $options = array('wrap' => 'none'))
    {
    $renderer = $this->document->loadRenderer('modules');
    echo $renderer->render($position, $options, null);
    }
     */

    /**
     * display
     *
     * View for Display View that uses no forms
     *
     * @param null $tpl
     *
     * @return bool
     */
    public function display($tpl = null)
    {
        /** no results */
        if (count($this->parameters) > 0
            && $this->parameters->def('suppress_no_results', false) === true
            && count($this->rowset == 0)
        ) {
            return;
        }

        /** Render Layout */
        $this->findPath($this->layout, $this->layout_type);
        if ($this->layout_path === false) {
            // load an error layout
            return;
        }

        $renderedOutput = $this->renderLayout($this->layout, $this->layout_type);

        /** Wrap Rendered Output */
        if ($this->wrap == 'horz') {
            $this->wrap = 'horizontal';
        }
        if ($this->wrap == 'xhtml') {
            $this->wrap = 'div';
        }
        if ($this->wrap == 'rounded') {
            $this->wrap = 'div';
        }
        if ($this->wrap == 'raw') {
            $this->wrap = 'none';
        }
        if ($this->wrap == '') {
            $this->wrap = 'none';
        }
        if ($this->wrap == null) {
            $this->wrap = 'none';
        }

        $this->findPath($this->wrap, 'wraps');
        if ($this->layout_path === false) {
            echo $renderedOutput;
            return;
        }

        $this->rowset = array();

        $tmpobj = new JObject();
        $tmpobj->set('wrap_id', $this->request['wrap_id']);
        $tmpobj->set('wrap_class', $this->request['wrap_class']);
        $tmpobj->set('content', $renderedOutput);

        $this->rowset[] = $tmpobj;

        $wrappedOutput = $this->renderLayout($this->wrap, 'wraps');

        echo $wrappedOutput;

        return;
    }

    /**
     * findPath
     *
     * Looks for path of Request Layout as a layout folder, in this order:
     *
     *  1. [template]/layouts/[layout-type]/[layout-folder]
     *  2. [extension_type]/[extension-name]/layouts/[layout-type]/[layout-folder]
     *      => For plugins, add plugin subfolder following [extension_type]
     *      => For components, add "views" subfolder following [extension-name]
     *  3. layouts/[layout_type]/[layout-folder]
     *
     * @return bool|string
     */
    protected function findPath($layout, $layout_type)
    {
        /** initialise layout */
        $this->layout_path = false;
        $template = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->template;

        /** 1. @var $templateLayoutPath [template]/layouts/[layout-type]/[layout-folder] */
        $templateLayoutPath = $template . '/layouts/' . $layout_type . '/' . $layout;
        $templateLayoutPathURL = JURI::root() . 'extensions/layouts/templates/' . $this->template . '/layouts/' . $layout_type . '/' . $layout;

        /** 2. @var $extensionPath [extension_type]/[extension-name]/layouts/[layout-type]/[layout-folder] */
        $extensionPath = '';
        if ($this->request['extension_type'] == 'plugin') {
            $extensionPath = MOLAJO_EXTENSIONS_PLUGINS . '/' . $this->request['plugin_folder'] . '/' . $this->request['option'] . '/layouts/' . $layout_type . '/' . $layout;
            $extensionPathURL = JURI::root() . 'extensions/layouts/plugins/' . $this->request['plugin_folder'] . '/' . $this->request['option'] . '/layouts/' . $layout_type . '/' . $layout;

        } else if ($this->request['extension_type'] == 'component') {
            $extensionPath = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->request['option'] . '/views/' . $this->request['view'] . '/layouts/' . $layout_type . '/' . $layout;
            $extensionPathURL = JURI::root() . 'extensions/layouts/components/' . $this->request['option'] . '/views/' . $this->request['view'] . '/layouts/' . $layout_type . '/' . $layout;

        } else if ($this->request['extension_type'] == 'module') {
            $extensionPath = MOLAJO_EXTENSIONS_MODULES . '/' . $this->request['option'] . '/layouts/' . $layout_type . '/' . $layout;
            $extensionPathURL = JURI::root() . 'extensions/layouts/modules/' . $this->request['option'] . '/layouts/' . $layout_type . '/' . $layout;

        } else {
            $extensionPath = '';
            $extensionPathURL = '';
        }

        /** 3. $corePath layouts/[layout_type]/[layout-folder] */
        $corePath = MOLAJO_EXTENSIONS_LAYOUTS . '/' . $layout_type . '/' . $layout;
        $corePathURL = JURI::root() . 'extensions/layouts/' . $layout_type . '/' . $layout;

        /**
         * Determine path in order of priority
         */

        /* 1. Template */
        if (is_dir($templateLayoutPath)) {
            $this->layout_path = $templateLayoutPath;
            $this->layout_path_url = $templateLayoutPathURL;
            return;

        /** 2. Extension **/
        } else if (is_dir($extensionPath)) {
            $this->layout_path = $extensionPath;
            $this->layout_path_url = $extensionPathURL;
            return;

        /** 3. Core **/
        } else if (is_dir($corePath)) {
            $this->layout_path = $corePath;
            $this->layout_path_url = $corePathURL;
            return;
        }

        $this->layout_path = false;
        $this->layout_path_url = false;
    }

    /**
     * renderLayout
     *
     * Can do one of two things:
     *
     * 1. Provide the entire set of query results in the $this->rowset object for the layout to process
     *      How? Include a layout file named custom.php (and no layout file and body.php)
     *
     * 2. Loop thru the $this->rowset object processing each row, one at a time.
     *      How? Include top.php, header.php, body.php, footer.php, and/or bottom.php
     *
     * Loops through rowset, one row at a time, including top, header, body, footer, and bottom files
     *
     * @return string
     *
     */
    protected function renderLayout($layout, $layout_type)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia();

        /** Language */
        $this->loadLanguage($layout, $layout_type);

        /** start collecting output */
        ob_start();

        /**
         *  I. Rowset processed by Layout
         *
         *  If the custom.php file exists in layoutFolder, layout handles $this->rowset processing
         *
         */
        if (file_exists($this->layout_path . '/layouts/custom.php')) {
            include $this->layout_path . '/layouts/custom.php';

        } else {

            /**
             * II. Loop through each row, one at a time
             *
             * The following layoutFolder/layouts/ files are included, if existing
             *
             * 1. Before any rows and if there is a top.php file:
             *
             *       - beforeDisplayContent output is rendered;
             *
             *       - the top.php file is included.
             *
             * 2. For each row:
             *
             *      if there is a header.php file, it is included,
             *        and the event afterDisplayTitle output is rendered.
             *
             *      If there is a body.php file, it is included;
             *
             *      If there is a footer.php file, it is included;
             *
             * 3. After all rows and if there is a footer.php file:
             *      the footer.php file is included;
             *      afterDisplayContent output is rendered;
             *
             */
            foreach ($this->rowset as $this->row) {

                /** layout: top */
                if ($rowCount == 1) {

                    /** event: Before Content Display */
                    if (isset($this->row->event->beforeDisplayContent)) {
                        echo $this->row->event->beforeDisplayContent;
                    }

                    if (file_exists($this->layout_path . '/layouts/top.php')) {
                        include $this->layout_path . '/layouts/top.php';
                    }
                }

                if ($this->row == null) {
                } else {

                    /** item: header */
                    if (file_exists($this->layout_path . '/layouts/header.php')) {
                        include $this->layout_path . '/layouts/header.php';

                        /** event: After Display of Title */
                        if (isset($this->row->event->afterDisplayTitle)) {
                            echo $this->row->event->afterDisplayTitle;
                        }
                    }

                    /** item: body */
                    if (file_exists($this->layout_path . '/layouts/body.php')) {
                        include $this->layout_path . '/layouts/body.php';
                    }

                    /** item: footer */
                    if (file_exists($this->layout_path . '/layouts/footer.php')) {
                        include $this->layout_path . '/layouts/footer.php';
                    }

                    $rowCount++;
                }

                /** layout: bottom */
                if (file_exists($this->layout_path . '/layouts/bottom.php')) {
                    include $this->layout_path . '/layouts/bottom.php';

                    /** event: After Layout is finished */
                    if (isset($this->row->event->afterDisplayContent)) {
                        echo $this->row->event->afterDisplayContent;
                    }
                }
            }
        }

        /** collect output */
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * loadLanguage
     *
     * Language
     *
     * Automatically includes Language Files (if existing) for layouts
     *
     * @param $this->layout_path
     * @return void
     */
    protected function loadLanguage($layout, $layout_type)
    {
        MolajoFactory::getLanguage()->load($layout, $this->layout_path, MolajoFactory::getLanguage()->getDefault(), false, false);
    }

    /**
     * loadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js]
     * 2. Extension specific CSS and JS in => media/[extension]/css[js]/XYZ.css[js]
     * 3. Asset ID specific CSS and JS in => media/[asset_id]/css[js]/XYZ.css[js]
     *
     * 4. Layout Path determined earlier (Template, Extension, Core precedence)
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @return void
     */
    protected function loadMedia()
    {
        /** Extension specific CSS and JS in => media/[extension]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/system/' . $this->request['option'] . '/layouts';
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . $this->request['option'] . '/layouts';
        MolajoFactory::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoFactory::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Asset ID specific CSS and JS in => media/[application]/[asset_id]/css[js]/XYZ.css[js] */
        /** todo: amy deal with assets for all levels        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA.'/'.$this->request['asset_id'];
        $urlPath = JURI::root().'sites/'.MOLAJO_SITE.'/media/'.$this->request['asset_id'];
        MolajoFactory::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoFactory::getApplication()->loadMediaJS($filePath, $urlPath);
         */
        /** Layout specific CSS and JS in path identified in getPath */
        $filePath = $this->layout_path . '/layouts';
        $urlPath = $this->layout_path_url . '/layouts';
        MolajoFactory::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoFactory::getApplication()->loadMediaJS($filePath, $urlPath);
    }

    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is either htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param   mixed  $var  The output to escape.
     *
     * @return  mixed  The escaped value.
     *
     * @since   1.0
     */
    function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
        }

        return call_user_func($this->_escape, $var);
    }

    /**
     * Method to add a model to the view.  We support a multiple model single
     * view system by which models are referenced by classname.  A caveat to the
     * classname referencing is that any classname prepended by JModel will be
     * referenced by the name without JModel, eg. JModelCategory is just
     * Category.
     *
     * @param   object   $model   The model to add to the view.
     * @param   boolean  $default  Is this the default model?
     *
     * @return  object   The added model.
     *
     * @since   1.0

    public function DELETEsetModel($model, $default = false)
    {
        $name = strtolower($model->getName());
        $this->_models[$name] = &$model;

        if ($default) {
            $this->_defaultModel = $name;
        }
        return $model;
    }
     */
    /**
     * Sets the layout name to use
     *
     * @param   string  $layout  The layout name or a string in format <template>:<layout file>
     *
     * @return  string  Previous value.
     *
     * @since   1.0

    public function DELETEsetLayout($layout)
    {
        $previous = $this->layout;
        if (strpos($layout, ':') === false) {
            $this->layout = $layout;
        }
        else
        {
            // Convert parameter to array based on :
            $temp = explode(':', $layout);
            $this->layout = $temp[1];
            // Set layout template
            //            $this->layoutTemplate = $temp[0];
        }

        return $previous;
    }
     */
}

/** 7. Optional data (put this into a model parent?) */
//		$this->category	            = $this->get('Category');
//		$this->categoryAncestors    = $this->get('Ancestors');
//		$this->categoryParent       = $this->get('Parent');
//		$this->categoryPeers	    = $this->get('Peers');
//		$this->categoryChildren	    = $this->get('Children');

/** used in manager */

/**
 * @var $render object
 */
//protected $render;

/**
 * @var $saveOrder string
 */
// protected $saveOrder;
//      $this->authorProfile        = $this->get('Author');

//      $this->tags (tag cloud)
//      $this->tagCategories (menu)
//      $this->calendar

/** blog variables
move variables into $options
retrieve variables here in view - and split int rowset if needed

protected $category;
protected $children;
protected $lead_items = array();
protected $intro_items = array();
protected $link_items = array();
protected $columns = 1;
 */
//Navigation
//$this->navigation->get('form_return_to_link')
//$this->navigation->get('previous')
//$this->navigation->get('next')
//
// Pagination
//$this->navigation->get('pagination_start')
//$this->navigation->get('pagination_limit')
//$this->navigation->get('pagination_links')
//$this->navigation->get('pagination_ordering')
//$this->navigation->get('pagination_direction')
//$this->breadcrumbs
//$total = $this->getTotal();

//$this->configuration
//Parameters (Includes Global Options, Menu Item, Item)
//$this->parameters->get('layout_show_page_heading', 1)
//$this->parameters->get('layout_page_class_suffix', '')