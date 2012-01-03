<?php
/**
 * @package     Molajo
 * @subpackage  HTML
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Render HTML Format
 *
 * @package     Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoHtmlFormat
{
    /**
     *  Sequence in which renderers are to be processed
     *
     * @var array
     * @since 1.0
     */
    protected $rendererProcessingSequence = array();

    /**
     *  Request Array
     *
     * @var array
     * @since 1.0
     */
    protected $requestArray = null;

    /**
     *  Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Template
     *
     * @var string
     * @since 1.0
     */
    protected $_template = array();

    /**
     *  Holds set of renderers defined within the template and associated attributes
     *
     * @var string
     * @since 1.0
     */
    protected $_renderers = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $requestArray from MolajoExtensions
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($requestArray = array())
    {
/*
                echo '<pre>';
                var_dump($requestArray);
                '</pre>';
*/
        $sequence = simplexml_load_file(MOLAJO_EXTENSIONS_CORE . '/core/formats/sequence.xml', 'SimpleXMLElement');
        foreach ($sequence->format as $format) {
            if ($format->name == 'html') {
                foreach ($format->renderer as $renderer) {
                    $this->rendererProcessingSequence[] = (string)$renderer[0];
                }
                break;
            }
        }

        /** set class properties */
        $this->requestArray = $requestArray;


        /** Request */
        $this->_render();
    }

    /**
     * Render the Template
     *
     * @return  object
     * @since  1.0
     */
    protected function _render()
    {
        /** Initialize */
        $template_include = '';

        if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/' . 'index.php')) {
            $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/' . 'index.php';
        } else {
            $this->requestArray['template_name'] = 'system';
            $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/system/index.php';
        }

        $template_path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'];
/** todo: amy look for path for page */
        $template_page_include = $template_path . '/pages/'.$this->requestArray['page'].'/index.php';

        $this->parameters = array(
            'template' => $this->requestArray['template_name'],
            'template_path' => $template_path,
            'page' => $template_page_include,
            'parameters' => $this->requestArray['template_parameters']

        );

        /** Before Event */
        MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH  . '/sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'];
        $urlPath = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH  . '/extensions/templates/' . $this->requestArray['template_name'];
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoController::getLanguage();
        $lang->load($this->requestArray['template_name'], MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'], $lang->getDefault(), false, false);

        ob_start();
        require $template_include;
        $this->_template = ob_get_contents();
        ob_end_clean();

        $this->_parseTemplate();

        $body = $this->_renderTemplate();

        MolajoController::getApplication()->setBody($body);

        /** After Event */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * _parseTemplate
     *
     * Parse the template and extract renderers and associated attributes
     *
     * @return  The parsed contents of the template
     */
    protected function _parseTemplate()
    {
        /** initialise */
        $matches = array();
        $this->_renderers = array();
        $i = 0;

        /** parse template for renderers */
        preg_match_all('#<include:(.*)\/>#iU', $this->_template, $matches);

        if (count($matches) == 0) {
            return;
        }

        /** store renderers in array */
        foreach ($matches[1] as $includeString) {

            /** initialise for each renderer */
            $includeArray = array();
            $includeArray = explode(' ', $includeString);
            $rendererType = '';

            foreach ($includeArray as $rendererCommand) {

                /** Type of Renderer */
                if ($rendererType == '') {
                    $rendererType = $rendererCommand;
                    $this->_renderers[$i]['name'] = $rendererType;
                    $this->_renderers[$i]['replace'] = $includeString;

                    /** Renderer Attributes */
                } else {
                    $rendererAttributes = str_replace('"', '', $rendererCommand);

                    if (trim($rendererAttributes) == '') {
                    } else {

                        /** Associative array of named pairs */
                        $splitAttribute = array();
                        $splitAttribute = explode('=', $rendererAttributes);
                        $this->_renderers[$i]['attributes'][$splitAttribute[0]] = $splitAttribute[1];
                    }
                }
            }
            $i++;
        }

        //        echo '<pre>';var_dump($this->_renderers);echo '</pre>';
    }

    /**
     * _renderTemplate
     *
     * Render pre-parsed template
     *
     * @return string rendered template
     */
    protected function _renderTemplate()
    {
        $replace = array();
        $with = array();

        foreach ($this->rendererProcessingSequence as $nextRenderer) {

            /** load renderer class */
            $class = 'Molajo' . ucfirst($nextRenderer) . 'Renderer';
            if ($class == 'MolajoHeadRenderer') {
            } elseif (class_exists($class)) {
                $rendererClass = new $class ($nextRenderer, $this->requestArray);
                echo $class . '<br />';
            } else {
                echo 'failed renderer = ' . $class . '<br />';
                // ERROR
            }

            if ($class == 'MolajoHeadRenderer') {
            } else {
                foreach ($this->_renderers as $index => $rendererArray) {

                    if ($nextRenderer == $rendererArray['name']) {

                        $renderer = $rendererArray['name'];

                        if (isset($rendererArray['attributes'])) {
                            $attributes = $rendererArray['attributes'];
                        } else {
                            $attributes = array();
                        }

                        $replace[] = "<include:" . $rendererArray['replace'] . "/>";
                        $with[] = $rendererClass->render($attributes);
                    }
                }
            }
        }

        return str_replace($replace, $with, $this->_template);
    }

    /**
     * Load a Favicon
     *
     * @return bool
     */
    protected function _loadFavicon()
    {
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/images/';

        if (file_exists($path . 'favicon.ico')) {
            $urlPath = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . '/extensions/templates/' . $this->requestArray['template_name'] . '/images/favicon.ico';
            MolajoController::getApplication()->addFavicon($urlPath);
            return true;
        }

        return false;
    }
}