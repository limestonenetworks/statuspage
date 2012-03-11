$(document).ready(function() {
	$("#addincident").dialog({
		autoOpen: false,
		height: 480,
		width: 350,
		modal: true,
		title: "Report an Incident",
		buttons: {
			"Add Incident": function() {
				$("#addincidentform").submit();
			},
			"Cancel": function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#severity,#status,#title,#initialupdate").val("").removeClass("ui-state-error");
		}
	}); 

	$('#reportincident').click(function(){
		$("#addincident").dialog("open");
		return false;
	});

	$("#timeopened").datetimepicker({ ampm: true, timeFormat: 'hh:mm tt', numberOfMonths: 2,  });

	$("#timeopened").change(function() {
		var d = new Date($("#timeopened").val());
		var scheduled = (d.getTime() * 0.001)|0;
		var currenttime = (new Date().valueOf() * 0.001)|0;

		if (scheduled > currenttime) {
			$("#maintfields").slideDown();
			$("#hidemaintenance").slideUp();
		}else{
			$("#maintfields").slideUp();
			$("#hidemaintenance").slideDown();
		}
	});

	$("#autorefreshbox").change(function(){
		if ($("#autorefreshbox").is(":checked")) {
			setRefresh();
		}else{
			clearRefresh();
		}
	});

});

function setRefresh() {
	var count = 60;
	$("#autorefreshbox").prop("checked", true);
	$("#refreshlabel").html('Refreshing in ' + count + ' seconds');
	countdown = setInterval(function(){
		count--;
		$("#refreshlabel").html('Refreshing in ' + count + ' seconds');
		if (count == 0) window.location = '/?refresh=true';
	}, 1000);
}

function clearRefresh() {
	$("#autorefreshbox").prop("checked", false);
	clearInterval(countdown);
	$("#refreshlabel").html('Auto Refresh');
}
