<?php 
	date_default_timezone_set('Europe/London'); 
	
	include("system-header.php"); 
	require_once("datafilter.php"); 
?>

<!--  Start of content -->
<link rel="stylesheet" href="css/fullcalendar.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/fullcalendar.print.css" type="text/css" media="all" />

<script type="text/javascript" src="js/gcal.js"></script>
<script type="text/javascript" src="js/fullcalendar.js"></script>

<script>
	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			editable: true,
			aspectRatio: 2.1,
			allDayDefault: false, 
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},

			eventRender: function(event, element) {
			   element.attr('title', "Clickx to view " + event.title);
			},
			
			eventClick: function(calEvent, jsEvent, view) {

				window.location.href = "viewabsences.php?id=" + calEvent.id;
		    },
			
		    dayClick: function(date, allDay, jsEvent, view) {
		
		        // change the day's background color just for fun
		
		    },
			
			events: [
				<?php
					$result = mysql_query(
									
							(	
									"SELECT A.id, " .
									"DATE_FORMAT(A.startdate, '%Y') AS startyear, " .
									"DATE_FORMAT(A.startdate, '%c') AS startmonth, " .
									"DATE_FORMAT(A.startdate, '%e') AS startday, " .
									"DATE_FORMAT(A.startdate, '%H:%m:%S') AS starttime, " .
									"DATE_FORMAT(A.enddate, '%Y') AS endyear, " .
									"DATE_FORMAT(A.enddate, '%c') AS endmonth, " .
									"DATE_FORMAT(A.enddate, '%e') AS endday, " .
									"DATE_FORMAT(A.enddate, '%H:%m:%S') AS endtime, " .
									"A.acceptedby, B.login " .
									"FROM {$_SESSION['DB_PREFIX']}absence A " .
									"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
									"ON B.member_id = A.memberid " .
									"WHERE A.acceptedby IS NOT NULL " .
									"ORDER BY A.startdate"
								)
						);
						
					if ($result) {
						$counter = 0;
						
						/* Show children. */
						while (($member = mysql_fetch_assoc($result))) {
							if ($counter++ > 0) {
								echo ",\n";
							}
							
							$startHour = substr($member['starttime'], 0, 2 );
							$startMinute = substr($member['starttime'], 3, 2 );
							
							$endHour = substr($member['endtime'], 0, 2 );
							$endMinute = substr($member['endtime'], 3, 2 );
														
							echo "{\n";
							echo "id:" . $member['id'] . ",\n";
							
							if ($member['acceptedby'] == null) {
								echo "title: '" . $member['login'] . " (Pending)',\n";
								echo "className: 'eventColor1',\n";

							} else {
								echo "title: '" . $member['login'] . " (Accepted)',\n";
								echo "className: 'eventColor2',\n";
							}
							
							echo "allDay: true,\n";
							echo "start: new Date(" . $member['startyear'] . ", " . ($member['startmonth'] - 1) . ", " . $member['startday'] . ", $startHour, $startMinute),\n";
							echo "end: new Date(" . $member['endyear'] . ", " . ($member['endmonth'] - 1) . ", " . $member['endday'] . ", $endHour, $endMinute)\n";
							echo "}\n";
						}
						
					} else {
						logError("Error:" + mysql_error());
					}
					
				?>
			]
		});
		
	});
	
	
	
</script>
<div id='calendar'></div>

<!--  End of content -->
<?php include("system-footer.php"); ?>