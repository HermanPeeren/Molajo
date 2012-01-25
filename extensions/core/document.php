<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Document
 *
 * @package     Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocument
{
    /**
     * Rendering Sequence
     *
     * @var array
     * @since 1.0
     */
    protected $sequence = array();

    /**
     * Request
     *
     * @var object
     * @since 1.0
     */
    protected $request = null;

    /**
     * Template Parameters
     *
     * @var string
     * @since 1.0
     */
    public $parameters = null;

    /**
     * Template
     *
     * @var string
     * @since 1.0
     */
    protected $_template = array();

    /**
     * Holds renderer set defined within the template and associated attributes
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
     * @param   null    $requestArray from MolajoRequests
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct($request = array())
    {
        $this->request = $request;

        $formatXML = MOLAJO_EXTENSIONS_CORE . '/core/renderers/sequence.xml';
        if (JFile::exists($formatXML)) {
        } else {
            //error
            return false;
        }
        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->sequence[] = (string)$next;
        }
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
        $parameters = array(
            'template' => $this->request->get('template_name'),
            'template_path' => $this->request->get('template_path'),
            'page' => $this->request->get('page_include'),
            'parameters' => $this->request->get('template_parameters')
        );

        /** Template Parameters */
        $this->parameters = new JRegistry;
        $this->parameters->loadArray($parameters);

        /** Before Event */
        //        MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** Load Media */
//        $this->_loadMedia();

        /** process template include, and then all rendered output, for <include statements */
        $body = $this->_renderLoop();

        /** set response body */
        MolajoController::getApplication()->setBody($body);

        /** after rendering */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * Do it like _renderLoop, Andrew Eddie:
     * https://groups.google.com/d/msg/joomla-dev-general/FeveA7hYyeg/WMxSky8YAccJ
     * Molajo supports <input:xyz statements in templates and extension layouts. =)
     *
     *  _renderLoop
     *
     * Extension Views can contain <include:xyz statements in the same manner that the
     *  template include files use these statements. For that reason, this method parses
     *  thru the initial template include, renders the output for the <include:xyz statements
     *  found, and then parses that output, over and over, until no more <include:xyz statements
     *  are located.
     *
     * @return string  Rendered output for the Response Head and Body
     * @since  1.0
     */
    protected function _renderLoop()
    {
        /** include the template and page */
        ob_start();
        require $this->request->get('template_path');
        $this->_template = ob_get_contents();
        ob_end_clean();

        /** process all buffered input for include: statements  */
        $complete = false;
        $loop = 0;
        while ($complete === false) {

            /** count looping */
            $loop++;

            /** parse $this->template for include statements */
            $this->_parseTemplate();

            /** if no more include statements found, processing is complete */
            if (count($this->_renderers) == 0) {
                break;
            } else {
                /** invoke renderers for new include statements */
                $this->_template = $this->_renderTemplate();
            }
            if ($loop > MOLAJO_STOP_LOOP) {
                break;
            }
            /** look for new include statements in just rendered output */
            continue;
        }

        return $this->_template;
    }

    /**
     * _parseTemplate
     *
     * Parse the template and extract renderers and associated attributes
     *
     * @return  The parsed contents of the template
     * @since   1.0
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
    }

    /**
     * _renderTemplate
     *
     * Render pre-parsed template
     *
     * @return  string rendered template
     * @since   1.0
     */
    protected function _renderTemplate()
    {
        $replace = array();
        $with = array();

        /** 1. process every renderer in the format file in defined order */
        foreach ($this->sequence as $nextSequence) {

            /** 2. if necessary, split renderer name from include name (ex. request:component) */
            if (stripos($nextSequence, ':')) {
                $includeName = substr($nextSequence, 0, strpos($nextSequence, ':'));
                $rendererName = substr($nextSequence, strpos($nextSequence, ':') + 1, 999);
            } else {
                $includeName = $nextSequence;
                $rendererName = $nextSequence;
            }

            /** 4. loop thru all extracted include values to find match */
            for ($i = 0; $i < count($this->_renderers); $i++) {

                $rendererArray = $this->_renderers[$i];

                if ($includeName == $rendererArray['name']) {

                    /** 5. place attribute pairs into variable */
                    if (isset($rendererArray['attributes'])) {
                        $attributes = $rendererArray['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** 6. store the "replace this" value */
                    $replace[] = "<include:" . $rendererArray['replace'] . "/>";

                    /** 7. load the renderer class and send in requestArray */
                    $class = 'Molajo' . 'Renderer'.ucfirst($rendererName);
                    if (class_exists($class)) {
                        $rendererClass = new $class ($rendererName, $this->request);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        // ERROR
                    }
                    /** 8. render output and store results as "replace with" */
                    $with[] = $rendererClass->render($attributes);
                }
            }
        }
        /** 9. replace it */
        $this->_template = str_replace($replace, $with, $this->_template);

        /** 10. make certain all <include:xxx /> literals are removed */
        $replace = array();
        $with = array();
        for ($i = 0; $i < count($this->_renderers); $i++) {
            $replace[] = "<include:" . $this->_renderers[$i]['replace'] . "/>";
            $with[] = '';
        }

        return str_replace($replace, $with, $this->_template);
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load(
            MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name'),
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /**  Site */
        $this->_loadMediaPlus('',
            MolajoController::getApplication()->get('media_priority_site', 100));

        /** Application */
        $this->_loadMediaPlus('/application' . MOLAJO_APPLICATION,
            MolajoController::getApplication()->get('media_priority_application', 200));

        /** User */
        $this->_loadMediaPlus('/user' . MolajoController::getUser()->get('id'),
            MolajoController::getApplication()->get('media_priority_user', 300));

        /** Template */
        $priority = MolajoController::getApplication()->get('media_priority_template', 600);
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name');
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->request->get('template_name');
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {

        /** Site Specific: Application */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . '/' . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . '/' . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITE_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SITES_MEDIA_FOLDER . $plus;
        $urlPath = MOLAJO_SITES_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return false;
    }
}
