	<div class="wrapper">
		<div id="content">
			{foreach from=$facilities item=facility}
			<div class="datacenter">
				<div class="dchead">
					<h3>{$facility.friendly_name}</h3>
					<a href="https://twitter.com/{$twitter_handle}" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @{$twitter_handle}</a>
				</div>

				<table class="services">
					<tr>{foreach from=$facility.services item=service}
						<td><span id="changeservice-{$service.id}"><img src="templates/default/images/ico_{$service.status}_large.gif" alt="{$service.status}" /></span> {$service.friendly_name}</td>{/foreach}
					</tr>
				</table>

				<table class="perday">
					<tr>
						{foreach from=$facility.summary key=day item=status}<th>{$day}</th>{/foreach}
					</tr>
					<tr>
						{foreach from=$facility.summary key=day item=status}<td><img src="templates/default/images/ico_{$status}_small.gif" alt="{$status}" /></td>{/foreach}
					</tr>
				</table>

				<div class="rightstuff">
					<div class="uhHuh">
						<div class="title">Scheduled Maintenance</div>
						{if $facility.scheduled|@count > 0}
						<ul class="maintschedule">
							{foreach from=$facility.scheduled key=date item=events}<li>{$date}
								<ul>
									{foreach from=$events item=event}<li>
										<span class="time">{$event.timeopened|date_format:"%I:%M %p %Z"}</span>
										{$event.title|truncate:26:'...':true}{if $event.maintenancedesc}
										<span class="readmore"><a href="#" id="readmore-{$event.id}">Read More</a></span>{/if}
									{/foreach}</li>
								</ul>
							</li>
							{/foreach}
						{else}<p>There is no maintenance scheduled at this time.</p>{/if}
					</div>
					{if isset($textarea)}
					<div class="uhHuh">
						<div class="title">{$textarea.heading}</div>
						<p>{$textarea.text}</p>
					</div>
					{/if}
				</div>

				<div class="log">


					{foreach from=$facility.incidents key=day item=incidents}
					<h4>{$day}</h4>
					
					{foreach from=$incidents item=incident}
					<div class="incident severity-{$incident.severity}">
						<div class="incidentheader">
							<span id="changeseverity-{$incident.id}"><img src="templates/default/images/ico_{$incident.severity}_small.gif" alt="{$incident.severity}" /></span>
							<span class="title" id="changetitle-{$incident.id}">{$incident.title}</span>
							<div class="status"><strong>Status:</strong> <span id="changestatus-{$incident.id}">{$incident.status}</span></div>
						</div>
						
						<div class="updates">
							{if $incident.updates|@count > 0}{foreach from=$incident.updates item=update}
							<div class="update">
								<span class="timestamp">{$update.timeadded|date_format:"%I:%M %p %Z"}</span>
								<span class="message">{$update.message}</span>
							</div>
							{/foreach}{/if}
							{if $smarty.session.auth.id}<p><form action="" method="post" class="microupdate"><input type="text" name="update" class="updatebox" id="updateto{$incident.id}" /><p class="posttotwitter"><input type="checkbox" name="twitter" class="micrototwitter" checked="CHECKED" /> Post to Twitter</p><input type="hidden" name="incidentid" value="{$incident.id}"></form>{/if}
						</div>
					</div>
					{/foreach}
					{foreachelse}
						<p>No recent incidents were found.</p>
					{/foreach}
				</div>
			</div>
			{/foreach}
		</div>
	</div>

{if $smarty.session.auth.id}
	<div id="addincident" class="ui-widget">
		<form action="" method="post" id="addincidentform">
		<fieldset>
				<label for="facilities_id">Facility:</label>
				<select name="facilities_id" id="ni_facilities_id">
					{foreach from=$facilities item=facility}<option value="{$facility.id}">{$facility.friendly_name}</option>{/foreach}
				</select>
				<label for="timeopened">Date/Time of Incident:</label>
				<input type="text" name="timeopened" id="timeopened" value="{$smarty.now|date_format:"%m/%d/%Y %I:%M %p"}" />
				<label for="title">Short Title:</label>
				<input type="text" name="title" id="title" />
				<div id="maintfields">
					<label for="maintenancedesc">Full Description of Maintenance:</label>
					<textarea id="maintenancedesc" name="maintenancedesc"></textarea>
				</div>
				<label for="severity">Severity:</label>
				<select name="severity" id="severity">
					<option>warning</option>
					<option>offline</option>
				</select>
				<label for="status">Status:</label>
				<select name="status" id="status">
					<option>Investigating</option>
					<option>Implementing Fix</option>
					<option>Resolved</option>
				</select>
				<div id="hidemaintenance">
					<label for="initialupdate">Initial Update:</label>
					<input type="text" name="update" id="initialupdate" />
				</div>
				<label for="incidenttwitter">Send to Twitter:</label>
				<input type="checkbox" name="twitter" id="incidenttwitter" checked="CHECKED" />
		</fieldset>
		</form>
	</div>



{foreach from=$facilities item=facility}{foreach from=$facility.incidents key=day item=incidents}{foreach from=$incidents item=incident}
	<script type="text/javascript">
	$("#changestatus-{$incident.id}").editable('save.php', {literal}{
		data	: " {'Investigating':'Investigating','Implementing Fix':'Implementing Fix','Resolved':'Resolved', 'selected':'{/literal}{$incident.status}{literal}'}",
		type	: 'select',
		submit	: 'OK',
		style	: 'display:inline' }{/literal});
	$("#changeseverity-{$incident.id}").editable('save.php', {literal}{
		data	: " {'warning':'Warning','offline':'Offline', 'selected':'{/literal}{$incident.severity}{literal}'}",
		type	: 'select',
		submit	: 'OK',
		style	: 'display:inline' }{/literal});
	$("#changetitle-{$incident.id}").editable('save.php', {literal}{ width: 250, style: 'display:inline' }{/literal});
	</script>

	<div id="editincident{$incident.id}" class="edit-incident">
		<form action="" method="post">
			
		</form>
	</div>
	{/foreach}{/foreach}
{foreach from=$facility.services key=day item=service}
	<script type="text/javascript">
	$("#changeservice-{$service.id}").editable('save.php', {literal}{
		data	: " {'online':'Online','warning':'Warning','offline':'Offline', 'selected':'{/literal}{$service.severity}{literal}'}",
		type	: 'select',
		submit	: 'OK',
		style	: 'display:inline' }{/literal});
	</script>
{/foreach}
{/foreach}
{/if}

<div id="readmorebox"></div>
{foreach from=$facilities item=facility}{if $facility.scheduled|@count > 0}
	<script type="text/javascript">
		{literal}$("#readmorebox").dialog({ autoOpen: false, title: 'Maintenance Description', width: 500, height: 300 });{/literal}
{foreach from=$facility.scheduled key=day item=events}{foreach from=$events item=event}
		$("#readmore-{$event.id}").click(function(){literal}{{/literal}
			$("#readmorebox").dialog('close');
			$("#readmorebox").html('<p>{$event.timeopened|date_format:"%m/%d/%Y %I:%M %p %Z"}<br /><strong>{$event.title|addslashes}</strong></p></p><p>{$event.maintenancedesc|htmlspecialchars|nl2br|replace:"\n":''|replace:"\r":''|addslashes|trim}</p>');
			$("#readmorebox").dialog('open');
			return false;
		{literal}}{/literal});
{/foreach}{/foreach}
	</script>
{/if}
{/foreach}
