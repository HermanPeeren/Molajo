<?php
/**
 * @package     Molajo
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * APC session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @see         http://www.php.net/manual/en/function.session-set-save-handler.php
 * @since       11.1
 */
class MolajoSessionStorageApc extends MolajoSessionStorage
{
    /**
     * Constructor
     *
     * @param   array  $options  Optional parameters
     *
     * @return  MolajoSessionStorageApc
     *
     * @since   11.1
     */
    public function __construct($options = array())
    {
        if (!$this->test()) {
            return MolajoError::raiseError(404, MolajoText::_('JLIB_SESSION_APC_EXTENSION_NOT_AVAILABLE'));
        }

        parent::__construct($options);
    }

    /**
     * Open the SessionHandler backend.
     *
     * @param   string  $save_path     The path to the session object.
     * @param   string  $session_name  The name of the session.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close the SessionHandler backend.
     *
     * @return boolean  True on success, false otherwise.
     */
    public function close()
    {
        return true;
    }

    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     *
     * @return  string  The session data.
     *
     * @since   11.1
     */
    public function read($id)
    {
        $sess_id = 'sess_'.$id;
        return (string)apc_fetch($sess_id);
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param   string  $id            The session identifier.
     * @param   string  $session_data  The session data.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function write($id, $session_data)
    {
        $sess_id = 'sess_'.$id;
        return apc_store($sess_id, $session_data, ini_get("session.gc_maxlifetime"));
    }

    /**
     * Destroy the data for a particular session identifier in the SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function destroy($id)
    {
        $sess_id = 'sess_'.$id;
        return apc_delete($sess_id);
    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param   integer  $maxlifetime  The maximum age of a session.
     *
     * @return boolean  True on success, false otherwise.
     */
    public function gc($maxlifetime = null)
    {
        return true;
    }

    /**
     * Test to see if the SessionHandler is available.
     *
     * @return boolean  True on success, false otherwise.
     */
    static function test()
    {
        return extension_loaded('apc');
    }
}
