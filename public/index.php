<?php
	require_once('../includes/base.inc.php');

	if (!empty($_SESSION['auth']['id'])) {
		if (!empty($_POST)) {
			if (isset($_POST['incidentid'], $_POST['update'])) {
				$status = new Status;

				$data['incidents_id'] = $_POST['incidentid'];
				$data['message'] = $_POST['update'];
				if (!empty($_POST['twitter'])) $data['twitterpost'] = true;
				$status->newUpdate($data);
			}
			if (isset($_POST['severity'], $_POST['status'], $_POST['title'], $_POST['update'], $_POST['timeopened'])) {
				$status = new Status;

				$data['severity'] = $_POST['severity'];
				$data['status'] = $_POST['status'];
				$data['title'] = $_POST['title'];
				$data['facilities_id'] = $_POST['facilities_id'];
				$data['timeopened'] = $_POST['timeopened'];
				if (!empty($_POST['update'])) $data['update'] = $_POST['update'];
				if (!empty($_POST['maintenancedesc'])) $data['maintenancedesc'] = $_POST['maintenancedesc'];
				if (!empty($_POST['twitter'])) $data['twitter'] = $_POST['twitter'];
				$status->newIncident($data);
			}

			
			header('Location: index.php');
		}
	}

	$facilitiesclass = new Facilities;
	$facilities = $facilitiesclass->getFacilities();
	if (empty($facilities)) throw new Exception('Unexpected Error. No facilities in database.');

	foreach ($facilities as $key => $value) {
		$status = new Status;
		$facilities[$key]['incidents'] = $status->getIncidents($value['id'], $config['days_to_report']);
		$facilities[$key]['summary'] = $status->getSummary($value['id'], $config['days_to_report'], true);
		$facilities[$key]['services'] = $status->getServices($value['id']);
		$facilities[$key]['scheduled'] = $status->getScheduled($value['id']);
	}

	$smarty->assign('facilities', $facilities);

	$smarty->assign('report_days', $config['days_to_report']);
	$smarty->assign('twitter_handle', $config['twitter_handle']);

	$smarty->display('_header.tpl');
	$smarty->display('index.tpl');
	$smarty->display('_footer.tpl');
?>
