<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Plugin Class
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       11.1
 */
abstract class MolajoPlugin extends JEvent
{
    /**
     * Plugin Parameters
     *
     * @var    string
     */
    public $params = null;

    /**
     * The name of the plugin
     *
     * @var    sring
     */
    protected $_name = null;

    /**
     * The plugin type
     *
     * @var    string
     */
    protected $_type = null;

    /**
     * Constructor
     *
     * @param   object  $subject  The object to observe
     * @param   array  $config  An optional associative array of configuration settings.
     * Recognized key values include 'name', 'group', 'params', 'language'
     * (this list is not meant to be comprehensive).
     *
     * @since   11.1
     */
    public function __construct(&$subject, $config = array())
    {
        if (isset($config['params'])) {
            if ($config['params'] instanceof JRegistry) {
                $this->params = $config['params'];
            } else {
                $this->params = new JRegistry;
                $this->params->loadString($config['params']);
            }
        }

        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }

        if (isset($config['type'])) {
            $this->_type = $config['type'];
        }

        parent::__construct($subject);
    }

    /**
     * Loads the plugin language file
     *
     * @param   string   $extension    The extension for which a language file should be loaded
     * @param   string   $basePath    The basepath to use
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   11.1
     */
    public function loadLanguage($extension = '', $basePath = MOLAJO_BASE_FOLDER)
    {
        if (empty($extension)) {
            $extension = 'plg' . ucfirst($this->_type) . ucfirst($this->_name);
        }

        $lang = MolajoFactory::getLanguage();

        $lang->load(strtolower($extension), $basePath, null, false, false)
        || $lang->load(strtolower($extension), MOLAJO_EXTENSION_PLUGINS.'/'.$this->_type.'/'.$this->_name, null, false, false)
        || $lang->load(strtolower($extension), MOLAJO_APPLICATION_PATH, null, false, false);
    }
}
