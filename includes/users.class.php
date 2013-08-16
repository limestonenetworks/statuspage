<?php

class Users {
	private $db;

	function __construct() {
		global $db;

		if (empty($db)) throw new Exception('Unable to contact database');

		$this->db = $db;
	}

	function getUsers() {
		$users_query = $this->db->query("SELECT * FROM users ORDER BY username ASC");

		$row = array();

		while($res = $users_query->fetchArray(SQLITE3_ASSOC)){ 
			$row[$res['id']] = $res;
		}

		return $row;
	}

	function createUser($username, $password) {
		if (empty($username)) throw new Exception('A username must be specified');
		if (empty($password)) throw new Exception('A password must be specified');

		if ($this->userExistsByUsername($username) == true) throw new Exception('That username already exists');
		$password = md5($password);

		$sql = $this->db->prepare("INSERT INTO users (username, password) VALUES (:uname, :pword)");
		$sql->bindValue(':uname', $username, SQLITE3_TEXT);
		$sql->bindValue(':pword', $password, SQLITE3_TEXT);
		$results = $sql->execute();

		if ($results) return true;
		return false;
	}

	function deleteUser($id) {
		global $_SESSION;
		if (empty($id) || !is_numeric($id)) throw new Exception('A user ID must be specified');
		if ($this->userExistsById($id) == false) throw new Exception('That user does not exist');
		if ($id == $_SESSION['auth']['id']) throw new Exception('You cannot delete your own user');

		$sql = $this->db->prepare("DELETE FROM users WHERE id=:uid");
		$sql->bindValue(':uid', $id, SQLITE3_INTEGER);
		$results = $sql->execute();

		if ($results) return true;
		return false;
	}

	private function userExistsById($id) {
		if (empty($id) || !is_numeric($id)) throw new Exception('A user ID must be specified');

		$sql = $this->db->prepare("SELECT * FROM users WHERE id=:uid");
		$sql->bindValue(':uid', $id, SQLITE3_INTEGER);
		$results = $sql->execute();
		$users = $results->fetchArray(SQLITE3_ASSOC);

		$result = (empty($users)) ? false : true;
		return $result;
	}

	private function userExistsByUsername($name) {
		if (empty($name)) throw new Exception('A username must be specified');

		$sql = $this->db->prepare("SELECT * FROM users WHERE username=:uname");
		$sql->bindValue(':uname', $name, SQLITE3_TEXT);
		$results = $sql->execute();
		$users = $results->fetchArray(SQLITE3_ASSOC);

		$result = (empty($users)) ? false : true;
		return $result;
	}
}