<?php 
	$mode = "V";
	
	include("system-header.php");
	
	function drivermode() {
		global $mode;
		
		$mode = "D";
	}
		
	function vehiclemode() {
		global $mode;
		
		$mode = "V";
	}
		
	function trailermode() {
		global $mode;
		
		$mode = "T";
	}
	?>
	<script src='./codebase/dhtmlxscheduler.js' type="text/javascript" charset="utf-8"></script>
	<script src='./codebase/ext/dhtmlxscheduler_timeline.js' type="text/javascript" charset="utf-8"></script>
	<script src='bookingscriptlibrary.js' type="text/javascript" charset="utf-8"></script>
	<script type='text/javascript' src='jsc/jquery.autocomplete.js'></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
	<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"></script>
	<link rel='STYLESHEET' type='text/css' href='./codebase/dhtmlxscheduler_glossy.css'>
	<link rel="stylesheet" href="./codebase/ext/dhtmlxscheduler_ext.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
	<style type="text/css" media="screen">
		.keyblock {
			width:10px;
			height:10px;
			border:1px solid black;
		}
		.one_line{
			white-space:nowrap;
			overflow:hidden;
			padding-top:5px; padding-left:5px;
			text-align:left !important;
		}
		
		.dhx_cal_event_line  {
			font-size:11px;
			line-height:12px;
		}
	</style>
	
	<script type="text/javascript" charset="utf-8">

		function initializeMap(start, end, waypoints, startIndex) {
		}

		$(document).ready(
				function() {
					init();
					
					$("#keydialog").dialog({
							modal: false,
							minWidth: 90,
							dialogClass: "kev",
							autoOpen: true,
							width: "auto",
							opacity: 0.4,
							position: ["center", "top"],
							overlay: { opacity: 0.3, background: "white" },
							title: "Key"
						});
					
					$("#bookingdialog").dialog({
							modal: true,
							dialogClass: "kev",
							autoOpen: false,
							width: "auto",
							opacity: 0.4,
							overlay: { opacity: 0.3, background: "white" },
							title: "Edit",
							buttons: {
								Ok: function() {
									callAjax(
											"updateschedule.php", 
											{ 
												id: $("#eventid").val(),
												status: $("#status").val(),
												clientid: $("#clientid").val(),
												memberid: $("#memberid").val(),
												startdate: $("#entrydate").val(),
												enddate: $("#entrydate").val(),
												starttime: $("#starttime").val(),
												endtime: $("#endtime").val()
											},
											function(data) {
											},
											false
										);
	
									scheduler.clearAll();
									scheduler.setCurrentView(null, "timeline");
									
									$("#bookingdialog").dialog("close");
								},
								"Remove": function() {
									callAjax(
											"removeschedule.php", 
											{ 
												id: $("#eventid").val()
											},
											function(data) {
											},
											false
										);
	
									scheduler.clearAll();
									scheduler.setCurrentView(null, "timeline");
									
									$("#bookingdialog").dialog("close");
								},
								Cancel: function() {
									$("#bookingdialog").dialog("close");
								}
							}
						});
				}
			);

		function init() {
			modSchedHeight();
			
			scheduler.locale.labels.timeline_tab = "Timeline";
			scheduler.locale.labels.section_custom="Section";
			scheduler.config.details_on_create=true;
			scheduler.config.details_on_dblclick=true;
			scheduler.config.xml_date="%Y-%m-%d %H:%i";
			
			scheduler.config.first_hour = 6;
			scheduler.config.last_hour = 23;
			//===============
			//Configuration
			//===============
			var sections=[
<?php 
				if ($mode == "V") {
					$sql = "SELECT id, registration AS name FROM {$_SESSION['DB_PREFIX']}vehicle ORDER BY registration";

				} else if ($mode == "T") {
					$sql = "SELECT id, registration AS name FROM {$_SESSION['DB_PREFIX']}trailer ORDER BY registration";
					
				} else if ($mode == "D") {
					$sql = "SELECT id, code AS name FROM {$_SESSION['DB_PREFIX']}driver ORDER BY agencydriver, code";
				}
				
				$result = mysql_query($sql);
				$first = true;
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($first) {
							$first = false;
						} else {
							echo ", ";
						}
?>
						{key:<?php echo $member['id']; ?>, label:"<?php echo $member['name']; ?>"}
<?php
					}
				}
		
