<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
	<head>
		<title>{$pagetitle}</title> 
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
		<meta name="description" content="This site provides updates for Limestone Networks' network. Limestone Networks is a dedicated hosting provider in Dallas, TX offering dedicated servers." /> 
		<meta name="keywords" content="dedicated server, dedicated server hosting, status" /> 
		<meta name="robots" content="all,index,follow" /> 
		<meta name="revisit-after" content="7 days" /> 
		<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />

		<link type="text/css" rel="stylesheet" media="all" href="templates/default/css/smoothness/jquery-ui-1.8.18.custom.css" />	
		<link type="text/css" rel="stylesheet" media="all" href="templates/default/css/style.css" />
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>{if $smarty.session.auth.id}
		<script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>{/if}
		<script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="templates/default/js/template.js"></script>
	</head>

	<body>
		<div id="header">
			<div class="wrapper">
				{if file_exists('templates/default/images/logo.gif')}
				<h1><a href="index.php"><img src="templates/default/images/logo.gif" alt="{$pagetitle}" /></a></h1>
				{else}
				<h1>Your logo here</h1>
				{/if}
				<div id="title">
					<h2>Network Status</h2>
					<div id="lastupdate">as of {$smarty.now|date_format:"%I:%M %p"} on {$smarty.now|date_format:"%D"}</div>
					<div id="autorefresh"><label for="autorefreshbox" id="refreshlabel">Auto Refresh</label><input type="checkbox" id="autorefreshbox" /></div>
				</div>
			</div>
		</div>

		<div class="wrapper">
			{if $smarty.session.auth.id}
			<div id="admincontrols">
				<p><strong>Welcome, {$smarty.session.auth.user}.</strong></p>
				<ul>
					<li><a href="#" id="reportincident">Report Incident</a></li>
					<li><a href="users.php">Users</a></li>
					<li><a href="logout.php">Logout</a></li>
				</ul>
			</div>
			{/if}
		</div>

