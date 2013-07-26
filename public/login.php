<?php
	require_once('../includes/base.inc.php');
	include('../includes/authentication.class.php');
	$auth = new Authentication;

	if (isset($_POST['username'], $_POST['password'])) {
		$login = $auth->login($_POST['username'], $_POST['password']);

		if ($login) header('Location: index.php');
		$smarty->assign('error', 'Invalid username or password');
	}

	$smarty->display('_header.tpl');
	$smarty->display('login.tpl');
	$smarty->display('_footer.tpl');
?>
