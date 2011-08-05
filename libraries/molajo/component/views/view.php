<?php
/**
 * @version     $id: view.html.php
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo View 
 *
 * @package	    Molajo
 * @subpackage	View
 * @since	    1.0
 */
class MolajoView extends JView
{
    /**
     * @var $app object
     */
        protected $app;
    
    /**
     * @var $system object
     */
        protected $system;

    /**
     * @var $document object
     */
        protected $document;

    /**
     * @var $user object
     */
        protected $user;

    /**
     * @var $request object
     */
        protected $request;

    /**
     * @var $state object
     */
        protected $state;

    /**
     * @var $params object
     */
        protected $params;

    /**
     * @var $rowset object
     */
        protected $rowset;

    /**
     * @var $row array
     */
        protected $row;

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
        /** @var $this->app */
        $this->app = MolajoFactory::getApplication();
        
        /** @var $this->system */
        $this->system = MolajoFactory::getConfig();

        /** @var $this->document */
        $this->document = MolajoFactory::getDocument();

        /** @var $this->user */
        $this->user = MolajoFactory::getUser();

        /** Set Page Meta */
//		$pageModel = JModel::getInstance('Page', 'MolajoModel', array('ignore_request' => true));
//		$pageModel->setState('params', $this->app->getParams());
    }

    /**
     * findPath
     * 
     * Looks for path of Request Layout as a layout folder, in this order:
     *
     *  1. CurrentTemplate/html/$layout-folder/
     *  2. components/com_component/views/$view/tmpl/$layout-folder/
     *  3. MOLAJO_LAYOUTS_EXTENSIONS/$layout-folder/
     *
     * @param  $tpl
     * @return bool|string
     */
    protected function findPath ($layout)
    {
        /** path: template **/
        $template = MolajoFactory::getApplication()->getTemplate();
        $templatePath = MOLAJO_PATH_THEMES.'/'.$template.'/html/';

        /** path: component **/
        if (MOLAJO_APPLICATION == 'site') {
            $componentPath = MOLAJO_PATH_ROOT.'/components/'.$this->request['option'].'/views/'.$this->request['view'].'/tmpl/';
        } else {
            $componentPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION.'/components/'.$this->request['option'].'/views/'.$this->request['view'].'/tmpl/';
        }

        /** path: core **/
        $corePath = MOLAJO_LAYOUTS_EXTENSIONS.'/';

        /** template **/
        if (is_dir($templatePath.$layout)) {
            return $templatePath.$layout;

        /** component **/
        } else if (is_dir($componentPath.$layout)) {
            return $componentPath.$layout;

        /** molajao library **/
        } else if (is_dir($corePath.$layout)) {
            return $corePath.$layout;
        }

        return false;
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
    * @param $layout
    * @param $layoutFolder
    * @return string
    *
    */
    protected function renderLayout ($layoutFolder, $layout)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia ($layoutFolder);

        /** Language */
        $this->loadLanguage ($layoutFolder);

        /** start collecting output */
        ob_start();

        /**
        *  I. Rowset processed by Layout
        *
        *  If the custom.php file exists in layoutFolder, layout handles $this->rowset processing
        *
        */
        if (file_exists($layoutFolder.'/layouts/custom.php')) {
            include $layoutFolder.'/layouts/custom.php';

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
                if ($rowCount == 1 && (!$layout == 'system')) {

                    /** event: Before Content Display */
                    if (isset($this->row->event->beforeDisplayContent)) {
                        echo $this->row->event->beforeDisplayContent;
                    }

                    if (file_exists($layoutFolder.'/layouts/top.php')) {
                        include $layoutFolder.'/layouts/top.php';
                    }
                }

                /** item: header */
                if (file_exists($layoutFolder.'/layouts/header.php')) {
                    include $layoutFolder.'/layouts/header.php';

                    /** event: After Display of Title */
                    if (isset($this->row->event->afterDisplayTitle)) {
                        echo $this->row->event->afterDisplayTitle;
                    }
                }

                /** item: body */
                if (file_exists($layoutFolder.'/layouts/body.php')) {
                    include $layoutFolder.'/layouts/body.php';
                }

                /** item: footer */
                if (file_exists($layoutFolder.'/layouts/footer.php')) {
                    include $layoutFolder.'/layouts/footer.php';
                }

                $rowCount++;
            }

            /** layout: bottom */
            if (file_exists($layoutFolder.'/layouts/bottom.php')) {
                include $layoutFolder.'/layouts/bottom.php';

                /** event: After Layout is finished */
                if (isset($this->row->event->afterDisplayContent)) {
                    echo $this->row->event->afterDisplayContent;
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
     * Automatically includes the following files (if existing)
     *
     * 1. Master Layout folder Language Files found in => layout/[current-language]/
     * 2. Current Layout folder Language Files found in => layout/current-layout/[current-language]/
     *
     * @param $layoutFolder
     * @return void
     */
    protected function loadLanguage ($layoutFolder)
    {
        $language = MolajoFactory::getLanguage();
        
        $language->load('layouts', MOLAJO_LAYOUTS_EXTENSIONS, $language->getDefault(), true, true);
        $language->load('layouts_'.$this->request['layout'], $layoutFolder, $language->getDefault(), true, true);
    }

    /**
     * loadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Application-specific CSS and JS in => media/site/[application]/css[js]/XYZ.css[js]
     * 2. Component specific CSS and JS in => media/site/[application]/[com_component]/css[js]/XYZ.css[js]
     * 3. Asset ID specific CSS and JS in => media/site/[application]/[asset_id]/css[js]/XYZ.css[js]
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @param $layoutFolder
     *
     * @return void
     */
    protected function loadMedia ($layoutFolder)
    {
        if (MOLAJO_APPLICATION_PATH == '') {
            $applicationName = 'frontend';
        } else {
            $applicationName = MOLAJO_APPLICATION_PATH;
        }

        /** Application-specific CSS and JS in => media/site/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_PATH_ROOT.'/media/site/'.$applicationName;
        $urlPath = JURI::root().'media/site/'.$applicationName;

        if ($this->params->get('load_application_css', true) === true) {
            $this->loadMediaCSS ($filePath, $urlPath);
        }
        if ($this->params->get('load_application_js', true) === true) {
            $this->loadMediaJS ($filePath, $urlPath);
        }

        /** Component specific CSS and JS in => media/site/[application]/[com_component]/css[js]/XYZ.css[js] */
        if ($this->params->get('load_component_css', true) === true) {
            $this->loadMediaCSS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }
        if ($this->params->get('load_component_js', true) === true) {
            $this->loadMediaJS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }

        /** Asset ID specific CSS and JS in => media/site/[application]/[asset_id]/css[js]/XYZ.css[js] */
        if ($this->params->get('load_asset_id_css', true) === true) {
//            $this->loadMediaCSS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }
        if ($this->params->get('load_asset_id_js', true) === true) {
//            $this->loadMediaJS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }

    }

    /**
     * loadMediaCSS
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function loadMediaCSS ($filePath, $urlPath)
    {
        if (JFolder::exists($filePath)) {
        } else {
            return;
        }
        
        $files = JFolder::files($filePath.'/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if ($this->document->direction == 'rtl') {
                         $this->document->addStyleSheet($urlPath.'/css/'.$file);
                    }
                } else {
                    $this->document->addStyleSheet($urlPath.'/css/'.$file);
                }
            }
        }
    }

    /**
     * loadMediaJS
     *
     * Loads the JS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function loadMediaJS ($filePath, $urlPath)
    {
        if (JFolder::exists($filePath)) {
        } else {
            return;
        }
        
        $files = JFolder::files($filePath.'/js', '\.js$', false, false);
        
        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->document->addScript($urlPath.'/js/'.$file);
            }
        }
    }

    /**
     * getColumns
     *
     * Displays system variable names and values
     *
     * $this->params
     *
     * $this->getColumns ('system');
     *
     * @param  $type
     * @return void
     */
    protected function getColumns ($type, $layout='system')
    {
        /** @var $this->rowset */
        $this->rowset = array();

        /** @var $registry */
        $registry = new JRegistry();

        /** @var $tempIndex */
        $columnIndex = 0;

        if ($type == 'user') {
            foreach ($this->$type as $column=>$value) {
                if ($column == 'params') {
                    $registry->loadJSON($value);
                    $options = $registry->toArray();
                    $this->getColumnsJSONArray ($type, $options);
                } else {
                    $this->getColumnsFormatting ($type, $column, $value);
                }
            }

        } else if ($type == 'system') {
                $registry->loadJSON($this->$type);
                $options = $registry->toArray();
                $this->getColumnsJSONArray ($type, $options);

        } else {
            return false;
        }

        /**
         *  Display Results
         */
        $layoutFolder = $this->findPath($layout);
        echo $this->renderLayout ($layoutFolder, 'system');

        return;
    }

    /**
     * getColumnsJSONArray
     *
     * Process Array from converted JSON Object
     *
     * @param  $type
     * @param  $options
     * @return void
     */
    private function getColumnsJSONArray ($type, $options)
    {
        foreach ($options as $column=>$value) {
            $this->getColumnsFormatting ($type, $column, $value);
        }
    }

    /**
     * getColumnsFormatting
     *
     * Process Columns from Object
     *
     * @param  $type
     * @param  $column
     * @param  $value
     * @return void
     */
    private function getColumnsFormatting ($type, $column, $value, $columnIndex)
    {
        $this->rowset[$columnIndex]['column'] = $column;

        if (is_array($value)) {
            $this->rowset[$columnIndex]['syntax'] = '$list = $this->'.$type."->get('".$column."');<br />";
            $this->rowset[$columnIndex]['syntax'] .= 'foreach ($list as $item=>$itemValue) { <br />';
            $this->rowset[$columnIndex]['syntax'] .= '&nbsp;&nbsp;&nbsp;&nbsp;echo $item.'."': '".'.$itemValue;';
            $this->rowset[$columnIndex]['syntax'] .= '<br />'.'}';
            $temp = '';
            $list = $this->$type->get($column);
            foreach ($list as $item=>$itemValue) {
                $temp .= $item.': '.$itemValue.'<br />';
            }
            $this->rowset[$columnIndex]['value'] = $temp;
        } else {
            $this->rowset[$columnIndex]['syntax'] = 'echo $this->'.$type."->get('".$column."');  ";
            $this->rowset[$columnIndex]['value'] = $value;
        }

        $columnIndex++;
    }
}