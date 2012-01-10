<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoControllerExtension
{
    /**
     * @var object $request
     *
     * @since 1.0
     */
    public $request;

    /**
     * @var object $parameters
     *
     * @since 1.0
     */
    public $parameters = array();

    /**
     * @var object $table
     *
     * @since 1.0
     */
    public $table = null;

    /**
     * @var object $model
     *
     * @since 1.0
     */
    public $model = null;

    /**
     * @var object $isNew
     *
     * @since 1.0
     */
    public $isNew = null;

    /**
     * @var object $existing_status
     *
     * @since 1.0
     */
    public $existing_status = null;

    /**
     * $redirectClass
     *
     * @var string
     */
    public $redirectClass = null;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param    array   $request
     *
     * @since    1.0
     */
    public function __construct(JObject $request, $parameters = array())
    {
        $this->request = $request;
        //$temp = (array) $this->request;
        //echo '<pre>';var_dump($temp);'</pre>';

        // Get parameters
        $this->parameters = new JRegistry;
        $this->parameters->loadArray($parameters);

        /**
        if (isset($attribs['params'))) {
        $template_params = new JRegistry;
        $template_params->loadString(html_entity_decode($attribs['params'), ENT_COMPAT, 'UTF-8'));
        $params->merge($template_params);
        $module = clone $module;
        $module->params = (string)$params;
        }

         */
        // todo: amy look at redirect
        $this->redirectClass = new MolajoControllerRedirect($this->request);

        /** load table */
        if ($this->request->get('task') == 'display'
            || $this->request->get('task') == 'add'
            || $this->request->get('task') == 'login'
            || $this->request->get('component_table') == '__dummy'
        ) {
            $this->isNew = false;

        } else {
            $this->table = $this->model->getTable();
            $this->table->reset();
            $this->table->load((int)$this->request->get('id'));

            if ($this->request->get('id') == 0) {
                $this->isNew = true;
                $this->existing_status = 0;
            } else {
                $this->isNew = false;
                $this->existing_status = $this->table->state;
            }
        }

        /** dispatch events
        if ($this->dispatcher
        || $this->request->get('plugin_type') == ''
        ) {
        } else {
        $this->dispatcher = JDispatcher::getInstance();
        MolajoPluginHelper::importPlugin($this->request->get('plugin_type'));
        }
         */
        /** check authorisation **/
        if (MOLAJO_APPLICATION == 'installation') {
        } else {
            $results = $this->checkTaskAuthorisation();
            if ($results === false) {
                return false;
            }
        }

        /** set redirects **/
        $this->redirectClass->initialise();

        /** success **/
        return true;
    }

    /**
     * checkTaskAuthorisation
     *
     * Method to verify the user's authorisation to perform a specific task
     *
     * @return bool
     */
    public function checkTaskAuthorisation()
    {
        $acl = new MolajoACL ();
        $results = $acl->authoriseTask(
            $this->request->get('option'),
            $this->request->get('controller'),
            $this->request->get('task'),
            $this->request->get('id'),
            $this->request->get('ids'),
            $this->request->get('category'),
            $this->table);

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED') . ' ' . $this->request->get('task'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * checkinItem
     *
     * Used to check in item if it is already checked out
     *
     * @return bool
     */
    public function checkinItem()
    {
        if ($this->request->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->request->get('id'));

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_CHECK_IN_FAILED'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * verifyCheckout
     *
     * method to verify that the current user is recorded in the checked_out column of the item
     *
     * @return    boolean
     */
    public function verifyCheckout()
    {
        if ($this->request->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        if ($this->table->checked_out == MolajoController::getUser()->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER') . ' ' . $this->getTask());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * checkoutItem
     *
     * method to set the checkout_time and checked_out values of the item
     *
     * @return    boolean
     * @since    1.0
     */
    public function checkoutItem()
    {
        if ($this->request->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkout($this->request->get('id'));
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_CHECKOUT_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }
        return true;
    }

    /**
     * createVersion
     *
     * Components have version management save and restore processes as an
     *  automatic option
     *
     * @return    boolean
     * @since    1.0
     */
    public function createVersion($context)
    {
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int)$this->request->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->request->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->request->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->request->get('id'), $versionKey));
        if (count($results) && in_array(false, $results, true)) {
        $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
        $this->redirectClass->setRedirectMessageType('error');
        return false;
        }
         **/
        return true;
    }

    /**
     * maintainVersionCount
     *
     * Molajo_Note: All Components have version management save and restore processes as
     * an automatic option
     *
     * @param  $context
     * @return boolean
     */
    public function maintainVersionCount($context)
    {
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no versions to delete for create **/
        if ((int)$this->request->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->request->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->request->get('id'), $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' . $this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->request->get('id'), $maintainVersions));
         **/
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
        $cache = MolajoController::getCache($this->request->get('option'));
        $cache->clean();
    }
}
