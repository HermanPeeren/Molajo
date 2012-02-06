<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoMessageRenderer extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  string $name
     * @param  string $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->_extension_required = false;
        parent::__construct($name, $type);
    }

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
        $this->task_request->set('mvc_model', 'MolajoMessagesModel');
        $this->task_request->set('mvc_task', 'display');
        $this->parameters->set('extension_suppress_no_results', 1);

        return true;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getApplicationDefaults()
    {
        if ((int)$this->task_request->get('template_view_id', 0) == 0) {
            $this->task_request->set('template_view_id', Molajo::Application()->get('message_template_view_id'));
        }
        if ((int)$this->task_request->get('wrap_view_id', 0) == 0) {
            $this->task_request->set('wrap_view_id', Molajo::Application()->get('message_wrap_view_id'));
        }

        return true;
    }
}

