<?php 
	$mode = "V";
	
	require_once("system-header.php");
	require_once("tinymce.php");
	require_once("bookingscopy.php");
	
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
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&region=EU&key=AIzaSyB1DBBtL19Tc4sz0Nl_tmGa014MeHtqjLI" type="text/javascript"></script>
	<script src='js/jquery.ui.timepicker.js'></script>
	<link rel='STYLESHEET' type='text/css' href='./codebase/dhtmlxscheduler_glossy.css'>
	<link href="bookingform.css" rel="stylesheet" type="text/css" />
	<script src='bookingscriptlibrary-20161116.js' type="text/javascript" charset="utf-8"></script>
	
	<style type="text/css" media="screen">
		div[aria-labelledby=ui-dialog-title-keydialog] {
			opacity: 0.6 ! important;
		}
		.utilisation {
			position: relative;
			opacity: 0.4; 
			background-color: white;
			background: repeating-linear-gradient(
			  45deg,
			  #FFCCCC,
			  #FFCCCC 10px,
			  white 10px,
			  white 20px
			);
			margin-top:-18px;
			height:20px;
			float:right;
			margin-right:5px;
			z-index:2022200;
			border-left: 4px solid red;
		}
		.crap {
			opacity: 0.5;
		}
		.utilisation2 {
			opacity: 0.4; 
			background-color: white;
			background: repeating-linear-gradient(
			  45deg,
			  #FFCCCC,
			  #FFCCCC 10px,
			  white 10px,
			  white 20px
			);
		}
		.bookingcell2 {
			position: absolute;
			width:calc(100% - 15px);
		}
		.bookingcell4 {
			text-align:right;
			opacity: 0.4; 
			width:calc(100% - 15px);
			position: absolute;
		}
		.bookingcell3 {
			display:inline-block;
			background-color: white;
			background: repeating-linear-gradient(
			  45deg,
			  #FFCCCC,
			  #FFCCCC 10px,
			  white 10px,
			  white 20px
			);
		}
		.bookingcell {
			width:100%;
		}
		.nowpointer {
			position: relative;
			height:400px;
			width:1px;
			z-index:100;
			top:46px;
			border-left: 1px dashed red;
		}
		.dhx_cal_event_line {
			z-index:101 ! important;
		}
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
		var timemode = "D";
		
<?php
		if (isset($_POST['pk2'])) {
?>
		timemode = "<?php echo $_POST['pk2']; ?>";
<?php
		}
