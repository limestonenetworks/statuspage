<?php

class Facilities {
	private $db;

	function __construct() {
		global $db;

		if (empty($db)) throw new Exception('Unable to contact database');

		$this->db = $db;
	}

	function getFacilities() {
		$facilities_query = $this->db->query("SELECT * FROM facilities");

		$row = array();

		while($res = $facilities_query->fetchArray(SQLITE3_ASSOC)){ 
			$row[$res['id']] = $res;
		}

		return $row;
	}
}

?>
