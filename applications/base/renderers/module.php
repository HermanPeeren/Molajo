<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoModuleRenderer extends MolajoRenderer
{
    /**
     * _getExtension
     *
     * Retrieve extension information using either the ID or the name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        $this->task->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_MODULE);

        $results = parent::_getExtension();

        if ($results === false) {
            return false;
        }

        $this->task->set('extension_path',
            ModuleHelper::getPath(
                strtolower($this->task->get('extension_instance_name'))));

        $this->task->set('extension_type', 'module');

        return true;
    }

    /**
     * import
     *
     * imports component folders and files
     * @since 1.0
     */
    protected function _importClasses()
    {
        $fileHelper = new FileServices();
        $name = ucfirst($this->task->get('extension_instance_name'));
        $name = str_replace (array('-', '_'), '', $name);

        /** Controller */
        if (file_exists($this->task->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->task->get('extension_path') . '/controller.php',
                $name . 'ModuleControllerDisplay');
        }

        /** Model */
        if (file_exists($this->task->get('extension_path') . '/model.php')) {
            $fileHelper->requireClassFile($this->task->get('extension_path') . '/model.php',
                $name . 'ModuleModelDisplay');
        }
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        parent::_loadMedia(MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->task->get('extension_instance_name'),
            MOLAJO_SITE_MEDIA_URL . '/' . $this->task->get('extension_instance_name'),
            Molajo::Application()->get('media_priority_module', 400));
    }
}
