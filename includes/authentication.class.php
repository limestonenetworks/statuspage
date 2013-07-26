<?php

class Authentication {
	private $db;

	function __construct() {
		global $db;

		if (empty($db)) throw new Exception('Unable to contact database');

		$this->db = $db;
	}

	function changePassword($username, $oldpw, $newpw) {
		if (empty($username)) throw new Exception('A username must be specified');
		if (empty($oldpw) || empty($newpw)) throw new Exception('Old and New passwords cannot be empty');
		
		$user = $this->testCredentials($username, $oldpw);
		if (!isset($user['id'])) throw new Exception('Invalid original password');

		$passhash = md5($newpw);

		$sql = $this->db->prepare('UPDATE users SET password=:pass WHERE username=:username');
		$sql->bindValue(':pass', $passhash, SQLITE3_TEXT);
		$sql->bindValue(':username', $username, SQLITE3_TEXT);
		
		if ($sql->execute()) return true;
		return false;
	}

	function login($user, $pass) {
		global $_SESSION;

		if (empty($user) || empty($pass)) throw new Exception('Username and Password cannot be empty');

		$user = $this->testCredentials($user, $pass);

		if (!empty($user['id'])) {
			$_SESSION['auth']['id'] = $user['id'];
			$_SESSION['auth']['user'] = $user['username'];
			return true;
		}else{
			return false;
		}
	}

	private function testCredentials($user, $pass) {
		if (empty($user) || empty($pass)) throw new Exception('Username and Password cannot be empty');

		$passhash = md5($pass);

		$sql = $this->db->prepare('SELECT * FROM users WHERE username=:username AND password=:password');
		$sql->bindValue(':username', $user, SQLITE3_TEXT);
		$sql->bindValue(':password', $passhash, SQLITE3_TEXT);
		$select = $sql->execute();

		$user = $select->fetchArray(SQLITE3_ASSOC);

		if (empty($user['id'])) return false;
		return $user;
	}

	function logout() {
		global $_SESSION;
		$_SESSION = array();
		return true;
	}
}