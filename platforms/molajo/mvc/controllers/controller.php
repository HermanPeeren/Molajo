<?php
/**
 * @package     Molajo
 * @subpackage  Primary Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Primary Controller
 *
 * @package        Molajo
 * @subpackage    Controller
 * @since        1.0
 */
class MolajoController extends JObject
{
    /**
     * @var object $config
     *
     * @since 1.0
     */
    public $config = array();

    /**
     * @var object $request
     *
     * @since 1.0
     */
    public $request = array();

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
    public $view = null;

    /**
     * @var object $model
     *
     * @since 1.0
     */
    public $model = null;

    /**
     * @var object $category_id
     *
     * @since 1.0
     */
    public $category_id = null;

    /**
     * @var object $id
     *
     * @since 1.0
     */
    public $id = null;

    /**
     * @var object $isNew
     *
     * @since 1.0
     */
    public $isNew = null;

    /**
     * @var object $existingState
     *
     * @since 1.0
     */
    public $existingState = null;

    /**
     * @var object $dispatcher
     *
     * @since 1.0
     */
    public $dispatcher = null;

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
     * @param    array   $config    An optional associative array of configuration settings.
     *
     * @since    1.0
     */
    public function __construct($config = array())
    {
        $this->config = $config;
    }

    /**
     * display
     *
     * Method to handle display, edit, and add tasks
     *
     * @param    boolean        $cachable    If true, the view output will be cached
     * @param    array        $urlparameters    An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return    JController    This object to support chaining.
     *
     * @since    1.0
     */
    public function display($cachable = false, $urlparameters = false)
    {
        if ($this->request['task'] == 'edit') {
            $results = $this->checkOutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** initialize view */

        /** 1. Request */
        $this->view->request = $this->request;

        /** 2. State */
        $this->view->state = $this->request['state'];

        /** 3. Parameters */
        $this->view->parameters = $this->parameters;

        /** 4. Template */
        $this->view->template = $this->request['template_id'];

        /** 5. Page */
        $this->view->page = $this->request['template_page'];

        /** 6. Layout Type */
        $this->view->layout_type = $this->request['layout_type'];

        /** 7. Layout */
        $this->view->layout = $this->request['layout'];

        /** 8. Wrap */
        $this->view->wrap = $this->request['wrap'];

        /** 9. Wrap ID */
        $this->view->wrap_id = $this->request['wrap_id'];

        /** 10. Wrap Class */
        $this->view->wrap_class = $this->request['wrap_class'];

        /** push model results into view */

        /** 1. Query Results */
        $this->view->rowset = $this->model->get('Items');

        /** 2. Pagination */
        $this->view->pagination = $this->model->get('Pagination');

        /** display view */
        //parent::display($cachable, $urlparameters);

        return $this->view->display();

        echo '<pre>';
        var_dump($this->view->request);
        echo '</pre>';
    }

    /**
     * Shared methods for all controllers follow:
     */

    /**
     * initialise
     *
     * initialisation code needed for all tasks
     *
     * @param null $task
     * @return bool
     */
    public function initialise($request)
    {
        $this->request = $request;

        $this->parameters = $this->request['parameters'];

        $this->redirectClass = new MolajoControllerRedirect();
        $this->redirectClass->request = $this->request;

        $this->id = $this->request['id'];
        if ((int)$this->id == 0) {
            $this->id = 0;
            $this->category_id = 0;
        }
        $this->request['category_id'] = 0;
        $this->category_id = $this->request['category_id'];
        if ((int)$this->category_id == 0) {
            $this->category_id = 0;
        }

        /** set model and view for display controller */
        if ($this->request['controller'] == 'display') {

            /** model */
            $this->model = $this->getModel(ucfirst($this->request['model']), ucfirst($this->request['option'] . 'Model'), array());
            $this->model->request = $this->request;
            $this->model->parameters = $this->request['parameters'];

            /** view format */
            $this->view = $this->getView($this->request['view']);
            $this->view->setModel($this->model, true);
            $this->view->setLayout($this->request['layout']);
        }

        /** load table */
        if ($this->request['task'] == 'display'
            || $this->request['task'] == 'add'
            || $this->request['task'] == 'login'
            || $this->request['component_table'] == '__dummy'
        ) {
            $this->isNew = false;

        } else {
            $this->table = $this->model->getTable();
            $this->table->reset();
            $this->table->load($this->id);
            $this->category_id = $this->table->category_id;

            if ($this->id == 0) {
                $this->isNew = true;
                $this->existingState = 0;
            } else {
                $this->isNew = false;
                $this->category_id = $this->table->category_id;
                $this->existingState = $this->table->state;
            }
        }

        /** dispatch events */
        if ($this->dispatcher
            || $this->request['plugin_type'] == ''
        ) {
        } else {
            $this->dispatcher = JDispatcher::getInstance();
            MolajoPlugin::importPlugin($this->request['plugin_type']);
        }

        /** check authorisation **/
        if (MOLAJO_APPLICATION == 'installation') {
        } else {
            $results = MolajoController::checkTaskAuthorisation($this->request['task']);
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
     * Molajo_Note: Task and content shared with ACL for authorisation verification, ACL Implementation data removed from CMS
     *
     * @param null $checkTask
     * @param null $checkId
     * @param null $checkCatid
     * @param null $checkTable
     *
     * @return bool
     */
    public function checkTaskAuthorisation($checkTask = null, $checkId = null, $checkCatid = null, $checkTable = null)
    {
        if ($checkTask == null) {
            $checkTask = $this->getTask();
        }

        if ($this->request['component_table'] == '__dummy') {
            $checkId = 0;
            $checkCatid = 0;
            $checkTable = array();
        } else {

            if ($checkId == null) {
                $checkId = $this->id;
            }

            //           if ($checkCatid == null) {
            //                if ((int)$this->category_id == 0) {
            //                    $checkCatid = (int)$this->table->category_id;
            //                } else {
            //                    $checkCatid = (int)$this->category_id;
            //                }
            //            }

            if ($checkTable == null) {
                $checkTable = $this->table;
            }
        }

        $acl = new MolajoACL ();
        $results = $acl->authoriseTask($this->request['option'], $this->request['view'], $checkTask, $checkId, $checkCatid, $checkTable);

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED') . ' ' . $checkTask);
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * checkInItem
     *
     * Used to check in item if it is already checked out
     *
     * @return bool
     */
    public function checkInItem()
    {
        /** no checkin for new row **/
        if ($this->id == 0) {
            return;
        }

        /** see if table supports checkin **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return;
        }

        /** model: checkin **/
        $results = $this->model->checkin($this->id);

        /** error processing **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_CHECK_IN_FAILED'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** success **/
        return true;
    }

    /**
     * verifyCheckOut
     *
     * method to verify that the current user is recorded in the checked_out column of the item
     *
     * @return    boolean
     */
    public function verifyCheckOut()
    {
        /** no checkout for new row **/
        if ($this->id == 0) {
            return;
        }

        /** no checkout if table does not supports it **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return;
        }

        /** model: checkin **/
        if ($this->table->checked_out == MolajoFactory::getUser()->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER') . ' ' . $this->getTask());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * checkOutItem
     *
     * method to set the checkout_time and checked_out values of the item
     *
     * @return    boolean
     * @since    1.0
     */
    public function checkOutItem()
    {
        /** no checkin for new row **/
        if ($this->id == 0) {
            return true;
        }

        /** see if table supports checkin **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        /** model: checkout **/
        $results = $this->model->checkout($this->id);

        /** error processing **/
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
     * Molajo_Note: All Components have version management save and restore processes as an
     *  automatic option
     *
     * @return    void
     * @since    1.0
     */
    public function createVersion($context)
    {
        /** activated? **/
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no version for create **/
        if ((int)$this->id == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->id);

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion **/
        /** Molajo_Note: New Event onContentCreateVersion so that all data stays in sync **/
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->id, $versionKey));
        if (count($results) && in_array(false, $results, true)) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        return true;
    }

    /**
     * maintainVersionCount
     *
     * Molajo_Note: All Components have version management save and restore processes as
     *  an automatic option
     *
     * @param  $context
     * @return bool
     */
    public function maintainVersionCount($context)
    {
        /** activated? **/
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return;
        }

        /** no versions to delete for create **/
        if ((int)$this->id == 0) {
            return;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete' && $this->parameters->def('retain_versions_after_delete', 1) == 0) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->id, $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' . $this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions **/
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->id, $maintainVersions));
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
        $cache = MolajoFactory::getCache($this->request['option']);
        $cache->clean();
    }

    // crap follows


    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.0
     */
    public function getModel($name = '', $prefix = '', $config = array())
    {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = $this->model_prefix;
        }

        return $this->createModel($name, $prefix, $config);
    }

    /**
     * Method to load and return a model object.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  Optional model prefix.
     * @param   array   $config  Configuration array for the model. Optional.
     *
     * @return  mixed   Model object on success; otherwise null failure.
     *
     * @since   1.0
     * @note    Replaces _createModel.
     */
    protected function createModel($name, $prefix = '', $config = array())
    {
        // Clean the model name
        $modelName = preg_replace('/[^A-Z0-9_]/i', '', $name);
        $classPrefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

        $result = MolajoModel::getInstance($modelName, $classPrefix, $config);

        return $result;
    }

    /**
     * Method to get the controller name
     *
     * The dispatcher name is set by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return  string  The name of the dispatcher
     *
     * @since   1.0
     */
    public function getName()
    {
        if (empty($this->name)) {
            $r = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
                MolajoError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
            }
            $this->name = strtolower($r[1]);
        }

        return $this->name;
    }

    /**
     * Get the last task that is being performed or was most recently performed.
     *
     * @return  string  The task that is being performed or was most recently performed.
     *
     * @since   1.0
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Gets the available tasks in the controller.
     *
     * @return  array  Array[i] of task names.
     *
     * @since   1.0
     */
    public function getTasks()
    {
        return $this->methods;
    }

    /**
     * Method to get a reference to the current view and load it if necessary.
     *
     * @param   string  $name    The view name. Optional, defaults to the controller name.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for view. Optional.
     *
     * @return  object  Reference to the view or an error.
     *
     * @since   1.0
     */
    public function getView($name = '', $prefix = '', $config = array())
    {
        static $views;

        if (!isset($views)) {
            $views = array();
        }

        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = $this->getName() . 'View';
        }

        if (empty($views[$name])) {
            if ($view = $this->createView($name, $prefix, $config)) {
                $views[$name] = & $view;
            }
            else
            {
                $result = MolajoError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $prefix));

                return $result;
            }
        }

        return $views[$name];
    }

    /**
     * Method to load and return a view object. This method first looks in the
     * current template directory for a match and, failing that, uses a default
     * set path to load the view class file.
     *
     * Note the "name, prefix, type" order of parameters, which differs from the
     * "name, type, prefix" order used in related public methods.
     *
     * @param   string  $name    The name of the view.
     * @param   string  $prefix  Optional prefix for the view class name.
     * @param   array   $config  Configuration array for the view. Optional.
     *
     * @return  mixed  View object on success; null or error result on failure.
     *
     * @since   1.0
     * @note    Replaces _createView.
     */
    protected function createView($name, $prefix = '', $config = array())
    {
        $viewClass = $prefix . $name;
        return new $viewClass($config);
    }
}
