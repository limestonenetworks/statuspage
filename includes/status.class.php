<?php

class Status {
	private $db;

	function __construct() {
		global $db;

		if (empty($db)) throw new Exception('Unable to contact database');

		$this->db = $db;
	}

	function newIncident($data) {
		// Check for required data
		if (empty($data['title'])) throw new Exception('A title must be specified');
		if (empty($data['status'])) throw new Exception('A status must be specified');
		if (empty($data['facilities_id'])) throw new Exception('A facility ID must be specified');
		if (empty($data['severity'])) throw new Exception('A severity must be specified');
		if (!in_array($data['severity'], array('warning', 'offline'))) throw new Exception('Severity must be "warning" or "offline"');

		// Fill in optional fields
		if (empty($data['maintenancedesc'])) $data['maintenancedesc'] = null;
		if (empty($data['visible'])) $data['visible'] = 1;
		$data['timeopened'] = (empty($data['timeopened'])) ? time() : strtotime($data['timeopened']);

		// If the incident is added with the status of closed, set the closed timestamp
		if ($data['status'] == 'closed') $data['timeclosed'] = (empty($data['timeclosed'])) ? time() : $data['timeclosed'];
		else if (!isset($data['timeclosed'])) $data['timeclosed'] = null;

		// If the incident is for the future, set the twitter message accordingly
		$twitter_prefix = ($data['timeopened'] > time()) ? 'Scheduled Maintenance:' : 'Incident Reported:';

		$sql = $this->db->prepare("INSERT INTO incidents (facilities_id, title, maintenancedesc, status, severity, timeopened, timeclosed, visible) VALUES (:facility, :title, :maintenancedesc, :status, :severity, datetime(:timeopened, 'unixepoch'), datetime(:timeclosed, 'unixepoch'), :visible)");
		$sql->bindValue(':facility', $data['facilities_id'], SQLITE3_INTEGER);
		$sql->bindValue(':title', $data['title'], SQLITE3_TEXT);
		$sql->bindValue(':maintenancedesc', $data['maintenancedesc'], SQLITE3_TEXT);
		$sql->bindValue(':status', $data['status'], SQLITE3_TEXT);
		$sql->bindValue(':severity', $data['severity'], SQLITE3_TEXT);
		$sql->bindValue(':timeopened', $data['timeopened'], SQLITE3_INTEGER);
		$sql->bindValue(':timeclosed', $data['timeclosed'], SQLITE3_INTEGER);
		$sql->bindValue(':visible', $data['visible'], SQLITE3_INTEGER);
		$insert = $sql->execute();

		if ($insert === false) throw new Exception('Unexpected error when attempting to add new incident');
		$newrow = $this->db->lastInsertRowID();

		if (!empty($data['update'])) {
			$firstupdate['incidents_id'] = $newrow;
			$firstupdate['message'] = $data['update'];
			$this->newUpdate($firstupdate);
		}

		if (!empty($data['twitter'])) {

			$twitter = new Twitter;
			$twitter->tweet("{$twitter_prefix} {$data['title']}");

			if (!empty($data['update'])) {
				$twitter->tweet($data['update']);
			}
		}

		return $newrow;
	}

	function updateService($services_id, $status) {
		if (empty($services_id)) throw new Exception('A service ID must be specified');
		if (empty($status)) throw new Exception('A severity must be specified');

		$sql = $this->db->prepare("UPDATE facilities_services SET status=:status WHERE id=:sid");
		$sql->bindValue(':sid', $services_id, SQLITE3_INTEGER);
		$sql->bindValue(':status', $status, SQLITE3_TEXT);
		$insert = $sql->execute();

		return $insert;
	}

