<?php
require_once('../includes/base.inc.php');
require_once('../includes/status.class.php');

	if (!empty($_SESSION['auth']['id'])) {
		if (isset($_POST)) {
			if (isset($_POST['id']) && isset($_POST['value'])) {
				$key = explode('-', $_POST['id']);

				switch($key[0]) {
					case 'changestatus':
						$status = new Status;
						$update = array('incidents_id' => $key[1], 'status' => $_POST['value']);
						$status->updateIncident($update);
						die($_POST['value']);
						break;
					case 'changeseverity':
						$status = new Status;
						$update = array('incidents_id' => $key[1], 'severity' => $_POST['value']);
						$status->updateIncident($update);
						die('<img src="templates/default/images/ico_'.$_POST['value'].'_small.gif" alt="'.$_POST['value'].'" />');
						break;
					case 'changeservice':
						$status = new Status;
						$status->updateService($key[1], $_POST['value']);
						die('<img src="templates/default/images/ico_'.$_POST['value'].'_large.gif" alt="'.$_POST['value'].'" />');
						break;
					case 'changetitle':
						$status = new Status;
						$update = array('incidents_id' => $key[1], 'title' => $_POST['value']);
						$status->updateIncident($update);
						die($_POST['value']);
						break;
				}
				
			}
		}
	}
?>
