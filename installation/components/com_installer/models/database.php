<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Database configuration model for Installer.
 *
 * @package		Molajo
 * @subpackage  Installation
 * @since		1.0
 */
class InstallerModelDatabase extends InstallerModelDisplay
{
    protected $_db = null;

    public function __construct($properties = null)
    {
        parent::__construct($properties);

        $this->_db = MolajoFactory::getDDbo();
    }
    
	function initialise($options)
	{
		// Get the options as a JObject for easier handling.
		$options = JArrayHelper::toObject($options, 'JObject');

		// Load the back-end language files so that the DB error messages work
		$jlang = MolajoFactory::getLanguage();
		// Pre-load en-GB in case the chosen language files do not exist
		$jlang->load('joomla', MOLAJO_PATH_ADMINISTRATOR, 'en-GB', true);
		// Load the selected language
		$jlang->load('joomla', MOLAJO_PATH_ADMINISTRATOR, $options->language, true);

		// Ensure a database type was selected.
		if (empty($options->db_type)) {
			$this->setError(MolajoText::_('INSTL_DATABASE_INVALID_TYPE'));
			return false;
		}

		// Ensure that a valid hostname and user name were input.
		if (empty($options->db_host) || empty($options->db_user)) {
			$this->setError(MolajoText::_('INSTL_DATABASE_INVALID_DB_DETAILS'));
			return false;
		}

		// Ensure that a database name was input.
		if (empty($options->db_name)) {
			$this->setError(MolajoText::_('INSTL_DATABASE_EMPTY_NAME'));
			return false;
		}

		// Validate database table prefix.
		if (!preg_match('#^[a-zA-Z]+[a-zA-Z0-9_]*$#', $options->db_prefix)) {
			$this->setError(MolajoText::_('INSTL_DATABASE_PREFIX_INVALID_CHARS'));
			return false;
		}

		// Validate length of database table prefix.
		if (strlen($options->db_prefix) > 15) {
			$this->setError(MolajoText::_('INSTL_DATABASE_FIX_TOO_LONG'));
			return false;
		}

		// Validate length of database name.
		if (strlen($options->db_name) > 64) {
			$this->setError(MolajoText::_('INSTL_DATABASE_NAME_TOO_LONG'));
			return false;
		}

		// If the database is not yet created, create it.
		if (empty($options->db_created)) {
			// Get a database object.
			$db = $this->getDbo($options->db_type, $options->db_host, $options->db_user, $options->db_pass, null, $options->db_prefix, false);

			// Check for errors.
			if (JError::isError($db)) {
				$this->setError(MolajoText::sprintf('INSTL_DATABASE_COULD_NOT_CONNECT', (string)$db));
				return false;
			}

			// Check for database errors.
			if ($err = $db->getErrorNum()) {
				$this->setError(MolajoText::sprintf('INSTL_DATABASE_COULD_NOT_CONNECT', $db->getErrorNum()));
				return false;
			}

			// Check database version.
			$db_version = $db->getVersion();
			if (($position = strpos($db_version, '-')) !== false) {
				$db_version = substr($db_version, 0, $position);
			}

			if (!version_compare($db_version, '5.0.4', '>=')) {
				$this->setError(MolajoText::sprintf('INSTL_DATABASE_INVALID_MYSQL_VERSION', $db_version));
				return false;
			}
			// @internal MySQL versions pre 5.1.6 forbid . / or \ or NULL
			if ((preg_match('#[\\\/\.\0]#', $options->db_name)) && (!version_compare($db_version, '5.1.6', '>='))) {
				$this->setError(MolajoText::sprintf('INSTL_DATABASE_INVALID_NAME', $db_version));
				return false;
			}

			// @internal Check for spaces in beginning or end of name
			if (strlen(trim($options->db_name)) <> strlen($options->db_name)) {
				$this->setError(MolajoText::_('INSTL_DATABASE_NAME_INVALID_SPACES'));
				return false;
			}

			// @internal Check for asc(00) Null in name
			if (strpos($options->db_name, chr(00)) !== false) {
				$this->setError(MolajoText::_('INSTL_DATABASE_NAME_INVALID_CHAR'));
				return false;
			}

			// Check utf8 support.
			$utfSupport = $db->hasUTF();

			// Try to select the database
			if (!$db->select($options->db_name)) {
				// If the database could not be selected, attempt to create it and then select it.
				if ($this->createDatabase($db, $options->db_name, $utfSupport)) {
					$db->select($options->db_name);
				} else {
					$this->setError(MolajoText::sprintf('INSTL_DATABASE_ERROR_CREATE', $options->db_name));
					return false;
				}
			} else {
				// Set the character set to UTF-8 for pre-existing databases.
				$this->setDatabaseCharset($db, $options->db_name);
			}

			// Should any old database tables be removed or backed up?
			if ($options->db_old == 'remove') {
				// Attempt to delete the old database tables.
				if (!$this->deleteDatabase($db, $options->db_name, $options->db_prefix)) {
					$this->setError(MolajoText::_('INSTL_DATABASE_ERROR_DELETE'));
					return false;
				}
			} else {
				// If the database isn't being deleted, back it up.
				if (!$this->backupDatabase($db, $options->db_name, $options->db_prefix)) {
					$this->setError(MolajoText::_('INSTL_DATABASE_ERROR_BACKINGUP'));
					return false;
				}
            }

			// Set the appropriate schema script based on UTF-8 support.
			$type = $options->db_type;
			$schema = 'sql/'.(($type == 'mysqli') ? 'mysql' : $type).'/molajo.sql';

			// Attempt to import the database schema.
			if (!$this->populateDatabase($db, $schema)) {
				$this->setError(MolajoText::sprintf('INSTL_ERROR_DB', $this->getError()));
				return false;
			}

			// Attempt to update the table #__schema.
			$files = JFolder::files(MOLAJO_PATH_ROOT.'/administrator/components/com_admin/sql/updates/mysql/', '\.sql$');
			if (empty($files)) {
				$this->setError(MolajoText::_('INSTL_ERROR_INITIALISE_SCHEMA'));
				return false;
			}
			$version = '';
			foreach ($files as $file) {
				if (version_compare($version, JFile::stripExt($file)) <0) {
					$version = JFile::stripExt($file);
				}
			}
			$query = $db->getQuery(true);
			$query->insert('#__schemas');
			$query->values('999, '. $db->quote($version));

			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum()) {
				$this->setError($db->getErrorMsg());
				return false;
			}

			// Attempt to refresh manifest caches
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__extensions');
			$db->setQuery($query);
			$extensions = $db->loadObjectList();
			// Check for errors.
			if ($db->getErrorNum()) {
				$this->setError($db->getErrorMsg());
				$return = false;
			}
			MolajoFactory::$database = $db;
			$installer = JInstaller::getInstance();
//			foreach ($extensions as $extension) {
//				if (!$installer->refreshManifestCache($extension->extension_id)) {
//					$this->setError(MolajoText::sprintf('INSTL_DATABASE_COULD_NOT_REFRESH_MANIFEST_CACHE', $extension->name));
//					return false;
//				}
//			}

			// Load the localise.sql for translating the data in molajo.sql/joomla_backwards.sql
			$dblocalise = 'sql/'.(($type == 'mysqli') ? 'mysql' : $type).'/localise.sql';
			if (JFile::exists($dblocalise)) {
				if (!$this->populateDatabase($db, $dblocalise)) {
					$this->setError(MolajoText::sprintf('INSTL_ERROR_DB', $this->getError()));
					return false;
				}
			}

			// Handle default backend language setting. This feature is available for localized versions of Joomla 1.5.
			$app = MolajoFactory::getApplication();
			$languages = $app->getLocaliseAdmin($db);
			if (in_array($options->language, $languages['admin']) || in_array($options->language, $languages['site'])) {
				// Build the language parameters for the language manager.
				$params = array();

				// Set default administrator/site language to sample data values:
				$params['administrator'] = 'en-GB';
				$params['site'] = 'en-GB';

				if (in_array($options->language, $languages['admin'])) {
					$params['administrator'] = $options->language;
				}
				if (in_array($options->language, $languages['site'])) {
					$params['site'] = $options->language;
				}
				$params = json_encode($params);

				// Update the language settings in the language manager.
				$db->setQuery(
					'UPDATE `#__extensions`' .
					' SET `params` = '.$db->Quote($params) .
					' WHERE `element`="com_languages"'
				);

				// Execute the query.
				$db->query();

				// Check for errors.
				if ($db->getErrorNum()) {
					$this->setError($db->getErrorMsg());
					$return = false;
				}
			}
		}

