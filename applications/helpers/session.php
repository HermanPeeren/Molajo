<?php
/**
 * @package     Molajo
 * @subpackage  Session
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Class to manage the session
 *
 * Users session for the application.
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoSessionHelper extends JObject
{
    /**
     * $_session
     *
     * @var    object Session
     * @since  1.0
     */
    protected $_session = null;

    /**
     * createSession
     *
     * Create the user session.
     *
     * Old sessions are flushed based on the configuration value for the cookie
     * lifetime. If an existing session, then the last access time is updated.
     * If a new session, a session id is generated and a record is created in
     * the #__sessions table.
     *
     * @param   string  $name  The sessions name.
     *
     * @return  MolajoSession  MolajoSession on success. May call exit() on database error.
     *
     * @since  1.0
     */
    public function createSession($name)
    {
        $options = array();
        $options['name'] = $name;

        if ($this->_getConfig('force_ssl') == 2) {
            $options['force_ssl'] = true;
        }

        /** retrieve session */
        $this->_session = MolajoController::getApplication()->getSession($options);

        /** unlock */

        /** The modulus introduces a little entropy so that queries only fires less than half the time. */
        $time = time() % 2;
        if ($time) {
        } else {
            return $this->_session;
        }

        $this->_removeExpiredSessions();

        $this->_checkSession();

        return $this->_session;
    }

    /**
     * _removeExpiredSessions
     *
     * @return void
     */
    protected function _removeExpiredSessions()
    {
        $db = MolajoController::getDbo();
        $db->setQuery(
            'DELETE FROM `#__sessions`' .
            ' WHERE `session_time` < ' . (int)(time() - $this->_session->getExpire())
        );
        $db->query();
    }

    /**
     * _checkSession
     *
     * Checks the user session.
     *
     * If the session record doesn't exist, initialise it.
     * If session is new, create session variables
     *
     * @return  void
     *
     * @since  1.0
     */
    protected function _checkSession()
    {
        $db = MolajoController::getDbo();
        $session = MolajoController::getApplication()->getSession();
        $user = MolajoController::getUser();

        $db->setQuery(
            'SELECT `session_id`' .
            ' FROM `#__sessions`' .
            ' WHERE `session_id` = ' . $db->quote($session->getId()), 0, 1
        );
        $exists = $db->loadResult();
        if ($exists) {
            return;
        }

        if ($session->isNew()) {
            $db->setQuery(
                'INSERT INTO `#__sessions` (`session_id`, `application_id`, `session_time`)' .
                ' VALUES (' . $db->quote($session->getId()) . ', ' . (int)MOLAJO_APPLICATION_ID . ', ' . (int)time() . ')'
            );

        } else {
            $db->setQuery(
                'INSERT INTO `#__sessions` (`session_id`, `application_id`, `session_time`, `user_id`)' .
                ' VALUES (' .
                $db->quote($session->getId()) . ', ' .
                (int)MOLAJO_APPLICATION_ID . ', ' .
                (int)$session->get('session.timer.start') . ', ' .
                (int)$user->get('id') . ')'
            );
        }

        // If the insert failed, exit the application.
        if ($db->query()) {
        } else {
            jexit($db->getErrorMSG());
        }

        // Session doesn't exist yet, so create session variables
        if ($session->isNew()) {
            $session->set('registry', new JRegistry('session'));
            $session->set('user', new MolajoUser());
        }
    }

    /**
     * _getConfig
     *
     * Gets a configuration value.
     *
     * @param   string   The name of the value to get.
     * @param   string   Default value to return
     *
     * @return  mixed    The user state.
     *
     * @since  1.0
     */
    protected function _getConfig($varname, $default = null)
    {
        return MolajoController::getApplication()->get('' . $varname, $default);
    }
}