	function updateIncident($data) {
		if (!is_array($data)) throw new Exception('Data must be an array');
		if (empty($data['incidents_id'])) throw new Exception('An incident ID must be specified');

		$sql = $this->db->prepare("SELECT * FROM incidents WHERE id=:iid LIMIT 1");
		$sql->bindValue(':iid', $data['incidents_id'], SQLITE3_INTEGER);
		$results = $sql->execute();

		if ($results === false) throw new Exception('Unable to locate incident to update');

		$incident = $results->fetchArray(SQLITE3_ASSOC);
		if (!isset($incident['timeopened'])) throw new Exception('Unable to locate incident to update (2)');

		foreach ($data as $key => $value) {
			if ($key == 'incidents_id') continue;

			$fieldtype = '';
			switch($key) {
				case 'title': $fieldtype = SQLITE3_TEXT; break;
				case 'maintenancedesc': $fieldtype = SQLITE3_TEXT; break;
				case 'status':
					if (!in_array($value, array('Investigating', 'Implementing Fix', 'Resolved'))) continue;

					if ($value == 'Resolved') {
						$sql = $this->db->prepare("UPDATE incidents SET timeclosed= datetime(:time, 'unixepoch') WHERE id=:iid");
						$sql->bindValue(':time', time(), SQLITE3_INTEGER);
						$sql->bindValue(':iid', $data['incidents_id'], SQLITE3_INTEGER);
						$insert = $sql->execute();
					}

					$fieldtype = SQLITE3_TEXT; break;
				case 'severity':
					if (!in_array($value, array('warning', 'offline'))) continue;

					$fieldtype = SQLITE3_TEXT; break;
				case 'timeopened': $fieldtype = SQLITE3_INTEGER; break;
				case 'timeclosed': $fieldtype = SQLITE3_INTEGER; break;
			}

			if (!empty($fieldtype)) {
				// Key previously validated by switch statement
				$sql = $this->db->prepare("UPDATE incidents SET {$key}=:value WHERE id=:iid");
				$sql->bindValue(':value', $value, $fieldtype);
				$sql->bindValue(':iid', $data['incidents_id'], SQLITE3_INTEGER);
				$insert = $sql->execute();

				
				if ($insert === false) throw new Exception('Unexpected error when attempting to update the incident');
			}
		}

		return true;
	}

	function newUpdate($data) {
		if (empty($data['incidents_id'])) throw new Exception('An incident ID must be specified');
		if (empty($data['message'])) throw new Exception('A message must be specified');

		if (empty($data['visible'])) $data['visible'] = 1;
		if (empty($data['twitterpost'])) $data['twitterpost'] = false;
		if (empty($data['timeadded'])) $data['timeadded'] = time();

		$sql = $this->db->prepare("SELECT * FROM incidents WHERE id=:iid LIMIT 1");
		$sql->bindValue(':iid', $data['incidents_id'], SQLITE3_INTEGER);

		$results = $sql->execute();
		if ($results === false) return new Exception('Database error attempting to locate incident');

		$incident = $results->fetchArray(SQLITE3_ASSOC);
		if (!isset($incident['timeopened'])) throw new Exception('Invalid Incident ID provided');

		$sql = $this->db->prepare("INSERT INTO incidents_updates (incidents_id, timeadded, message, visible) VALUES (:iid, datetime(:timeadded, 'unixepoch'), :message, :visible)");
		$sql->bindValue(':iid', $data['incidents_id'], SQLITE3_TEXT);
		$sql->bindValue(':timeadded', $data['timeadded'], SQLITE3_INTEGER);
		$sql->bindValue(':message', $data['message'], SQLITE3_TEXT);
		$sql->bindValue(':visible', $data['visible'], SQLITE3_INTEGER);
		$insert = $sql->execute();

		if ($data['twitterpost']) {

			$twitter = new Twitter;
			$twitter->tweet($data['message']);
		}

		if ($insert === false) throw new Exception('Unexpected error when attempting to add new incident update');
		return $this->db->lastInsertRowID();
	}

