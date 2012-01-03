<?php
/**
 * @package     Molajo
 * @subpackage  Redirect
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Redirect Controller
 *
 * @package    Molajo
 * @subpackage    Controller
 * @since    1.0
 */

class MolajoControllerRedirect
{
    /**
     * $redirect
     *
     * @var object
     */
    protected $redirect = null;

    /**
     * $redirectAction
     *
     * @var boolean
     */
    protected $redirectAction = null;

    /**
     * $successIndicator
     *
     * @var boolean
     */
    protected $successIndicator = null;

    /**
     * $redirectMessage
     *
     * @var boolean
     */
    protected $redirectMessage = null;

    /**
     * $redirectMessageType
     *
     * @var boolean
     */
    protected $redirectMessageType = null;

    /**
     * $redirectReturn
     *
     * @var string
     */
    protected $redirectReturn = null;

    /**
     * $redirectSuccess
     *
     * @var string
     */
    protected $redirectSuccess = null;

    /**
     * $datakey
     *
     * @var string
     */
    protected $datakey = null;

    /**
     * $return_page
     *
     * @var string
     */
    protected $return_page = null;

    /**
     * $request Array
     *
     * @var object
     */
    public $requestArray = null;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param    array   $requestArray    An optional associative array of configuration settings.
     *
     * @since    1.0
     */
    public function __construct($requestArray = array())
    {
        $this->requestArray = $requestArray;
    }

    /**
     * initialise
     *
     * Establish the Link needed for redirecting after the task is complete (or fails)
     *
     * @return    boolean
     * @since    1.0
     */
    public function initialise()
    {

        /** 1. ajax and non-html output **/
        $format = $this->requestArray['format'];
        if ($format == 'html' || $format == null || $format == '') {
            $format = 'html';
        } else {
            $this->setRedirectAction(false);
            return;
        }

        /** 2. display, add, edit tasks **/
        if ($this->requestArray['task'] == 'display'
            || $this->requestArray['task'] == 'add'
            || $this->requestArray['task'] == 'edit'
        ) {
            $this->setRedirectAction(false);
            return;
        }

        /** remaining: tasks that will redirect to a display/add/edit task upon completion **/
        $this->redirectAction = true;

        /** extension: category uses this parameter **/
        $extension = $this->requestArray['extension'];
        if ($extension == ''
            || $extension == null
        ) {
            $extension = '';
        } else {
            $extension = '&extension=' . $extension;
        }

        /** component_specific: to add parameter pairs needed in addition to standard **/
        $component_specific = $this->requestArray['component_specific'];
        if ($component_specific == ''
            || $component_specific == null
        ) {
            $component_specific = '';
        } elseif (substr($component_specific, 1, 1) == '&') {
        } else {
            $component_specific .= '&' . $component_specific;
        }

        /** cancel **/
        if ($this->requestArray['task'] == 'cancel') {
            if (MolajoController::getApplication()->getName() == 'site') {
                if ($this->requestArray['id'] == 0) {
                    $this->redirectSuccess = 'index.php';
                } else {
                    $this->redirectSuccess = 'index.php?option=' . $this->requestArray['option'] . '&view=display&id=' . $this->requestArray['id'] . $extension . $component_specific;
                }
            } else {
                $this->redirectSuccess = 'index.php?option=' . $this->requestArray['option'] . '&view=edit&id=' . $this->requestArray['id'] . $extension . $component_specific;
            }
            $this->redirectReturn = $this->redirectSuccess;
            return true;
        }

        if ($this->requestArray['task'] == 'login') {
            $this->redirectSuccess = 'index.php?option=dashboard&view=display';
            $this->redirectReturn = 'index.php?option=login';

        } elseif ($this->requestArray['task'] == 'logout') {
            $this->redirectSuccess = 'index.php';
            $this->redirectReturn = 'index.php?option=' . $this->requestArray['option'] . '&view=display' . $extension . $component_specific;

        } elseif ($this->requestArray['task'] == 'display') {
            $this->redirectSuccess = 'index.php?option=' . $this->requestArray['option'] . '&view=display' . $extension . $component_specific;
            $this->redirectReturn = $this->redirectSuccess;

        } else {
            $this->redirectSuccess = 'index.php?option=' . $this->requestArray['option'] . '&view=display' . $extension . $component_specific;
            $this->redirectReturn = 'index.php?option=' . $this->requestArray['option'] . '&view=edit' . $extension . $component_specific;
        }

        return;
    }

