<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Database session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 * @see            http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class MolajoSessionStorageDatabase extends MolajoSessionStorage
{
    protected $_data = null;

    /**
     * Open the SessionHandler backend.
     *
     * @param   string   The path to the session object.
     * @param   string   The name of the session.
     * @return  boolean  True on success, false otherwise.
     * @since   1.0
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close the SessionHandler backend.
     *
     * @return  boolean  True on success, false otherwise.
     * @since   1.0
     */
    public function close()
    {
        return true;
    }

    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *
     * @param   string   The session identifier.
     * @return  string   The session data.
     * @since   1.0
     */
    public function read($id)
    {
        // Get the database connection object and verify its connected.
        $db = MolajoController::getDbo();
        if (!$db->connected()) {
            return false;
        }

        // Get the session data from the database table.
        $db->setQuery(
            'SELECT `data`' .
                ' FROM `#__sessions`' .
                ' WHERE `session_id` = ' . $db->quote($id)
        );
        return (string)$db->loadResult();
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param   string   The session identifier.
     * @param   string   The session data.
     *
     * @return  boolean  True on success, false otherwise.
     * @since   1.0
     */
    public function write($id, $data)
    {
        // Get the database connection object and verify its connected.
        $db = MolajoController::getDbo();

//        if ($db->connected()) {
//        } else {
//            return false;
 //       }

        $query = $db->getQuery(true);

        $query->update($db->nameQuote('#__sessions'));
        $query->set($db->nameQuote('data') . ' = ' . $db->quote($data));
        $query->set($db->nameQuote('session_time') . ' = ' . (int)time());
        $query->where($db->nameQuote('session_id') . ' = ' . $db->quote($id));

        // Try to update the session data in the database table.
        //		$db->setQuery(
        //			'UPDATE `#__sessions`' .
        //			' SET `data` = '.$db->quote($data).',' .
        //			'	  `session_time` = '.(int) time() .
        //			' WHERE `session_id` = '.$db->quote($id)
        //		);
        if ($db->query()) {
        } else {
            return false;
        }

        if ($db->getAffectedRows()) {
            return true;
        }

        $query = $db->getQuery(true);
        $db->setQuery(
            'INSERT INTO `#__sessions` (`session_id`, `application_id`, `data`, `session_time`)' .
                ' VALUES (' . $db->quote($id) . ', ' . $db->quote(MOLAJO_APPLICATION_ID) . ', ' . $db->quote($data) . ', ' . (int)time() . ')'
        );

        return (boolean)$db->query();
    }

    /**
     * Destroy the data for a particular session identifier in the
     * SessionHandler backend.
     *
     * @param   string   The session identifier.
     *
     * @return  boolean  True on success, false otherwise.
     * @since   1.0
     */
    public function destroy($id)
    {
        // Get the database connection object and verify its connected.
        $db = MolajoController::getDbo();
        if (!$db->connected()) {
            return false;
        }

        // Remove a session from the database.
        $db->setQuery(
            'DELETE FROM `#__sessions`' .
                ' WHERE `session_id` = ' . $db->quote($id)
        );
        return (boolean)$db->query();
    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param   integer  The maximum age of a session.
     * @return  boolean  True on success, false otherwise.
     * @since   1.0
     */
    function gc($lifetime = 1440)
    {
        // Get the database connection object and verify its connected.
        $db = MolajoController::getDbo();
        if (!$db->connected()) {
            return false;
        }

        // Determine the timestamp threshold with which to purge old sessions.
        $past = time() - $lifetime;

        // Remove expired sessions from the database.
        $db->setQuery(
            'DELETE FROM `#__sessions`' .
                ' WHERE `session_time` < ' . (int)$past
        );
        return (boolean)$db->query();
    }
}