		return true;
	}
    /**
     * installSampleData
     *
     * @param $options
     * @return bool
     */
	function installSampleData($options)
	{
		// Get the options as a JObject for easier handling.
		$options = JArrayHelper::toObject($options, 'JObject');

		// Get a database object.
		$db = MolajoInstallationHelperDatabase::getDBO($options->db_type, $options->db_host, $options->db_user, $options->db_pass, $options->db_name, $options->db_prefix);

		// Check for errors.
		if (JError::isError($db)) {
			$this->setError(MolajoText::sprintf('INSTL_DATABASE_COULD_NOT_CONNECT', (string)$db));
			return false;
		}

		// Check for database errors.
		if ($err = $db->getErrorNum()) {
			$this->setError(MolajoText::sprintf('INSTL_DATABASE_COULD_NOT_CONNECT', $db->getErrorNum()));
			return false;
		}

		// Build the path to the sample data file.
		$type = $options->db_type;
		if ($type == 'mysqli') {
			$type = 'mysql';
		}

		$data = MOLAJO_PATH_INSTALLATION.'/sql/'.$type.'/' . $options->sample_file;

		// Attempt to import the database schema.
		if (!file_exists($data)) {
			$this->setError(MolajoText::sprintf('INSTL_DATABASE_FILE_DOES_NOT_EXIST', $data));
			return false;
		}
		elseif (!$this->populateDatabase($db, $data)) {
			$this->setError(MolajoText::sprintf('INSTL_ERROR_DB', $this->getError()));
			return false;
		}

		return true;
	}