    /**
     * setDatakey
     *
     * unique random value and $datakey parameter used for storing and retrieving form contents from session
     * instead of $context for returning due to an error
     *
     * @return    string    The return URL.
     * @since    1.0
     */
    protected function setDatakey()
    {
        $this->requestArray['datakey'] = mt_rand();
        return;
    }

    /**
     * setRedirectAction
     *
     * Indicator of whether or not a redirect should be issued
     *
     * @return    boolean
     * @since    1.0
     */
    public function setRedirectAction($action)
    {
        $this->redirectAction = $action;
        return;
    }

    /**
     * setRedirectMessageType
     *
     * Message Type of Message: message, warning, or error
     *
     * @return    boolean
     * @since    1.0
     */
    public function setRedirectMessageType($messagetype)
    {
        $this->redirectMessageType = $messagetype;
        return;
    }

    /**
     * setRedirectMessage
     *
     * User Message regarding Task conclusion
     *
     * @return    boolean
     * @since    1.0
     */
    public function setRedirectMessage($message)
    {
        $this->redirectMessage = $message;
        return;
    }

    /**
     * setSuccessIndicator
     *
     * Indicator as to whether or not the task succeeded or failed
     *
     * @return    boolean
     * @since    1.0
     */
    public function setSuccessIndicator($indicator = true)
    {
        $this->successIndicator = (boolean)$indicator;
        $this->redirect();
    }

    /**
     * redirect
     *
     * Redirects the browser or returns false if no redirect is set.
     *
     * @return    boolean    False if no redirect exists.
     * @since    1.0
     */
    public function redirect($task = null)
    {
        /** Display Tasks, non-Component Output, and non-HTML format tasks do not redirect **/
        if ($this->redirectAction === false) {
            return false;
        }

        /** task **/
        if ($task == null) {
            $task = $this->data['task'];
        }

        /** message and message type **/
        if ($this->successIndicator === false) {

            if ($this->redirectMessage == null || $this->redirectMessage == '') {
                $this->redirectMessage = MolajoTextHelper::_('MOLAJO_STANDARD_FAILURE_MESSAGE');
            }
            if ($this->redirectMessageType == null) {
                $this->redirectMessageType = 'error';
            }

        } else {

            /** defaults to success **/
            if ($this->redirectMessage == null) {
                $this->redirectMessage = MolajoTextHelper::_('MOLAJO_STANDARD_SUCCESS_MESSAGE');
            }
            if ($this->redirectMessageType == null) {
                $this->redirectMessageType = 'message';
            }
        }

        /** list **/
        if ($this->requestArray['controller'] == $this->requestArray['DefaultView']) {
            $link = $this->redirectSuccess;

            /** failure **/
        } else if ($this->successIndicator === false || $task == 'apply' || $task == 'saveandnew') {
            $link = $this->redirectReturn;
            if ($this->requestArray['EditView'] == '') {
            } else {
                $id = $this->data['id'];
                if ((int)$id == 0 || $task == 'saveandnew') {
                    $link .= '&task=' . $this->requestArray['EditView'] . '.add' . '&datakey=' . $this->datakey;

                } else {
                    $link .= '&task=' . $this->requestArray['EditView'] . '.edit&id=' . (int)$id . '&datakey=' . $this->datakey;
                }
            }

            /** success */
        } else {
            $id = $this->data['id'];
            if ((int)$id == 0) {
                $idLink = '';
            } else {
                $idLink = '&id=' . (int)$id;
            }
            $link = $this->redirectSuccess . $idLink;
        }

        /** should not be needed */
        if ($link == '') {
            $link = 'index.php';
        }

        /** redirect **/
        MolajoController::getApplication()->redirect(MolajoRouteHelper::_($link, false), $this->redirectMessage, $this->redirectMessageType);
    }
}