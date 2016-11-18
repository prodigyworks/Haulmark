<?php 
	include("system-header.php"); 
?>

<!--  Start of content -->
<link rel="stylesheet" href="css/fullcalendar.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/fullcalendar.print.css" type="text/css" media="all" />

<script type="text/javascript" src="js/gcal.js"></script>
<script type="text/javascript" src="js/fullcalendar.js"></script>
<style>
	.eventcolor_S .fc-event-inner {
		background: linear-gradient(to bottom, #b7deed 0%,#71ceef 50%,#21b4e2 51%,#b7deed 100%) ! important;
		color: black ! important;
		border: 1px solid grey ! important;
	}
	.eventcolor_A .fc-event-inner {
		background: linear-gradient(to bottom, #fceabb 0%,#fccd4d 50%,#f8b500 51%,#fbdf93 100%) ! important;
		color: black ! important;
		border: 1px solid grey ! important;
	}
	.eventcolor_I .fc-event-inner {
		background: linear-gradient(to bottom, #ffb76b 0%,#ffa73d 50%,#ff7c00 51%,#ff7f04 100%) ! important;
		color: black ! important;
		border: 1px solid grey ! important;
	}
	.eventcolor_C .fc-event-inner {
		background: linear-gradient(to bottom, #bfd255 0%,#8eb92a 50%,#72aa00 51%,#9ecb2d 100%) ! important;
		color: black ! important;
		border: 1px solid grey ! important;
	}
	#calendarcontainer {
		overflow: auto;
	}	
</style>
<div class="modal" id="vehicle_editdialog">
	<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass" id="vehicle_edittable">
		<tbody>
			<tr>
				<td>Vehicle</td>
				<td>
					<?php createCombo("vehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "WHERE active = 'Y'"); ?>
				</td>
			</tr>
			<tr>
				<td>Start Date / Time</td>
				<td>
					<input required="true" type="text" id="startdate" name="startdate" size=8> 
					<input required="true" type="text" id="startdate_time" name="startdate_time" size=5>
				</td>
			</tr>
			<tr>
				<td>End Date / Time</td>
				<td>
					<input required="true" type="text" id="enddate" name="enddate" size=8> 
					<input required="true" type="text" id="enddate_time" name="enddate_time" size=5>
				</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					<select id="status" name="status">
						<option value=""></option>
						<option value="S">Scheduled</option>
						<option value="I">In Progress</option>
						<option value="A">Awaiting Order Number</option>
						<option value="C">Complete</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Order Number</td>
				<td>
					<input type="text" id="ordernumber" name="ordernumber" size="20"> 
				</td>
			</tr>
			<tr>
				<td>Invoice Number</td>
				<td>
					<input type="text" id="invoicenumber" name="invoicenumber" size="20"> 
				</td>
			</tr>
			<tr>
				<td>Work Carried Out</td>
				<td>
					<textarea style="width: 600px" rows="6" cols="80" id="workcarriedout" name="workcarriedout"></textarea>
				</td>
			</tr>
			<tr>
				<td>Total Cost</td>
				<td>
					<input type="number" pattern="[0-9]+([,\.][0-9]+)?" title="The number input must start with a number and use either comma or a dot as a decimal character." style="width: 72px" id="totalcost" name="totalcost"> 
				</td>
			</tr>
			<tr>
				<td>Reason</td>
				<td>
					<?php createCombo("reasonid", "id", "name", "{$_SESSION['DB_PREFIX']}vehicleunavailabilityreasons"); ?>
				</td>
			</tr>
			<tr class="hidden defectrow" style="display: table-row;">
				<td>Defect Number</td>
				<td>
					<input type="text" id="defectnumber" name="defectnumber" size="7" required=""> 
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="modal" id="trailer_editdialog">
	<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass" id="trailer_edittable">
		<tbody>
			<tr>
				<td>Trailer</td>
				<td>
					<?php createCombo("trailerid", "id", "registration", "{$_SESSION['DB_PREFIX']}trailer", "WHERE active = 'Y'"); ?>
				</td>
			</tr>
			<tr>
				<td>Start Date / Time</td>
				<td>
					<input required="true" type="text" id="startdate" name="startdate" size=8> 
					<input required="true" type="text" id="startdate_time" name="startdate_time" size=5>
				</td>
			</tr>
			<tr>
				<td>End Date / Time</td>
				<td>
					<input required="true" type="text" id="enddate" name="enddate" size=8> 
					<input required="true" type="text" id="enddate_time" name="enddate_time" size=5>
				</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					<select id="status" name="status">
						<option value=""></option>
						<option value="S">Scheduled</option>
						<option value="I">In Progress</option>
						<option value="A">Awaiting Order Number</option>
						<option value="C">Complete</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Order Number</td>
				<td>
					<input type="text" id="ordernumber" name="ordernumber" size="20"> 
				</td>
			</tr>
			<tr>
				<td>Invoice Number</td>
				<td>
					<input type="text" id="invoicenumber" name="invoicenumber" size="20"> 
				</td>
			</tr>
			<tr>
				<td>Work Carried Out</td>
				<td>
					<textarea style="width: 600px" rows="6" cols="80" id="workcarriedout" name="workcarriedout"></textarea>
				</td>
			</tr>
			<tr>
				<td>Total Cost</td>
				<td>
					<input type="number" pattern="[0-9]+([,\.][0-9]+)?" title="The number input must start with a number and use either comma or a dot as a decimal character." style="width: 72px" id="totalcost" name="totalcost"> 
				</td>
			</tr>
			<tr>
				<td>Reason</td>
				<td>
					<?php createCombo("reasonid", "id", "name", "{$_SESSION['DB_PREFIX']}trailerunavailabilityreasons"); ?>
				</td>
			</tr>
			<tr class="hidden defectrow" style="display: table-row;">
				<td>Defect Number</td>
				<td>
					<input type="text" id="defectnumber" name="defectnumber" size="7" required=""> 
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script>
	$(document).ready(function() {
		$("#vehicle_editdialog").dialog({
				modal: true,
				autoOpen: false,
				width: "auto",
				title: "Vehicle Unavailability",
				buttons: {
					"Close": function() {
						$(this).dialog("close");
					}
				}
			});

		$("#trailer_editdialog").dialog({
				modal: true,
				autoOpen: false,
				width: "auto",
				title: "Trailer Unavailability",
				buttons: {
					"Close": function() {
						$(this).dialog("close");
					}
				}
			});

		$("#calendarcontainer").css("height", ($("body").attr("offsetHeight") - 200));
	
		$('#calendar').fullCalendar({
			editable: true,
			aspectRatio: 2.1,
			allDayDefault: false, 
			height: ($("body").attr("offsetHeight") - 200),
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			
			eventClick: function(calEvent, jsEvent, view) {
				if (calEvent.type == "V") {
					callAjax(
							"finddata.php", 
							{ 
								sql: "SELECT A.*, " + 
									 "DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate2, " + 
									 "DATE_FORMAT(A.startdate, '%H:%I') AS starttime2, " + 
									 "DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate2, " + 
									 "DATE_FORMAT(A.enddate, '%H:%I') AS endtime2 " + 
									 "FROM <?php echo $_SESSION['DB_PREFIX'];?>vehicleunavailability A " +
									 "WHERE A.id = " + calEvent.id
							},
							function(data) {
								if (data.length > 0) {
									var node = data[0];
									
									$("#vehicle_edittable #vehicleid").val(node.vehicleid);
									$("#vehicle_edittable #startdate").val(node.startdate2);
									$("#vehicle_edittable #startdate_time").val(node.starttime2);
									$("#vehicle_edittable #enddate").val(node.enddate2);
									$("#vehicle_edittable #enddate_time").val(node.endtime2);
									$("#vehicle_edittable #reasonid").val(node.reasonid);
									$("#vehicle_edittable #workcarriedout").val(node.workcarriedout);
									$("#vehicle_edittable #totalcost").val(node.totalcost);
									$("#vehicle_edittable #status").val(node.status);
									$("#vehicle_edittable #ordernumber").val(node.ordernumber);
									$("#vehicle_edittable #invoicenumber").val(node.invoicenumber);
									$("#vehicle_edittable #defectnumber").val(node.defectnumber);
	
									if (node.defectnumber  == null || node.defectnumber == "") {
										$(".defectrow").hide();
										
									} else {
										$(".defectrow").show();
									}
	
									$("#vehicle_edittable input, #vehicle_edittable select, #vehicle_edittable textarea").attr("disabled", true);
										
									$("#vehicle_editdialog").dialog("open");
								}
							
							}
						);
					
				} else {
					callAjax(
							"finddata.php", 
							{ 
								sql: "SELECT A.*, " + 
									 "DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate2, " + 
									 "DATE_FORMAT(A.startdate, '%H:%I') AS starttime2, " + 
									 "DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate2, " + 
									 "DATE_FORMAT(A.enddate, '%H:%I') AS endtime2 " + 
									 "FROM <?php echo $_SESSION['DB_PREFIX'];?>trailerunavailability A " +
									 "WHERE A.id = " + calEvent.id
							},
							function(data) {
								if (data.length > 0) {
									var node = data[0];
									
									$("#trailer_edittable #trailerid").val(node.trailerid);
									$("#trailer_edittable #startdate").val(node.startdate2);
									$("#trailer_edittable #starttime").val(node.starttime2);
									$("#trailer_edittable #enddate").val(node.enddate2);
									$("#trailer_edittable #endtime").val(node.endtime2);
									$("#trailer_edittable #reasonid").val(node.reasonid);
									$("#trailer_edittable #workcarriedout").val(node.workcarriedout);
									$("#trailer_edittable #totalcost").val(node.totalcost);
									$("#trailer_edittable #status").val(node.status);
									$("#trailer_edittable #ordernumber").val(node.ordernumber);
									$("#trailer_edittable #invoicenumber").val(node.invoicenumber);
									$("#trailer_edittable #defectnumber").val(node.defectnumber);

									if (node.defectnumber  == null || node.defectnumber == "") {
										$(".defectrow").hide();
										
									} else {
										$(".defectrow").show();
									}

									$("#trailer_edittable  input, #trailer_edittable  select, #trailer_edittable  textarea").attr("disabled", true);
										
									$("#trailer_editdialog").dialog("open");
								}
							
							}
						);
				}
		    },

			events: {
	            url: 'vehiclecalendarevents.php',
	            type: 'POST', // Send post data
	            error: function() {
	                alert('There was an error while fetching events.');
	            }
	        }
		});
		
	});
	
	
	
</script>
<div id="calendarcontainer">
	<div id='calendar'></div>
</div>

<!--  End of content -->
<?php include("system-footer.php"); ?>