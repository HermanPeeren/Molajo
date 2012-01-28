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
class MolajoRendererModule extends MolajoRenderer
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
        $this->mvc->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_MODULE);
        parent::_getExtension();
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
        $fileHelper = new MolajoFileHelper();

        /** Controller */
        if (file_exists(MOLAJO_EXTENSIONS_MODULES . '/controller.php')) {
          $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_MODULES . '/controller.php',
              ucfirst($this->mvc->get('extension_instance_name')) . 'ModuleControllerDisplay');
        }

        /** Model */
        if (file_exists($this->mvc->get('extension_path') . '/model.php')) {
          $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_MODULES . '/model.php',
              ucfirst($this->mvc->get('extension_instance_name')) . 'ModuleModelDisplay');
        }
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
        parent::_loadMedia(MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->mvc->get('extension_instance_name'),
            MOLAJO_SITE_MEDIA_URL . '/' . $this->mvc->get('extension_instance_name'),
            MolajoController::getApplication()->get('media_priority_module', 400));
    }
}