	function getIncidents($facilities_id, $days) {
		if (empty($facilities_id) || !is_numeric($facilities_id)) throw new Exception('A facility ID must be specified');
		if (empty($days) || !is_numeric($days)) throw new Exception('The amount of days must be specified and be numeric');

		$sql = $this->db->prepare("SELECT *, strftime('%s', timeopened) as timeopened, strftime('%s', timeclosed) as timeclosed FROM incidents WHERE facilities_id=:fid AND date(timeopened) >= date('now', :days) AND timeopened < datetime('now') ORDER BY timeopened DESC");
		$sql->bindValue(':days', ($days * -1) . ' day', SQLITE3_TEXT);
		$sql->bindValue(':fid', $facilities_id, SQLITE3_INTEGER);
		$results = $sql->execute();

		$incidents = array();
		while ($entry = $results->fetchArray(SQLITE3_ASSOC)) {
			$date = date("F j, Y", $entry['timeopened']);

			if ($date == date("F j, Y", time())) $date = 'Today';
			else if ($date == date("F j, Y", strtotime("yesterday"))) $date = 'Yesterday';

			$entry['updates'] = $this->getUpdates($entry['id']);

			$incidents[$date][] = $entry;
		}

		return $incidents;
	}

	function getScheduled($facilities_id) {
		if (empty($facilities_id) || !is_numeric($facilities_id)) throw new Exception('A facility ID must be specified');

		$sql = $this->db->prepare("SELECT *, strftime('%s', timeopened) as timeopened, strftime('%s', timeclosed) as timeclosed FROM incidents WHERE facilities_id=:fid AND timeopened > datetime('now') ORDER BY timeopened ASC");
		$sql->bindValue(':fid', $facilities_id, SQLITE3_INTEGER);
		$results = $sql->execute();

		$incidents = array();
		while ($entry = $results->fetchArray(SQLITE3_ASSOC)) {
			$date = date("F j, Y", $entry['timeopened']);

			if ($date == date("F j, Y", time())) $date = 'Today';
			else if ($date == date("F j, Y", strtotime("tomorrow"))) $date = 'Tomorrow';

			$incidents[$date][] = $entry;
		}

		return $incidents;
	}

	function getServices($facilities_id) {
		if (empty($facilities_id) || !is_numeric($facilities_id)) throw new Exception('A facility ID must be specified');

		$sql = $this->db->prepare("SELECT * FROM facilities_services WHERE facilities_id=:fid ORDER BY `friendly_name` ASC");
		$sql->bindValue(':fid', $facilities_id, SQLITE3_INTEGER);
		$results = $sql->execute();

		$services = array();
		while ($entry = $results->fetchArray(SQLITE3_ASSOC)) {
			$services[] = $entry;
		}
		return $services;
	}

	function getUpdates($incidents_id) {
		$sql = $this->db->prepare("SELECT *, strftime('%s', timeadded) as timeadded FROM incidents_updates WHERE incidents_id=:iid ORDER BY timeadded ASC");
		$sql->bindValue(':iid', $incidents_id, SQLITE3_INTEGER);
		$results = $sql->execute();

		$updates = array();
		while ($entry = $results->fetchArray(SQLITE3_ASSOC)) {
			$updates[] = $entry;
		}

		return $updates;
	}

	function getSummary($facilities_id, $days, $shortdate = false) {
		if (empty($facilities_id) || !is_numeric($days)) throw new Exception('A facility ID must be specified');
		if (empty($days) || !is_numeric($days)) throw new Exception('The amount of days must be specified and be numeric');

		$incidents = $this->getIncidents($facilities_id, $days);

		$dateformat = "F j, Y";
		$shortdateformat = "m/d";

		for ($i=0; $i < $days; $i++) {
			$date = date($dateformat, strtotime("-{$i} days"));
			$shortdate = date($shortdateformat, strtotime("-{$i} days"));
			if ($date == date($dateformat, time())) $date = $shortdate = 'Today';
			if ($date == date($dateformat, strtotime("yesterday"))) $date = $shortdate = 'Yesterday';

			if ($shortdate) { $displaydate = $shortdate; }
				else { $displaydate = $date; }

			if (isset($incidents[$date])) {
				$warning = 0; $offline = 0;

				foreach ($incidents[$date] as $event) {
					switch ($event['severity']) {
						case 'warning': $warning++; break;
						case 'offline': $offline++; break;
					}
				}

				if ($offline > 0) {
					$summary[$displaydate] = 'offline';
				}else{
					$summary[$displaydate] = 'warning';
				}
			}else{
				$summary[$displaydate] = 'online';
			}
		}

		return $summary;
	}

}

?>