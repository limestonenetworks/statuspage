<?php

include('base.inc.php');

$status = new Status;

#
# Adding an incident
#
$data['facilities_id'] = 1;
$data['title'] = 'Slightly different issue causing downtime.';
$data['status'] = 'Resolved';
$data['severity'] = 'offline';
$data['timeopened'] = strtotime('-1 days');
$data['infolink'] = null;

//$result = $status->newIncident($data);
//print_r($result);


#
# Adding an incident update
#

$data = array();
$data['incidents_id'] = 1;
//$data['message'] = 'We have identified the issue and are currently working on repairing it.';
$data['message'] = 'Fixed the problem';
$data['timeadded'] = strtotime("-2 days");

//$result = $status->newUpdate($data);
//print_r($result);


#
# Update incident
#
$data = array();
$data['incidents_id'] = 2;
$data['status'] = 'Resolved';
//$data['severity'] = 'warning';

//$status->updateIncident($data);


#
# Getting incidents
#

$result = $status->getIncidents(1, 5);
//$result = $status->getSummary(1, 5, true);
print_r($result);


echo 'Hello World';

?>
