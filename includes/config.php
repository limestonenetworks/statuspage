<?php
$config['app_path'] = dirname(dirname(__FILE__));
$config['days_to_report'] = 10; //how many days to look backward

$config['db_path'] = $config['app_path'].'/cache/status.db'; //database location, must be writable by webserver
$config['cache_path'] = $config['app_path'].'/cache'; //where smarty will compile and cache its templates, must be writable by webserver
$config['template_name'] = 'default';

$config['twitter_handle'] = 'StatusTwitter'; //this is the twitter account username to reference
$config['twitter_key'] = '';
$config['twitter_secret'] = '';
$config['twitter_oauth_token'] = '';
$config['twitter_oauth_token_secret'] = '';

$config['default_services'] = array('Network', 'One Portal', 'Filtering System', 'Name Servers', 'Storage Servers'); //only used during database initialization
$config['default_facilities'] = array('Dallas, TX Data Center'); //only used during database initialization

$config['pagetitle'] = 'Network Status';
$config['footer_links'] = array( //this is optional, delete this to get rid of all footer links
	array('title' => 'Dedicated Servers', 'url' => 'http://www.example.com'),
	array('title' => 'Resell Dedicated Servers', 'url' => 'http://www.example.com/partners/resellers.html'),
	array('title' => 'Contact Us', 'url' => 'http://www.example.com/home/contactus.html')
);
$config['textarea'] = array(
	'heading' => 'Get Support',
	'text' => 'Having service issues? In the event that our ticketing system is offline we will monitor emails sent to <a href="mailto:support@example.com">support@example.com</a>. Please be advised that unless our ticketing system is inaccessable this mail box is not actively monitored and you should <a href="https://example.com/support/newticket.html" target="_blank">open a ticket</a> for support.'
);
$config['smarty_debug'] = false;