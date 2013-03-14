<?php

class Database {
	private $dbpath;
	public $sqlite;

	function __construct($dbpath) {
		$this->dbpath = $dbpath;
		$this->initialize();	
	}

	function _get_count_result($handle) {
		$count = 0;
		if (gettype($handle) == 'object') {
                        $row = array();
			$row = $handle->fetchArray();
                        $count = $row['COUNT(*)'];
                }
                else {
                        $count = $handle;
                }

		return $count;
	}

	function initialize() {
		if (($this->sqlite = new SQLite3($this->dbpath)) === false) throw new Exception('Unable to save database file.');

		$check_users = @$this->sqlite->query('SELECT COUNT(*) FROM users');
		$check_users_count = $this->_get_count_result($check_users);
		if (!isset($check_users) || $check_users_count < 1) {
			// Creating user table
			echo "Creating user table...<br />\n";
			$this->sqlite->query("CREATE TABLE users (id integer primary key autoincrement, username varchar unique, password varchar)");

			// Creating default user
			$query = $this->sqlite->prepare("INSERT INTO users (username, password) VALUES ('admin', :adminpass)");
			$query->bindValue(':adminpass',  md5('admin'), SQLITE3_TEXT);
			$result = $query->execute();
		}

		$check_facilities = @$this->sqlite->query('SELECT COUNT(*) FROM facilities');
		$check_facilities_count = $this->_get_count_result($check_facilities);
		if ($check_facilities === false || $check_facilities_count < 1) {
			// Creating facilities table
			echo "Creating facilities table...<br />\n";
			$this->sqlite->query("CREATE TABLE facilities (id integer primary key autoincrement, friendly_name varchar, visible int default 1)");

			// Creating default facility
			global $config;
			foreach ($config['default_facilities'] as $facility) {
				$query = $this->sqlite->prepare("INSERT INTO facilities (friendly_name) VALUES (:facilityname)");
				$query->bindValue(':facilityname', $facility, SQLITE3_TEXT);
				$query->execute();
			}
		}


		$check_facilities_services = @$this->sqlite->query('SELECT COUNT(*) FROM facilities_services');
		$check_facilities_services_count = $this->_get_count_result($check_facilities_services);
		if ($check_facilities_services === false || $check_facilities_services_count < 1) {
			// Creating facilities_services table
			echo "Creating facilities_services table...<br />\n";
			$this->sqlite->query("CREATE TABLE facilities_services (id integer primary key autoincrement, facilities_id integer, friendly_name varchar, status varchar)");

			// Creating default services
			global $config;
			foreach ($config['default_services'] as $service) {
				$query = $this->sqlite->prepare("INSERT INTO facilities_services (facilities_id, friendly_name, status) VALUES (1, :servicename, 'online')");
				$query->bindValue(':servicename', $service, SQLITE3_TEXT);
				$query->execute();
			}
		}

		$check_incidents = @$this->sqlite->query('SELECT COUNT(*) FROM incidents');
		if ($check_incidents === false) {
			// Creating incidents table
			echo "Creating incidents table...<br />\n";
			$this->sqlite->query("CREATE TABLE incidents (
				id integer primary key autoincrement,
				facilities_id integer,
				title varchar,
				maintenancedesc text,
				status varchar,
				severity varchar,
				timeopened timestamp,
				timeclosed timestamp,
				visible int default 1
			)");

			// Create an index for the opened time
			$this->sqlite->query("CREATE INDEX incidents_timeopened ON incidents(timeopened)");
		}

		$check_updates = @$this->sqlite->query('SELECT COUNT(*) FROM incidents_updates');
		if ($check_updates === false) {
			// Creating updates table
			echo "Creating updates table...<br />\n";
			$this->sqlite->query("CREATE TABLE incidents_updates (
				id integer primary key autoincrement,
				incidents_id integer,
				timeadded timestamp,
				message text,
				visible int default 1
			)");

			// Create an index for the opened time
			$this->sqlite->query("CREATE INDEX incidents_updates_timeadded ON incidents_updates(timeadded)");
			$this->sqlite->query("CREATE INDEX incidents_updates_incidents_id ON incidents_updates(incidents_id)");
		}
	}
}

?>
