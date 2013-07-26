<?php
	require_once('../includes/base.inc.php');
	include('../includes/authentication.class.php');
	$auth = new Authentication;

	$auth->logout();
	header('Location: index.php');
?>
