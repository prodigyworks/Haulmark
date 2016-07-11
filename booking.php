<?php 
	$mode = "V";
	
	include("system-header.php");
	include("tinymce.php");
	
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
	<script type='text/javascript' src='jsc/jquery.autocomplete.js'></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
	<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"></script>
	<script src='js/jquery.ui.timepicker.js'></script>
	<link rel='STYLESHEET' type='text/css' href='./codebase/dhtmlxscheduler_glossy.css'>
	<link rel="stylesheet" href="./codebase/ext/dhtmlxscheduler_ext.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link href="bookingform.css" rel="stylesheet" type="text/css" />
	<script src='bookingscriptlibrary-20160710.js' type="text/javascript" charset="utf-8"></script>
	
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
		var counter = 1;
		var map = null;

		function getDMYDate(txtDate) {
			var day = txtDate.substring(0, 2) - 0;      
			var month= txtDate.substring(3, 5) - 1; // because months in JS start from 0     
			var year = txtDate.substring(6, 10) - 0;     
		}
		
		function getLatLng(name, address)  {
		    var geocoder = new google.maps.Geocoder();

		    geocoder.geocode(
		    		{ 
		    			'address' : address 
		    		}, 
		    		function( results, status ) {
				        if (status == google.maps.GeocoderStatus.OK ) {
				        }
				    }
				);            
		}

	    function initializeMap(start, end, waypoints, startIndex) {
	    	if (map == null) {
			    directionsService = new google.maps.DirectionsService();
			    directionsDisplay = new google.maps.DirectionsRenderer(); 
			    
			    var mapOptions = { mapTypeId: google.maps.MapTypeId.ROADMAP, disableDefaultUI: true }
			    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
			    var bounds = new google.maps.LatLngBounds();
			    
			    directionsDisplay.setMap(map);
			    google.maps.event.addListenerOnce(map, 'idle', function () { });
	    	}
	    	
	      	var request = { 
	      			origin: start, 
	      			destination: end, 
	      			waypoints: waypoints,
	      			travelMode: google.maps.DirectionsTravelMode.DRIVING 
	      		};
	      
	      	directionsService.route(request, function(response, status) {
	        		if (status == google.maps.DirectionsStatus.OK) {
		        		directionsDisplay.setDirections(response);
						google.maps.event.trigger(map, "resize");
						
						var totalDistance = 0;
					    var totalDuration = 0;
					    var legs = response.routes[0].legs;
						var METERS_TO_MILES = 0.000621371192;

					    for(var i=0; i < legs.length; ++i) {
					        totalDistance += legs[i].distance.value;
					        totalDuration += legs[i].duration.value / 0.8;
					        totalDuration += <?php echo getSiteConfigData()->averagewaittime * 60; ?> * 60;
					    }
					    
					    var prevTime = $("#startdatetime_time"); 
					    var prevDate = $("#startdatetime");
					    var cnt = 0;

						$(".pointcontainer").each(
								function() {
									var index = parseInt($(this).attr("index"));
									var startdate = $(this).find(".datepicker");
									var starttime = $(this).find(".timepicker");
									
									if (startIndex <= index) {
	    					    		starttime.val(
	    					    				getJourneyTime(
	    					    						prevTime.val(), 
	    					    						prevDate.val(), 
	    					    						startdate.attr("id"), 
	    					    						(legs[cnt].duration.value / 0.8) + <?php echo getSiteConfigData()->averagewaittime * 60; ?>
	    					    					)
	    					    			);
									}
									
								    prevTime = starttime; 
								    prevDate = startdate;

								    cnt++;
								}
							);
					    
					       	
			    		$('#enddatetime_time').val(
			    				getJourneyTime(
			    						prevTime.val(), 
			    						prevDate.val(), 
			    						"enddatetime", 
			    						(legs[legs.length - 1].duration.value / 0.8)
			    					)
			    			);
						
						var mins = Math.round( totalDuration / 60 ) % 60;
						var hours = Math.round( totalDuration / 3600 );
						
						$('#miles').val((Math.round( totalDistance * METERS_TO_MILES * 10 ) / 10));						    
						$('#duration').val(hours + "." + mins);	
						
						fetchOverHeadRates();					    
	        		}
	      		});
	      		
  	    }			

		$(document).ready(
				function() {
					init();
<?php
					if (isset($_POST['pk1'])) {
?>
					var txtDate = "<?php echo $_POST['pk1']; ?>";
					var day = txtDate.substring(0, 2) - 0;      
					var month= txtDate.substring(3, 5) - 1; // because months in JS start from 0     
					var year = txtDate.substring(6, 10) - 0;     
					
					scheduler.setCurrentView(new Date(year, month, day));
<?php
					}
?>

					$("#btnPrevPrices").click(
							function() {
								showPreviousPrices();
							}
						);
					
					$("#prevPriceDialog").dialog({
						autoOpen: false,
						modal: true,
						width: 800,
						title: "Previous Prices",
						buttons: {
							"Select": function() {
								$("#charge").val($("input[type='radio'][name='pricecheck']:checked").val());
								$(this).dialog("close");
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
					
					$("#rateCardDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							height: 600,
							title: "Rate Card",
							buttons: {
								"Close": function() {
									$(this).dialog("close");
								}
							}
						});

					$("#bookingdate").datepicker({ 
							showOn: 'button', 
							buttonImageOnly: true, 
							buttonImage: 'images/ui-icon-calendar.png',
							dateFormat: "dd/mm/yy"
						});
					
					$("#bookingdate").change(
							function() {
								var txtDate = $(this).val();
								var day = txtDate.substring(0, 2) - 0;      
								var month= txtDate.substring(3, 5) - 1; // because months in JS start from 0     
								var year = txtDate.substring(6, 10) - 0;     
								
								scheduler.setCurrentView(new Date(year, month, day));
							}
						); 

<?php
					if (! isUserInRole("ADMIN")) {
?>					
					$("#charge").attr("disabled", true);
					$("#rate").attr("disabled", true);
<?php
					}
?>					
					
					$("#keydialog").dialog({
							modal: false,
							minWidth: 90,
							dialogClass: "kev",
							autoOpen: true,
							width: "auto",
							opacity: 0.4,
							position: ["right", "bottom"],
							overlay: { opacity: 0.3, background: "white" },
							title: "Key"
						});
					
					$("#bookingdialog").dialog({
							modal: true,
							dialogClass: "kev",
							autoOpen: false,
							width: 960,
							height: 600,
							opacity: 0.4,
							overlay: { opacity: 0.3, background: "white" },
							title: "Booking",
							buttons: {
								"Print": function() {
									window.open("deliverynotereport.php?id=" + $("#bookingid").val());
								},
								"Save": function() {
									if (! verifyStandardForm("#bookinginnerform")) {
										return;
									}

									if ($("#statusid").val() == 7 && 
										$("#statusid").val() != $("#originalstatusid").val()) {
										
<?php
										if (! isUserInRole("COMPLETED")) {
?>
										pwAlert("You do not have permission to complete this job.");
<?php
										} else {
?>
										/* Move to complete. */
										$("#priceagreed").val("Y");
										$("#agreedby").val("<?php echo GetUserName(); ?>");
										$("#completedialog").dialog("open");
<?php
										}
?>
									} else {
										updateBooking();
										
										$("#bookingdialog").dialog("close");
									}
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

					$("#completedialog").dialog({
							modal: true,
							autoOpen: false,
							width: 380,
							opacity: 0.4,
							overlay: { opacity: 0.3, background: "white" },
							title: "Completion Confirmation",
							buttons: {
								"Continue": function() {
									updateBooking();
									
									$("#bookingdialog").dialog("close");
									$(this).dialog("close");
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});

					$("#customerid").change(customerid_onchange);
					$("#vehicleid").change(vehicleid_onchange);
					$("#vehicletypeid").change(vehicletypeid_onchange);
					$("#agencydayrate").change(calculateRate2);
					$("#allegrodayrate").change(calculateRate2);
					$("#vehiclecostoverhead").change(calculateRate2);
					$("#fuelcostoverhead").change(calculateRate2);
					$("#maintenanceoverhead").change(calculateRate2);
					$("#wages").change(calculateRate2);
					$("#driverid").change(driverid_onchange);
				}
			);

		function updateBooking() {
			var legs = new Array();
			var pointIndex = 0;
			
			$(".pointcontainer").each(
					function() {
						legs[pointIndex++] = {
								place: $(this).find(".point").val(),
								date: $(this).find(".datepicker").val(),
								time: $(this).find(".timepicker").val(),
								reference: $(this).find(".reference").val(),
								phone: $(this).find(".phone").val()
							};
					}
				);

			
			callAjax(
					"updatebooking.php", 
					{ 
						id: $("#bookingid").val(),
						legs: legs,
						toplace_phone: $("#toplace_phone").val(),
						fromplace_phone: $("#fromplace_phone").val(),
						toplace_ref: $("#toplace_ref").val(),
						fromplace_ref: $("#fromplace_ref").val(),
						customerid: $("#customerid").val(),
						statusid: $("#statusid").val(),
						memberid: $("#memberid").val(),
						driverid: $("#driverid").val(),
						agencydriver: $("#agencydriver").val(),
						vehicleid: $("#vehicleid").val(),
						vehicletypeid: $("#vehicletypeid").val(),
						trailerid: $("#trailerid").val(),
						drivername: $("#drivername").val(),
						driverphone: $("#driverphone").val(),
						worktypeid: $("#worktypeid").val(),
						loadtypeid: $("#loadtypeid").val(),
						ordernumber: $("#ordernumber").val(),
						ordernumber2: $("#ordernumber2").val(),
						miles: $("#miles").val(),
						duration: $("#duration").val(),
						pallets: $("#pallets").val(),
						weight: $("#weight").val(),
						rate: $("#rate").val(),
						charge: $("#charge").val(),
						notes: $("#notes").val(),
						startdatetime: $("#startdatetime").val(),
						startdatetime_time: $("#startdatetime_time").val(),
						fromplace: $("#fromplace").val(),
						enddatetime: $("#enddatetime").val(),
						enddatetime_time: $("#enddatetime_time").val(),
						toplace: $("#toplace").val()
					},
					function(data) {
					},
					false
				);

			scheduler.clearAll();
			scheduler.setCurrentView(null, "timeline");
		}
		
  	    function calculateRate2() {
  	    	calculateRate(
  	    			<?php echo getSiteConfigData()->defaultwagesmargin; ?>, 
  	    			<?php echo getSiteConfigData()->defaultprofitmargin; ?>
  	    		);
      	}

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
	      	scheduler.attachEvent("onClick",function(parentnode) {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.* " + 
								 "FROM <?php echo $_SESSION['DB_PREFIX']; ?>booking A " +
								 "WHERE id = " + parentnode
						},
						function(data) {
							if (data.length == 1) {
								var node = data[0];

								$("#bookingid").val(parentnode);
								$("#customerid").val(node.customerid);
								$("#statusid").val(node.statusid);
								$("#originalstatusid").val(node.statusid);
								$("#memberid").val(node.memberid);
								
								$("#toplace_phone").val(node.toplace_phone);
								$("#toplace_ref").val(node.toplace_ref);
								$("#fromplace_phone").val(node.fromplace_phone);
								$("#fromplace_ref").val(node.fromplace_ref);
								$("#driverid").val(node.driverid);
								$("#agencydriver").val(node.agencydriver);
								$("#vehicleid").val(node.vehicleid);
								$("#vehicletypeid").val(node.vehicletypeid);
								$("#trailerid").val(node.trailerid);
								$("#drivername").val(node.drivername);
								$("#driverphone").val(node.driverphone);
								$("#worktypeid").val(node.worktypeid);
								$("#loadtypeid").val(node.loadtypeid);
								$("#ordernumber").val(node.ordernumber);
								$("#ordernumber2").val(node.ordernumber2);
								$("#notes").val(node.notes);

								$("#startdatetime").val(node.startdatetime.substring(0, 10));
								$("#startdatetime_time").val(node.startdatetime.substring(11, 16));
								$("#fromplace").val(node.fromplace);
								$("#enddatetime").val(node.enddatetime.substring(0, 10));
								$("#enddatetime_time").val(node.enddatetime.substring(11, 16));
								$("#toplace").val(node.fromplace);
								
								$(".pointcontainer").remove();
								
								counter = 1;

								loadLegs(parentnode);

								if (node.statusid == 8) {
									$("#bookinginnerform input").attr("disabled", true);
									$("#bookinginnerform textarea").attr("disabled", true);
									$("#bookinginnerform select").attr("disabled", true);
									$("#bookinginnerform .bookingbutton").hide();
									$("#bookinginnerform .pointimage").hide();
									$("#ordernumber2").attr("disabled", false);
									
								} else {
									$("#bookinginnerform input").attr("disabled", false);
									$("#bookinginnerform textarea").attr("disabled", false);
									$("#bookinginnerform select").attr("disabled", false);
									$("#bookinginnerform .bookingbutton").show();
									$("#bookinginnerform .pointimage").show();
								}
								
								$("#memberid").attr("disabled", true);
								
								fetchOverHeadRates();
							
								driverid_onchange();

								$("#miles").val(node.miles);
								$("#duration").val(node.duration);
								$("#pallets").val(node.pallets);
								$("#weight").val(node.weight);
								$("#rate").val(node.rate);
								$("#charge").val(node.charge);
							}
						},
						false
					);

		      		$("#bookingdialog").dialog("open");
		      		
			      	return false;
		      	});
	      	scheduler.attachEvent("onClick",function(){return false;})

//			
//			scheduler.attachEvent("onBeforeEventChanged", function(ev, e, is_new){
//			    //any custom logic here
//			    var strStartDate, strEndDate;
//
//			    strStartDate = padZero(ev.start_date.getDate());
//			    strStartDate += "/" + padZero(ev.start_date.getMonth() + 1);
//			    strStartDate += "/" + (1900 + ev.start_date.getYear());
//			    strStartDate += " " + padZero(ev.start_date.getHours());
//			    strStartDate += ":" + padZero(ev.start_date.getMinutes());
//			    
//			    strEndDate = padZero(ev.end_date.getDate());
//			    strEndDate += "/" + padZero(ev.end_date.getMonth() + 1);
//			    strEndDate += "/" + (1900 + ev.end_date.getYear());
//			    strEndDate += " " + padZero(ev.end_date.getHours());
//			    strEndDate += ":" + padZero(ev.end_date.getMinutes());
//			    
//				callAjax(
//						"updatebooking.php", 
//						{ 
//							id: ev.id,
//							sectionid: ev.section_id,
//							startdate: strStartDate,
//							enddate: strEndDate
//						},
//						function(data) {
//						}
//					);
//
//			    return true;
//			    
//			});			
			
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
			call("drivermode", {pk1: dateToDMY(scheduler.getState().date)});
		}

		function trailermode() {
			call("trailermode", {pk1: dateToDMY(scheduler.getState().date)});
		}

		function vehiclemode() {
			call("vehiclemode", {pk1: dateToDMY(scheduler.getState().date)});
		}
	</script>
	<div id="map_canvas" class="modal"></div>
	<div id="keydialog" class="modal">
		<table width='220px'>
<?php 			
			$qry = "SELECT * 
					FROM {$_SESSION['DB_PREFIX']}bookingstatus 
					ORDER BY sequence";
		
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
		<div id="bookinginnerform">
			<?php include("bookingform.php"); ?>
		</div>
	</div>
	<div id="completedialog" class="modal">
		<table cellspacing=10>
			<tr>
				<td>Price Agreed</td>
				<td>
					<SELECT id="priceagreed">
						<OPTION value="Y">Yes</OPTION>
						<OPTION value="N">No</OPTION>
					</SELECT>
				</td>
			</tr>
			<tr>
				<td>Agreed By</td>
				<td>
					<input readonly type="text" style='width:250px' id="agreedby" />
				</td>
			</tr>
		</table>
	</div>
	<div style="position:absolute; left:210px; top:60px; z-index:100">
		<input type="text" id="bookingdate" class="modal"></span>
	</div>
	<div id="prevPriceDialog" class="modal">
		<div id="prevPriceDiv">
		</div>
	</div>
	<div id="rateCardDialog" class="modal">
		<div>
			<iframe id="rateCardIframe" src="about:blank" frameborder=1 style='width:760px;height:510px'></iframe>
		</div>
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