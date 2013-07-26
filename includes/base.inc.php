<?php

include('config.php');

require_once('../vendor/autoload.php');

session_start();

require_once('database.class.php');
$db_conn = new Database($config['db_path']);
$db = $db_conn->sqlite;

require_once('template.inc.php');
require_once('status.class.php');
require_once('facilities.class.php');

?>