?>
			];
				
			scheduler.createTimelineView({
				name:	"timeline",
				x_unit:	"minute",
				x_date:	"%H:%i",
				x_step:	60,
				x_size: 24,
				x_start: 0,
				x_length:	24,
				y_unit:	sections,
				y_property:	"section_id",
				render:"bar"
			});

			scheduler.locale.labels.timeline_tab = "Timeline";
			scheduler.locale.labels.section_custom="Section";
			scheduler.config.details_on_create=false;
			scheduler.config.dblclick_create = false;
			scheduler.config.drag_in = false;	      	
			
			scheduler.attachEvent("onBeforeViewChange", function (old_mode, old_date, mode, date) {
			    if (old_mode != mode || +old_date != +date)
			        scheduler.clearAll();
			    return true;
			});
			scheduler.attachEvent("onBeforeDrag",function(){return false;})
	      	scheduler.attachEvent("onDblClick",function(){return false;})
	      	scheduler.attachEvent("onClick",function(node) {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.* " + 
								 "FROM <?php echo $_SESSION['DB_PREFIX']; ?>booking A " +
								 "WHERE id = " + node
						},
						function(data) {
							if (data.length == 1) {
								var node = data[0];

								$("#customerid").val(node.customerid);
								$("#statusid").val(node.statusid);
								$("#clientid").val(node.clientid);
								$("#memberid").val(node.memberid);

								$("#driverid").val(node.driverid);
								$("#agencydriver").val(node.agencydriver);
								$("#vehicleid").val(node.vehicleid);
								$("#vehicletypeid").val(node.vehicletypeid);
								$("#trailerid").val(node.trailerid);
								$("#drivername").val(node.drivername);
								$("#storename").val(node.storename);
								$("#worktypeid").val(node.worktypeid);
								$("#loadtypeid").val(node.loadtypeid);
								$("#ordernumber").val(node.ordernumber);
								$("#ordernumber2").val(node.ordernumber2);
								$("#miles").val(node.miles);
								$("#duration").val(node.duration);
								$("#pallets").val(node.pallets);
								$("#weight").val(node.weight);
								$("#rate").val(node.rate);
								$("#charge").val(node.charge);
								$("#notes").val(node.notes);
							}
						},
						false
					);

		      		$("#bookingdialog").dialog("open");
		      		
			      	return false;
		      	});
	      	scheduler.attachEvent("onClick",function(){return false;})

			
			scheduler.attachEvent("onBeforeEventChanged", function(ev, e, is_new){
			    //any custom logic here
			    var strStartDate, strEndDate;

			    strStartDate = padZero(ev.start_date.getDate());
			    strStartDate += "/" + padZero(ev.start_date.getMonth() + 1);
			    strStartDate += "/" + (1900 + ev.start_date.getYear());
			    strStartDate += " " + padZero(ev.start_date.getHours());
			    strStartDate += ":" + padZero(ev.start_date.getMinutes());
			    
			    strEndDate = padZero(ev.end_date.getDate());
			    strEndDate += "/" + padZero(ev.end_date.getMonth() + 1);
			    strEndDate += "/" + (1900 + ev.end_date.getYear());
			    strEndDate += " " + padZero(ev.end_date.getHours());
			    strEndDate += ":" + padZero(ev.end_date.getMinutes());
			    
				callAjax(
						"updatebooking.php", 
						{ 
							id: ev.id,
							sectionid: ev.section_id,
							startdate: strStartDate,
							enddate: strEndDate
						},
						function(data) {
						}
					);

			    return true;
			    
			});			
			
			//===============
			//Data loading
			//===============
			scheduler.config.lightbox.sections=[	
				{name:"description", height:130, map_to:"text", type:"textarea" , focus:true},
				{name:"custom", height:23, type:"select", options:sections, map_to:"section_id" },
				{name:"time", height:12, type:"time", map_to:"auto"}
			];
			
			scheduler.init('scheduler_here',new Date(),"timeline");
			scheduler.setLoadMode("day");
			scheduler.config.show_loading = true;

			scheduler.load("events.php?mode=<?php echo $mode; ?>","json",function(){
			    // alert("Data has been successfully loaded");
			    scheduler.updateCollection("sections",sections );
			});
			var dp = new dataProcessor("events.php");
			dp.init(scheduler);
		}
		
		function modSchedHeight(){
			var sch = document.getElementById("scheduler_here");
			sch.style.height = (document.body.offsetHeight - 20) + "px";
			var contbox = document.getElementById("contbox");
			contbox.style.width = (parseInt(document.body.offsetWidth)-300)+"px";
		}

		function drivermode() {
			call("drivermode");
		}

		function trailermode() {
			call("trailermode");
		}

		function vehiclemode() {
			call("vehiclemode");
		}
	</script>
	<div id="map_canvas" class="modal"></div>
	<div id="keydialog" class="modal">
		<table width='220px'>
<?php 			
			$qry = "SELECT * 
					FROM {$_SESSION['DB_PREFIX']}bookingstatus 
					ORDER BY name";
		
			$result = mysql_query($qry);
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$statementnumber = $member['statementnumber'] + 1;
?>
			<tr>
				<td><?php echo $member['name']; ?></td>
				<td>
					<div class="keyblock" style="background-color: <?php echo $member['bgcolour']; ?>; color: <?php echo $member['fgcolour']; ?>">&nbsp;</div>
				</td>
			</tr>
<?php					
				}
			}
?>
		</table>
	</div>
	
	<div id="bookingdialog" class="modal">
		<?php include("bookingform.php"); ?>
	</div>
	<div onclick="drivermode()" style="font-size:11px; position:absolute; left:360px; top:62px; z-index:100" class="dhx_cal_today_button">&nbsp;&nbsp;Drivers</div>
	<div onclick="vehiclemode()" style="font-size:11px; position:absolute; left:430px; top:62px; z-index:100" class="dhx_cal_today_button">&nbsp;Vehicles</div>
	<div onclick="trailermode()" style="font-size:11px; position:absolute; left:500px; top:62px; z-index:100" class="dhx_cal_today_button">&nbsp;Trailers</div>
	<div style="height:0px;background-color:#3D3D3D;border-bottom:5px solid #828282;">
		<div id="contbox" style="float:left;color:white;margin:22px 75px 0 75px; overflow:hidden;font: 17px Arial,Helvetica;color:white">
		</div>
	</div>
	<!-- end. info block -->
	<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_today_button"></div>
			<div class="dhx_cal_date"></div>
			<div class="dhx_cal_tab" name="day_tab" style="right:215px;"></div>
			<div class="dhx_cal_tab" name="timeline_tab" style="right:280px;"></div>
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>		
	</div>
<?php 
	include("system-footer.php");
?>