//	/**
//     * getDbo
//     *
//	 * Method to get a JDatabase object.
//	 *
//	 * @param	string	$driver		The database driver to use.
//	 * @param	string	$host		The hostname to connect on.
//	 * @param	string	$user		The user name to connect with.
//	 * @param	string	$password	The password to use for connection authentication.
//	 * @param	string	$database	The database to use.
//	 * @param	string	$prefix		The table prefix to use.
//	 * @param	boolean $select		True if the database should be selected.
//	 *
//	 * @return	mixed	JDatabase object on success, JException on error.
//	 * @since	1.0
//	 */
//	public function & getDbo($driver, $host, $user, $password, $database, $prefix, $select = true)
//	{
//		static $db;
//
//		if (!$db) {
//			// Build the connection options array.
//			$options = array (
//				'driver' => $driver,
//				'host' => $host,
//				'user' => $user,
//				'password' => $password,
//				'database' => $database,
//				'prefix' => $prefix,
//				'select' => $select
//			);
//
//			// Get a database object.
//			$db = JDatabase::getInstance($options);
//		}
//
//		return $db;
//	}

	/**
	 * Method to backup all tables in a database with a given prefix.
	 *
	 * @param	JDatabase	&$db	JDatabase object.
	 * @param	string		$name	Name of the database to process.
	 * @param	string		$prefix	Database table prefix.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function backupTables()
	{
        $conf = MolajoFactory::getConfig();

        $prefix	  = $conf->get('dbprefix');
        $database = $conf->get('db');

		// Initialise variables.
		$return = true;
		$backup = 'bak_' . $prefix;

		// Get the tables in the database.
		$this->_db->setQuery(
			'SHOW TABLES' .
			' FROM '.$this->_db->nameQuote($database)
		);

		if ($tables = $this->_db->loadResultArray()) {
			foreach ($tables as $table)
			{
				// If the table uses the given prefix, back it up.
				if (strpos($table, $prefix) === 0) {
					// Backup table name.
//                    $backupTable = str_replace($prefix, $backup, $table);
					$backupTable = str_replace($prefix, $backup, $table);

					// Drop the backup table.
					$this->_db->setQuery(
						'DROP TABLE IF EXISTS '.$this->_db->nameQuote($backupTable)
					);
					$this->_db->query();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						$this->setError($this->_db->getErrorMsg());
						$return = false;
					}

					// Rename the current table to the backup table.
					$this->_db->setQuery(
						'RENAME TABLE '.$this->_db->nameQuote($table).' TO '.$this->_db->nameQuote($backupTable)
					);
					$this->_db->query();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						$this->setError($this->_db->getErrorMsg());
						$return = false;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Method to create a new database.
	 *
	 * @param	JDatabase	&$db	JDatabase object.
	 * @param	string		$name	Name of the database to create.
	 * @param	boolean 	$utf	True if the database supports the UTF-8 character set.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function createDatabase(& $db, $name, $utf)
	{
		// Build the create database query.
		if ($utf) {
			$query = 'CREATE DATABASE '.$db->nameQuote($name).' CHARACTER SET `utf8`';
		}
		else {
			$query = 'CREATE DATABASE '.$db->nameQuote($name);
		}

		// Run the create database query.
		$db->setQuery($query);
		$db->query();

		// If an error occurred return false.
		if ($db->getErrorNum()) {
			return false;
		}

		return true;
	}

	/**
	 * Method to delete all tables in a database with a given prefix.
	 *
	 * @param	JDatabase	&$db	JDatabase object.
	 * @param	string		$name	Name of the database to process.
	 * @param	string		$prefix	Database table prefix.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function deleteTables()
	{
		// Initialise variables.
		$return = true;
        
        $conf = MolajoFactory::getConfig();

        $prefix	  = $conf->get('dbprefix');
        $database = $conf->get('db');
        

		// Get the tables in the database.
		$this->_db->setQuery(
			'SHOW TABLES FROM '.$this->_db->nameQuote($name)
		);
		if ($tables = $this->_db->loadResultArray()) {
			foreach ($tables as $table)
			{
				// If the table uses the given prefix, drop it.
				if (strpos($table, $prefix) === 0) {
					// Drop the table.
					$this->_db->setQuery(
						'DROP TABLE IF EXISTS '.$this->_db->nameQuote($table)
					);
					$this->_db->query();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						$this->setError($this->_db->getErrorMsg());
						$return = false;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Method to import a database schema from a file.
	 *
	 * @param	JDatabase	&$db	JDatabase object.
	 * @param	string		$schema	Path to the schema file.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function populateDatabase(& $db, $schema)
	{
		// Initialise variables.
		$return = true;

		// Get the contents of the schema file.
		if (!($buffer = file_get_contents($schema))) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Get an array of queries from the schema and process them.
		$queries = $this->_splitQueries($buffer);
		foreach ($queries as $query)
		{
			// Trim any whitespace.
			$query = trim($query);

			// If the query isn't empty and is not a comment, execute it.
			if (!empty($query) && ($query{0} != '#')) {
				// Execute the query.
				$db->setQuery($query);
				$db->query();

				// Check for errors.
				if ($db->getErrorNum()) {
					$this->setError($db->getErrorMsg());
					$return = false;
				}
			}
		}

		return $return;
	}

	/**
	 * Method to set the database character set to UTF-8.
	 *
	 * @param	JDatabase	&$db	JDatabase object.
	 * @param	string		$name	Name of the database to process.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function setDatabaseCharset(& $db, $name)
	{
		// Only alter the database if it supports the character set.
		if ($db->hasUTF()) {
			// Run the create database query.
			$db->setQuery(
				'ALTER DATABASE '.$db->nameQuote($name).' CHARACTER' .
				' SET `utf8`'
			);
			$db->query();

			// If an error occurred return false.
			if ($db->getErrorNum()) {
				return false;
			}
		}

		return true;
	}

	/**
     * _splitQueries
     *
	 * Method to split up queries from a schema file into an array.
	 *
	 * @param	string	$sql SQL schema.
	 *
	 * @return	array	Queries to perform.
	 * @since	1.0
	 * @access	protected
	 */
	function _splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			if ($sql[$i] == ";" && !$in_string) {
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
			$queries[] = $sql;
		}

		return $queries;
	}
}