?>
		function getDMYDate(txtDate) {
			var day = txtDate.substring(0, 2) - 0;      
			var month= txtDate.substring(3, 5) - 1; // because months in JS start from 0     
			var year = txtDate.substring(6, 10) - 0;     
		}
		
		function getLatLng(name, address)  {
		    var geocoder = new google.maps.Geocoder();

		    geocoder.geocode(
		    		{ 
		    			'address': address,
		    			componentRestrictions: {country: 'UK'}
		    		}, 
		    		function( results, status ) {
				        if (status == google.maps.GeocoderStatus.OK ) {
				        }
				    }
				);            
		}
		
		function showMap(id) {
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT B.id, A.fromplace, A.toplace, B.place FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							 "LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>bookingleg B " + 
							 "ON B.bookingid = A.id " +
							 "WHERE A.id = " + id + " " +
							 "ORDER BY B.id"
					},
					function(data) {
						if (data.length > 0) {
							var waypoints = [];
							
							for (var i = 0; i < data.length - 1; i++) {
								var node = data[i];
								
								if (node.place != null && node.place != "") {
									waypoints.push({
											stopover: true,
											location: node.place
										});
								}
							}
							
							initializeMap2(node.fromplace, data[data.length - 1].place, waypoints, 0);
							  
							$("#mapDialog").dialog("open");
						}
					
					}
				);
		
		}

	    function initializeMap(start, end, waypoints, startIndex, ingoreLast) {
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
	      			provideRouteAlternatives: false,
	      			travelMode: google.maps.DirectionsTravelMode.DRIVING,
					drivingOptions: {
						    departureTime: new Date(),
						    trafficModel: 'pessimistic'
						},
					unitSystem: google.maps.UnitSystem.IMPERIAL									
	      		};
	      
	      	directionsService.route(request, function(response, status) {
	        		if (status == google.maps.DirectionsStatus.NOT_FOUND) {
	        			pwAlert("Location not found, please provide more detail, e.g. Post code");
	        			
	        		} else if (status == google.maps.DirectionsStatus.OK) {
		        		directionsDisplay.setDirections(response);
						google.maps.event.trigger(map, "resize");
						
						var totalDistance = 0;
					    var totalDuration = 0;
					    var legs = response.routes[0].legs;
						var METERS_TO_MILES = 0.000621371192;

					    for(var i=0; i < legs.length; ++i) {
					        totalDistance += legs[i].distance.value;
					        totalDuration += legs[i].duration.value / 0.9;
					    }

					    var prevTime = $("#startdatetime_time"); 
					    var prevDate = $("#startdatetime");
					    var cnt = 0;

						$(".pointcontainer").each(
								function() {
									var location = $(this).find(".point").val();

									if (location != "") {
										var index = parseInt($(this).attr("index"));
										var startdate = $(this).find(".arrivaldate");
										var starttime = $(this).find(".arrivaltime");
										var departuredate = $(this).find(".departuredate");
										var departuretime = $(this).find(".departuretime");
										
										if (startIndex <= index) {
	    				    				getJourneyTime(
	    				    						prevTime.val(), 
	    				    						prevDate.val(), 
	    				    						startdate.attr("id"), 
	    				    						starttime.attr("id"), 
	    				    						departuredate.attr("id"),
	    				    						departuretime.attr("id"),
	    				    						(legs[cnt].duration.value / 0.9)
		    					    			);
										}
										
									    prevTime = departuretime; 
									    prevDate = departuredate;
									}

								    cnt++;
								}
							);
					    
					       	
	    				getJourneyTime(
	    						prevTime.val(), 
	    						prevDate.val(), 
	    						"enddatetime", 
	    						"enddatetime_time", 
	    						null,
	    						null,
	    						(legs[legs.length - 1].duration.value / 0.9)
			    			);
						
						var mins = Math.round( totalDuration / 60 ) % 60;
						var hours = Math.round( totalDuration / 3600 );
						
						$('#miles').val((Math.round( totalDistance * METERS_TO_MILES * 10 ) / 10));						    
						$('#duration').val(new Number(hours + "." + mins).toFixed(2));	
						
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
					$("#mapDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Map",
							buttons: {
								"Instructions": function() {
									$("#map_steps").slideToggle();
								},
								"Close": function() {
									$(this).dialog("close");
								}
							}
						});
					$("#xweek_tab").click(
							function(e) {
								call("vehiclemode", {
										pk1: dateToDMY(scheduler.getState().date),
										pk2: "W"
									});
							}
						);
					
					$("#xday_tab").click(
							function(e) {
								call("vehiclemode", {
										pk1: dateToDMY(scheduler.getState().date),
										pk2: "D"
									});
							}
						);
					
					$("#btnPrevPrices").click(
							function() {
								showPreviousPrices();
							}
						);
					
					$("#copyDialog").dialog({
						autoOpen: false,
						modal: true,
						width: 400,
						title: "Copy",
						buttons: {
							"Copy": function() {
								if (verifyStandardForm("#copyform")) {
									$.ajax({
											url: "copybooking.php",
											dataType: 'json',
											async: false,
											data: { 
												id: $("#bookingid").val(),
												date: $("#copydate").val(),
												time: $("#copytime").val(),
												vehicleid: $("#copyvehicleid").val(),
												ordernumber: $("#copyordernumber").val()
											},
											type: "POST",
											error: function(jqXHR, textStatus, errorThrown) {
												pwAlert("ERROR :" + errorThrown);
											},
											success: function(data) {
											}
										});
																			
									updateBooking();
									
									$("#copyDialog").dialog("close");
								}
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
					
					$("#prevPriceDialog").dialog({
						autoOpen: false,
						modal: true,
						width: 800,
						title: "Previous Prices",
						buttons: {
							"Select": function() {
								$("#charge").val($("input[type='radio'][name='pricecheck']:checked").val());
								$("#fixedprice").attr("checked", true);
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
							width: 1050,
							height: 600,
							opacity: 0.4,
							overlay: { opacity: 0.3, background: "white" },
							title: "Booking",
							buttons: {
								"Copy": function() {
									$("#copydate").val("<?php echo date("d/m/Y"); ?>");
									$("#copytime").val("<?php echo date("H:m"); ?>");
									$("#copyvehicleid").val("0");
									$("#copyordernumber").val("");
									$("#copyDialog").dialog("open");
								},
								"Map": function() {
									showMap($("#bookingid").val());
								},
								"Delivery Note": function() {
									window.open("deliverynotereport.php?id=" + $("#bookingid").val());
								},
								"Save": function() {
									if (! verifyStandardForm("#bookinginnerform")) {
										return;
									}

									if (! validateForm($("#bookingid").val())) {
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
									askremoval();
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
									if ($("#priceagreed").val() == "Y") {
										updateBooking();
										$("#bookingdialog").dialog("close");
									}

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
					$("#worktypeid").change(worktypeid_onchange);

					setupEvents();
					
				    new google.maps.places.Autocomplete(
				    		document.getElementById("fromplace"), 
				    		{
				        		region: "uk",
				        		componentRestrictions: {country: ["uk"]}       
				        	}
				        );

				    new google.maps.places.Autocomplete(
				    		document.getElementById("toplace"), 
				    		{
				        		region: "uk",
				        		componentRestrictions: {country: ["uk"]}       
				        	}
				        );

					scheduleCheck();
					
				}
			);
  	    
  	    function getAverageWaitTime() {
  	    	return <?php echo getSiteConfigData()->averagewaittime * 60; ?>;
  	    }		

		function positionNowPoint() {
			if (timemode == "D") {
				var hour = 0;
				var leftPos = 0;
				var currentMins = new Date().getMinutes();
				var currentHour = new Date().getHours();

				$(".dhx_scale_bar").each(
						function() {
							if (hour++ == currentHour) { 
								leftPos = $(this).attr("offsetLeft");
								leftPos += ((currentMins / 60) * $(this).attr("offsetWidth"));
							}
						}
					);

				$(".nowpointer").css("left", leftPos + "px");
				$(".nowpointer").css("height", ($("#scheduler_here").attr("offsetHeight") - 20) + "px");
			}
		}

		function checkForOverdueBookings() {
			$.ajax({
					url: "checkoverduebookings.php",
					dataType: 'html',
					async: true,
					data: { 
					},
					type: "POST",
					error: function(jqXHR, textStatus, errorThrown) {
//						pwAlert("ERROR :" + errorThrown);
					},
					success: function(data) {
						if (data != "") {
							$("#appwarning").html(data);
							$("#appwarning").show();
							$("#appwarningclose").show();

						} else {
							$("#appwarning").hide();
							$("#appwarningclose").hide();
						}
					}
				});
		}
		
		function scheduleCheck() {
			positionNowPoint();
			checkForOverdueBookings();

			setTimeout(
					function() {
						scheduleCheck();
					},
					1000 * 60 * 2					
				);
		}

		function updateBooking() {
			var legs = new Array();
			var pointIndex = 0;
			
			$(".pointcontainer").each(
					function() {
						legs[pointIndex++] = {
								place: $(this).find(".point").val(),
								departuredate: $(this).find(".departuredate").val(),
								departuretime: $(this).find(".departuretime").val(),
								arrivaldate: $(this).find(".arrivaldate").val(),
								arrivaltime: $(this).find(".arrivaltime").val(),
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
						agencyvehicleregistration: $("#agencyvehicleregistration").val(),
						driverphone: $("#driverphone").val(),
						worktypeid: $("#worktypeid").val(),
						nominalledgercodeid: $("#nominalledgercodeid").val(),
						loadtypeid: $("#loadtypeid").val(),
						ordernumber: $("#ordernumber").val(),
						ordernumber2: $("#ordernumber2").val(),
						miles: $("#miles").val(),
						duration: $("#duration").val(),
						pallets: $("#pallets").val(),
						weight: $("#weight").val(),
						rate: $("#rate").val(),
						charge: $("#charge").val(),
						notes: tinyMCE.get("notes").getContent(),
						startdatetime: $("#startdatetime").val(),
						startdatetime_time: $("#startdatetime_time").val(),
						fromplace: $("#fromplace").val(),
						enddatetime: $("#enddatetime").val(),
						enddatetime_time: $("#enddatetime_time").val(),
						toplace: $("#toplace").val()
					},
					function(data) {
						scheduler.clearAll();
						scheduler.setCurrentView(null, "timeline");
						
						checkForOverdueBookings();
					},
					false
				);

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
			scheduler.locale.labels.xweek_tab = "Weekly";
			scheduler.locale.labels.xday_tab = "Daily";
			scheduler.locale.labels.section_custom="Section";
			scheduler.config.xml_date="%Y-%m-%d %H:%i";
			scheduler.config.mark_now = true;			
			scheduler.config.first_hour = 6;
			scheduler.config.last_hour = 23;
			scheduler.config.container_autoresize = false;
			scheduler.config.dy = 20;
			//===============
			//Configuration
			//===============
			var sections=[
<?php 
				if ($mode == "V") {
					$sql = "SELECT A.id, B.imageid, A.registration AS name, B.name AS typename 
							FROM {$_SESSION['DB_PREFIX']}vehicle A 
							INNER JOIN {$_SESSION['DB_PREFIX']}vehicletype B 
							ON B.id = A.vehicletypeid 
							WHERE A.active = 'Y' 
							ORDER BY B.code, A.registration";

				} else if ($mode == "T") {
					$sql = "SELECT id, registration AS name, '' AS typename
							FROM {$_SESSION['DB_PREFIX']}trailer A 
							WHERE A.active = 'Y' 
							ORDER BY registration";
					
				} else if ($mode == "D") {
					$sql = "SELECT id, CASE WHEN agencydriver = 'Y' THEN CONCAT('(Agency) - ', name) ELSE name END name, '' AS typename 
							FROM {$_SESSION['DB_PREFIX']}driver 
							ORDER BY agencydriver, name";
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
						
						if ($member['imageid'] != null) {
?>
						{key:<?php echo $member['id']; ?>, label:"<img src='system-imageviewer.php?id=<?php echo $member['imageid'] . "' />&nbsp;". $member['name']; ?>"}
<?php
						} else if ($member['typename'] != "") {
?>
						{key:<?php echo $member['id']; ?>, label:"<?php echo $member['typename'] . " - " . $member['name']; ?>"}
<?php
						} else {
?>
						{key:<?php echo $member['id']; ?>, label:"<?php echo $member['name']; ?>"}
<?php
						}
					}
				}
		
?>
			];

			if (timemode == "D") {
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
						render:"bar",
						dy: 25
					});

			} else {
				scheduler.createTimelineView({
						name:	"timeline",
						x_unit:"day",//measuring unit of the X-Axis.
					    x_date:"%D %d %M %y", //date format of the X-Axis
					    x_step:1,      //X-Axis step in 'x_unit's
					    x_size:7,      //X-Axis length specified as the total number of 'x_step's
					    x_start:0,     //X-Axis offset in 'x_unit's
					    x_length:7,    //number of 'x_step's that will be scrolled at a time
						y_unit:	sections,
						y_property:	"section_id",
						render:"bar",
						dy: 25
					});
			}

			scheduler.locale.labels.timeline_tab = "Timeline";
			scheduler.locale.labels.section_custom="Section";
			scheduler.config.details_on_create=false;
			scheduler.config.dblclick_create = false;
//			scheduler.config.drag_in = false;	      	

			scheduler.attachEvent("onBeforeDrag", function (id, mode, e){
	      		var event = scheduler.getEvent(id);

		      	return (event.type == "B");
			});			
				
			scheduler.attachEvent("onBeforeViewChange", function (old_mode, old_date, mode, date) {
			    if (old_mode != mode || +old_date != +date)
			        scheduler.clearAll();
						
			    return true;
			});
			
			scheduler.attachEvent("onSchedulerResize", function() {
				positionNowPoint();
			});
			
			scheduler.attachEvent("onOptionsLoadFinal", function () {
				if (timemode == "D") {
					$("#scheduler_here").append("<div class='nowpointer'></div>");
					positionNowPoint();
				}
			});
			
			scheduler.attachEvent("onTimelineCreated", function (){
			    return true; 
			});
			
			scheduler.attachEvent("onViewChange", function (m, d) {
				if (timemode == "D") {
					if (d.getFullYear() == <?php echo date("Y"); ?> && 
					   (d.getMonth() + 1) == <?php echo date("m"); ?> && 
						d.getDate() == <?php echo date("d"); ?>) {
	
						$(".nowpointer").show();
						
					} else {
						$(".nowpointer").hide();
					}
				}
			});
			
			scheduler.attachEvent("onBeforeEventCreated",function(){return false;})
			scheduler.attachEvent("onEventChanged", function(id,ev){
					var startDate = dateToDMYHM(ev.start_date);
					var endDate = dateToDMYHM(ev.end_date);

					callAjax(
							"updatebookingevent.php", 
							{ 
								startdate: startDate,
								enddate: endDate,
								id: id,
								sectionid: ev.section_id,
								mode: "<?php echo $mode; ?>"
							},
							function(data) {
								scheduler.clearAll();
								scheduler.setCurrentView(null, "timeline");
								
								checkForOverdueBookings();
							},
							true
						);
				});
			scheduler.attachEvent("onDblClick",function(){return false;})
	      	scheduler.attachEvent("onClick",function(parentnode) {
	      		var event = scheduler.getEvent(parentnode);

		      	if (event.type != "B") {
			      	return;
		      	}
		      	
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.*, B.street, B.address2, B.town, B.city, B.county, B.postcode, B.telephone " + 
								 "FROM <?php echo $_SESSION['DB_PREFIX']; ?>booking A " +
								 "LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX']; ?>customer B " +
								 "ON B.id = A.customerid " +
								 "WHERE A.id = " + parentnode
						},
						function(data) {
							if (data.length == 1) {
								var node = data[0];
								var address = node.street;

								if (node.address2 != null && node.address2 != "") address += "<br>" + node.address2;
								if (node.town != null && node.town != "") address += "<br>" + node.town;
								if (node.city != null && node.city != "") address += "<br>" + node.city;
								if (node.county != null && node.county != "") address += "<br>" + node.county;
								if (node.postcode != null && node.postcode != "") address += "<br>" + node.postcode;

								address += "<br><b>Tel:</b>" + node.telephone;

								$("#ui-dialog-title-bookingdialog").html("<i>Booking Number</i> : <b><?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.id, 6) + "</b>");
								$(".address").html(address);

								$("#bookingid").val(parentnode);
								$("#customerid").val(node.customerid);
								$("#statusid").val(node.statusid);
								$("#originalstatusid").val(node.statusid);
								$("#memberid").val(node.memberid);
								
								$("#toplace_phone").val(node.toplace_phone);
								$("#toplace_ref").val(node.toplace_ref);
								$("#fromplace_phone").val(node.fromplace_phone);
								$("#fromplace_ref").val(node.fromplace_ref);
								$("#agencydriver").val(node.agencydriver);
								$("#driverid").val(node.driverid);
								$("#vehicleid").val(node.vehicleid);
								$("#vehicletypeid").val(node.vehicletypeid);
								$("#trailerid").val(node.trailerid);
								$("#drivername").val(node.drivername);
								$("#agencyvehicleregistration").val(node.agencyvehicleregistration);
								$("#driverphone").val(node.driverphone);
								$("#worktypeid").val(node.worktypeid);
								$("#nominalledgercodeid").val(node.nominalledgercodeid);
								$("#loadtypeid").val(node.loadtypeid);
								$("#ordernumber").val(node.ordernumber);
								$("#ordernumber2").val(node.ordernumber2);

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

									day = $("#startdatetime").val().substring(0, 2) - 0;      
									month= $("#startdatetime").val().substring(3, 5) - 1; // because months in JS start from 0     
									year = $("#startdatetime").val().substring(6, 10) - 0; 

									var startSeconds = (new Date(year, month, day)).getTime();      
									var startToday = (new Date(<?php echo date("Y"); ?>, <?php echo date("m") - 1; ?>, <?php echo date("d"); ?>)).getTime();      

									if (node.statusid < 7 && (startToday < startSeconds)) {
										$("#statusid option[value='6']").attr("disabled", true);
										$("#statusid option[value='7']").attr("disabled", true);
										
									} else {
										$("#statusid option[value='6']").attr("disabled", false);
										$("#statusid option[value='7']").attr("disabled", false);
									}
								}
								
								$("#memberid").attr("disabled", true);
								
								fetchOverHeadRates();

								driverid_onchange();
								vehicleid_onchange();
								$("#agencydriver").val(node.agencydriver);
								$("#driverid").val(node.driverid);
								$("#vehicleid").val(node.vehicleid);

								$("#miles").val(node.miles);
								$("#duration").val(node.duration);
								$("#pallets").val(node.pallets);
								$("#weight").val(node.weight);
								$("#rate").val(node.rate);
								$("#charge").val(node.charge);

								$("#startdatetime").val(node.startdatetime.substring(0, 10));
								$("#startdatetime_time").val(node.startdatetime.substring(11, 16));
								$("#fromplace").val(node.fromplace);
								$("#enddatetime").val(node.enddatetime.substring(0, 10));
								$("#enddatetime_time").val(node.enddatetime.substring(11, 16));
								$("#toplace").val(node.toplace);

								$("#fixedprice").attr("checked", node.fixedprice == 1);

								tinyMCE.get("notes").setContent(node.notes);
							}
						},
						false
					);

		      		$("#bookingdialog").dialog("open");
		      		
			      	return false;
		      	});

<?php 
	if (isset($_SESSION['BOOKING_GANTT'])) {
?>
			var strDate = "<?php echo $_SESSION['BOOKING_GANTT']; ?>".split('-');
			var date = new Date(strDate[0], strDate[1] - 1, strDate[2]);

			scheduler.init('scheduler_here',date,"timeline");
<?php 
	} else {
?>
			scheduler.init('scheduler_here',new Date(),"timeline");
<?php 
	}
?>
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
			sch.style.height = ( $("body").attr("offsetHeight") - 220) + "px";
			var contbox = document.getElementById("contbox");
			contbox.style.width = (parseInt(document.body.offsetWidth)-300)+"px";
		}

		function drivermode() {
			call("drivermode", {
					pk1: dateToDMY(scheduler.getState().date),
					pk2: timemode
				});
		}

		function trailermode() {
			call("trailermode", {
					pk1: dateToDMY(scheduler.getState().date),
					pk2: timemode
				});
		}

		function vehiclemode() {
			call("vehiclemode", {
					pk1: dateToDMY(scheduler.getState().date),
					pk2: timemode
				});
		}
		
		function askremoval() {
			$("#confirmremovedialog .confirmdialogbody").html("You are about to remove this booking.<br>Are you sure ?");
			$("#confirmremovedialog").dialog("open");
		}
		
		function confirmremoval(crudID) {
			callAjax(
					"removeschedule.php", 
					{ 
						id: $("#bookingid").val()
					},
					function(data) {
						checkForOverdueBookings();
					},
					false
				);

			scheduler.clearAll();
			scheduler.setCurrentView(null, "timeline");
			
			$("#bookingdialog").dialog("close");
			$("#confirmremovedialog").dialog("close");
		}
	
	</script>
	
	<?php 
		createConfirmDialog("confirmremovedialog", "Confirm removal ?", "confirmremoval");
	?>
	<div id="mapDialog" class="modal">
     	<div id="map_canvas" style="width:780px;height:350px; border:1px solid grey; ">
		</div>
     	<div id="map_steps" style="width:780px;height:150px; border:1px solid grey; overflow:auto ">
		</div>
	</div>
	<div id="copyDialog" class="modal">
		<form id="copyform">
			<table cellpadding=5 cellspacing=5 style="table-layout: fixed" width=400>
				<tr>
					<td width='90px'>Start Date / Time</td>
					<td>
						<input id="copydate" name="copydate" class="datepicker" required="true" />
						<span></span>
						<input id="copytime" name="copytime" class="timepicker" required="true" />
						<span></span>
					</td>
				</tr>
				<tr>
					<td width='90px'>Vehicle</td>
					<td>
						<?php createCombo("copyvehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "WHERE active = 'Y'", false); ?>
						<span></span>
					</td>
				</tr>
				<tr>
					<td width='90px'>Order Number</td>
					<td>
						<input type="text" style="width:120px" id="copyordernumber" name="copyordernumber" />
					</td>
				</tr>
			</table>
		</form>
	</div>
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
		<div id="prevPriceDiv" class="crudentryform">
		</div>
	</div>
	<div id="rateCardDialog" class="modal">
		<div>
			<iframe id="rateCardIframe" src="about:blank" frameborder=1 style='width:760px;height:510px'></iframe>
		</div>
	</div>
	<div onclick="drivermode()" style="font-size:11px; position:absolute; left:380px; top:57px; z-index:100" class="dhx_cal_tab <?php if ($mode == "D") echo "active"; ?>">&nbsp;&nbsp;Drivers</div>
	<div onclick="vehiclemode()" style="font-size:11px; position:absolute; left:450px; top:57px; z-index:100" class="dhx_cal_tab <?php if ($mode == "V") echo "active"; ?>">&nbsp;Vehicles</div>
	<div onclick="trailermode()" style="font-size:11px; position:absolute; left:520px; top:57px; z-index:100" class="dhx_cal_tab <?php if ($mode == "T") echo "active"; ?>">&nbsp;Trailers</div>
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
			<div class="dhx_cal_tab2" id="xday_tab" name="xday_tab" style="right:215px;"></div>
			<div class="dhx_cal_tab" name="timeline_tab" style="right:280px;"></div>
			<div class="dhx_cal_tab2" id="xweek_tab" name="xweek_tab" style="right:150px;"></div>
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>		
	</div>
<?php 
	include("system-footer.php");
?>