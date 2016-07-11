<?php
	require_once("crud.php");
	require_once("bookingshared.php");
	
	function copybooking() {
		$id = $_POST['copy_id'];
		$bookingdate = convertStringToDate($_POST['copy_date']) . " " . $_POST['copy_time'];
		$vehicleid = $_POST['copy_vehicleid'];
		$memberid = getLoggedOnMemberID();
		
		$sql = "SELECT startdatetime FROM {$_SESSION['DB_PREFIX']}booking
			    WHERE id = $id";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		$offset = 0;
		
		while (($member = mysql_fetch_assoc($result))) {
			$offset = $member['startdatetime'];
		}
		
		$diff = (strtotime($bookingdate) - strtotime($offset));
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}booking
				(
					vehicleid, driverid, trailerid, customerid, loadtypeid, vehicletypeid, 
					depotid, worktypeid, pallets, items, memberid, startdatetime, enddatetime, 
					dsdatetime, rate, charge, weight, miles, vehiclecostoverhead, allegrodayrate, 
					agencydayrate, wages, fuelcostoverhead, maintenanceoverhead, profitmargin, 
					customercostpermile, bookingtype, postedtosage, statusid, legsummary, duration, 
					ordernumber, ordernumber2, drivername, driverphone, fromplace, toplace, 
					fromplace_lat, fromplace_lng, fromplace_phone, fromplace_ref, toplace_lat, 
					toplace_lng, toplace_phone, toplace_ref, totalmiles, totaltimehrs, notes, 
					metacreateddate, metamodifieddate, metamodifieduserid, metacreateduserid
				)
				SELECT
					$vehicleid, driverid, trailerid, customerid, loadtypeid, vehicletypeid, 
					depotid, worktypeid, pallets, items, memberid, DATE_ADD(startdatetime, INTERVAL $diff SECOND), 
					DATE_ADD(enddatetime, INTERVAL $diff SECOND), DATE_ADD(dsdatetime, INTERVAL $diff SECOND), 
					rate, charge, weight, miles, vehiclecostoverhead, allegrodayrate, 
					agencydayrate, wages, fuelcostoverhead, maintenanceoverhead, profitmargin, 
					customercostpermile, bookingtype, postedtosage, 1, legsummary, duration, 
					ordernumber, ordernumber2, drivername, driverphone, fromplace, toplace, 
					fromplace_lat, fromplace_lng, fromplace_phone, fromplace_ref, toplace_lat, 
					toplace_lng, toplace_phone, toplace_ref, totalmiles, totaltimehrs, notes, 
					NOW(), NOW(), $memberid, $memberid
				FROM {$_SESSION['DB_PREFIX']}booking
				WHERE id = $id";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		$newid = mysql_insert_id();		
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
				(
					bookingid, arrivaltime, departuretime, place, reference,
					phone, place_lng, place_lat, timetaken, miles
				)
				SELECT
					$newid, '$bookingdate', DATE_ADD(departuretime, INTERVAL $diff SECOND), place, reference,
					phone, place_lng, place_lat, timetaken, miles
				FROM {$_SESSION['DB_PREFIX']}bookingleg
				WHERE bookingid = $id";
		$legresult = mysql_query($sql);
		
		if (! $legresult) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	class BookingCrud extends Crud {
		
		public function __construct() {
			parent::__construct();
			
			$and = "";
			
			if (isset($_GET['date'])) {
				$date = convertStringToDate($_GET['date']);
				$and = "AND DATE(A.startdatetime) = '$date' ";
			}
			
			$this->validateForm = "validateForm";
			$this->title = "Bookings";
			$this->table = "{$_SESSION['DB_PREFIX']}booking";
			$this->allowView = true;
			$this->dialogwidth = 950;
			$this->sql = 
				   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
				    E.registration AS vehiclename, F.name AS vehicletypename, 
				    H.name AS statusname, I.fullname, J.name AS worktypename, K.name AS loadtypename
					FROM {$_SESSION['DB_PREFIX']}booking A 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B 
					ON B.id = A.trailerid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver C 
					ON C.id = A.driverid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer D 
					ON D.id = A.customerid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle E 
					ON E.id = A.vehicleid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicletype F 
					ON F.id = A.vehicletypeid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus H 
					ON H.id = A.statusid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members I 
					ON I.member_id = A.memberid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}worktype J 
					ON J.id = A.worktypeid
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}loadtype K 
					ON K.id = A.loadtypeid
					WHERE A.statusid IN ( 1, 2, 3, 9)
					$and
					ORDER BY A.id DESC";
			
			$this->columns = array(
					array(
						'name'       => 'id',
						'length' 	 => 16,
						'pk'		 => true,
						'editable'	 => false,
						'bind'	 	 => false,
						'showInView' => false,
						'filter'	 => false,
						'label' 	 => 'Booking Number'
					),
					array(
						'name'       => 'bookingref',
						'function'   => 'bookingReference',
						'sortcolumn' => 'A.id',
						'type'		 => 'DERIVED',
						'length' 	 => 16,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Booking Number'
					),
					array(
						'name'       => 'statusid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'readonly'   => true,
						'label' 	 => 'Status',
						'table'		 => 'bookingstatus',
						'table_id'	 => 'id',
						'alias'		 => 'statusname',
						'table_name' => 'name'
					),
					array(
						'name'       => 'customerid',
						'type'       => 'DATACOMBO',
						'length' 	 => 35,
						'onchange'	 => 'checkBookingStatus',
						'label' 	 => 'Customer',
						'table'		 => 'customer',
						'table_id'	 => 'id',
						'alias'		 => 'customername',
						'table_name' => 'name'
					),
					array(
						'name'       => 'driverid',
						'type'       => 'DATACOMBO',
						'onchange'	 => 'checkBookingStatus',
						'length' 	 => 34,
						'label' 	 => 'Driver / Agency',
						'table'		 => 'driver',
						'table_id'	 => 'id',
						'required'	 => false,
						'alias'		 => 'driversname',
						'table_name' => 'name'
					),
					array(
						'name'       => 'vehicleid',
						'type'       => 'DATACOMBO',
						'onchange'	 => 'checkBookingStatus',
						'length' 	 => 10,
						'label' 	 => 'Vehicle',
						'table'		 => 'vehicle',
						'required'	 => false,
						'onchange'	 => 'vehicleid_onchange',
						'table_id'	 => 'id',
						'alias'		 => 'vehiclename',
						'table_name' => 'registration'
					),
					array(
						'name'       => 'vehicletypeid',
						'type'       => 'DATACOMBO',
						'onchange'	 => 'checkBookingStatus',
						'length' 	 => 19,
						'label' 	 => 'Vehicle Type',
						'table'		 => 'vehicletype',
						'required'	 => true,
						'onchange'	 => 'vehicletypeid_onchange',
						'table_id'	 => 'id',
						'alias'		 => 'vehicletypename',
						'table_name' => 'name'
					),
					array(
						'name'       => 'trailerid',
						'type'       => 'DATACOMBO',
						'length' 	 => 10,
						'required'   => false,
						'onchange'	 => 'checkBookingStatus',
						'label' 	 => 'Trailer',
						'table'		 => 'trailer',
						'required'	 => false,
						'table_id'	 => 'id',
						'alias'		 => 'trailername',
						'table_name' => 'registration'
					),
					array(
						'name'       => 'worktypeid',
						'type'       => 'DATACOMBO',
						'length' 	 => 20,
						'showInView' => false,	
						'required'   => true,
						'label' 	 => 'Work Type',
						'table'		 => 'worktype',
						'table_id'	 => 'id',
						'alias'		 => 'worktypename',
						'table_name' => 'name'
					),
					array(
						'name'       => 'drivername',
						'showInView' => false,
						'length' 	 => 20,
						'label' 	 => 'Driver Name'
					),
					array(
						'name'       => 'driverphone',
						'showInView' => false,
						'length' 	 => 20,
						'label' 	 => 'Driver Phone'
					),
					array(
						'name'       => 'loadtypeid',
						'type'       => 'DATACOMBO',
						'length' 	 => 16,
						'label' 	 => 'Load Type',
						'table'		 => 'loadtype',
						'required'	 => true,
						'table_id'	 => 'id',
						'alias'		 => 'loadtypename',
						'table_name' => 'name'
					),
					array(
						'name'       => 'fromplace',
						'showInView' => false,
						'length' 	 => 30,
						'type'		 => 'GEOLOCATION',
						'label' 	 => 'From Location'
					),
					array(
						'name'       => 'fromplace_ref',
						'showInView' => false,
						'length' 	 => 30,
						'label' 	 => 'From Location'
					),
					array(
						'name'       => 'fromplace_phone',
						'showInView' => false,
						'length' 	 => 13,
						'label' 	 => 'From Phone'
					),
					array(
						'name'       => 'startdatetime',
						'datatype'	 => 'datetime',
						'length' 	 => 18,
						'label' 	 => 'Collection Date'
					),
					array(
						'name'       => 'legsummary',
						'bind'		 => false,
						'length' 	 => 60,
						'label' 	 => 'To Location'
					),
					array(
						'name'       => 'toplace',
						'length' 	 => 30,
						'showInView' => false,
						'type'		 => 'GEOLOCATION',
						'label' 	 => 'To Location'
					),
					array(
						'name'       => 'toplace_ref',
						'showInView' => false,
						'length' 	 => 30,
						'label' 	 => 'To Location'
					),
					array(
						'name'       => 'toplace_phone',
						'showInView' => false,
						'length' 	 => 13,
						'label' 	 => 'To Phone'
					),
					array(
						'name'       => 'enddatetime',
						'datatype'	 => 'datetime',
						'length' 	 => 18,
						'label' 	 => 'Delivery Time'
					),
					array(
						'name'       => 'ordernumber',
						'length' 	 => 15,
						'label' 	 => 'Order Number'
					),
					array(
						'name'       => 'ordernumber2',
						'length' 	 => 15,
						'required'	 => false,
						'label' 	 => 'Order Number 2'
					),
					array(
						'name'       => 'maintenanceoverhead',
						'length' 	 => 22,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Maintenance Overhead'
					),
					array(
						'name'       => 'profitmargin',
						'length' 	 => 14,
						'showInView' => false,
						'align'		 => 'right',
						'label' 	 => 'Profit Margin'
					),
					array(
						'name'       => 'vehiclecostoverhead',
						'length' 	 => 22,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Vehicle Cost Overhead'
					),
					array(
						'name'       => 'allegrodayrate',
						'length' 	 => 20,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Allegro Day Rate'
					),
					array(
						'name'       => 'agencydayrate',
						'length' 	 => 22,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Agency Day Rate'
					),
					array(
						'name'       => 'wages',
						'length' 	 => 12,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Wages'
					),
					array(
						'name'       => 'fuelcostoverhead',
						'length' 	 => 22,
						'align'		 => 'right',
						'showInView' => false,
						'label' 	 => 'Fuel Cost Overhead'
					),
					array(
						'name'       => 'customercostpermile',
						'length' 	 => 22,
						'showInView' => false,
						'align'		 => 'right',
						'label' 	 => 'Customer Cost Per Mile'
					),
					array(
						'name'       => 'items',
						'length' 	 => 12,
						'align'		 => 'right',
						'label' 	 => 'Items'
					),
					array(
						'name'       => 'pallets',
						'length' 	 => 12,
						'datatype'	 => 'integer',
						'align'		 => 'right',
						'label' 	 => 'Pallets'
					),
					array(
						'name'       => 'weight',
						'length' 	 => 12,
						'datatype'	 => 'double',
						'align'		 => 'right',
						'label' 	 => 'Weight'
					),
					array(
						'name'       => 'rate',
						'length' 	 => 12,
						'datatype'	 => 'double',
						'readonly'	 => ! isUserInRole("ADMIN"),
						'align'		 => 'right',
						'label' 	 => 'Rate'
					),
					array(
						'name'       => 'miles',
						'length' 	 => 12,
						'datatype'	 => 'double',
						'align'		 => 'right',
						'label' 	 => 'Distance (miles)'
					),
					array(
						'name'       => 'duration',
						'length' 	 => 12,
						'datatype'	 => 'double',
						'align'		 => 'right',
						'label' 	 => 'Duration'
					),
					array(
						'name'       => 'charge',
						'datatype'	 => 'double',
						'length' 	 => 12,
						'align'		 => 'right',
						'readonly'	 => ! isUserInRole("ADMIN"),
						'label' 	 => 'Charge'
					),
					array(
						'name'       => 'memberid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Logged By',
						'table'		 => 'members',
						'table_id'	 => 'member_id',
						'alias'		 => 'fullname',
						'table_name' => 'fullname'
					),
					array(
						'name'       => 'notes',
						'length' 	 => 50,
						'type'		 => 'TEXTAREA',
						'showInView' => false,
						'label' 	 => 'Notes'
					)
				);
			
			$this->subapplications = array(
					array(
						'title'		  => 'Map',
						'imageurl'	  => 'images/map.png',
						'script' 	  => 'showMap'
					),
					array(
						'title'		  => 'Route',
						'imageurl'	  => 'images/map.png',
						'application' => 'managebookinglegs.php'
					),
					array(
						'title'		  => 'Delivery Note',
						'imageurl'	  => 'images/print.png',
						'script' 	  => 'printDeliveryNote'
					),
					array(
						'title'		  => 'Copy',
						'imageurl'	  => 'images/copy.png',
						'script' 	  => 'copy'
					),
										array(
						'title'		  => 'Documents',
						'imageurl'	  => 'images/document.gif',
						'script' 	  => 'editDocuments'
					)
				);
				
			$this->messages = array(
					array('id'		  => 'copy_id'),
					array('id'		  => 'copy_date'),
					array('id'		  => 'copy_time'),
					array('id'		  => 'copy_vehicleid')
				);
		}
		
		public function editScreenSetup() {
			include("bookingform.php");
		}
		
		public function postAddScriptEvent() {
?>
			$(".pointcontainer").remove();
			
			counter = 1;
			
			addPoint();
			 
			$("#profitmargin").val("<?php echo getSiteConfigData()->defaultprofitmargin; ?>");
			$("#fromplace").val("<?php echo getSiteConfigData()->basepostcode; ?>").trigger("change");
			$("#toplace").val("<?php echo getSiteConfigData()->basepostcode; ?>").trigger("change");
			$("#startdatetime").val("<?php echo date("d/m/Y"); ?>");
			$("#startdatetime_time").val("<?php echo date('H:i'); ?>");
			$("#enddatetime").val("<?php echo date("d/m/Y"); ?>");
			$("#enddatetime_time").val("<?php echo date('H:i'); ?>");
			$("#agencydriver").val("N");
			$("#rate").val("0.00");
			$("#charge").val("0.00");
			$("#customercostpermile").val("0.00");
			$(".drivernamerow").hide();
			$("#memberid").val("<?php echo getLoggedOnMemberID(); ?>");
			$("#memberid").attr("disabled", true);
			$("#statusid").val("1");
			$("#worktypeid").val("1");
			$("#weight").val("0.00");
<?php
		}
		
		public function postEditScriptEvent() {
?>
			$(".pointcontainer").remove();
			$("#memberid").attr("disabled", true);
			
			driverid_onchange();
			
			counter = 1;
			
			loadLegs(id);
<?php			
		}
	
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
?>
			<script type="text/javascript" src="js/html2canvas.js"></script>
			<script type="text/javascript" src="bookingscriptlibrary-20160710.js"></script>
			<link href="bookingform.css" rel="stylesheet" type="text/css" />
			<style>
				#dateswitch {
					position: absolute;
					top: 38px;
					left: 900px;
					width:200px;
					height:32px;
				}
				#cleardate {
					width:16px;
					height:16px;
				}
			</style>
			<div id="dateswitch">
				<span>Date</span>
				<input class="datepicker" id="switchdate" name="switchdate" value="<?php if (isset($_GET['date'])) echo str_replace("-", "/", $_GET['date']); ?>" />
				<span id="cleardate"><img src='images/delete.png' /></span>
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
			<div id="mapDialog" class="modal">
     			<div id="map_canvas" style="width:780px;height:500px; border:1px solid grey; ">
				</div>
				<br>
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
					</table>
				</form>
			</div>
<?php
		}

		public function postInsertEvent() {
			$id = mysql_insert_id();
			
			for ($i = 1; ; $i++) {
				if (isset($_POST['point_' . $i])) {
					$point = $_POST['point_' . $i];
					$pointlat = $_POST['point_' . $i . "_lat"];
					$pointlng = $_POST['point_' . $i . "_lng"];
					$pointdate = convertStringToDate($_POST['pointdate_' . $i]);
					$pointtime = $_POST['pointtime_' . $i];
					$pointdate = $pointdate . " " . $pointtime;
					$phone = $_POST['point_' . $i . "_phone"];
					$reference = $_POST['point_' . $i . "_ref"];
					
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
							(
							bookingid, place, place_lng, place_lat, departuretime, phone, reference
							)
							VALUES
							(
							$id, '$point', $pointlng, $pointlat, '$pointdate', '$phone', '$reference'
							)";
					$result = mysql_query($sql);
			
					if (! $result) {
						logError($sql . " - " . mysql_error());
					}
					
				} else {
					break;
				}
			}
			
			$legsummary = getJourneyDescription($id);
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
					legsummary = '$legsummary' 
					WHERE id = $id";
			
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
		}
		
		public function postUpdateEvent($id) {
			$sql = "DELETE FROM {$_SESSION['DB_PREFIX']}bookingleg WHERE bookingid = $id";
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$counter = 0;
			$points = $_POST['bookingpoints'];
			
			for ($i = 1; $counter < $points; $i++) {
				if (isset($_POST['point_' . $i])) {
					$counter++;
					
					$point = $_POST['point_' . $i];
					$pointlat = $_POST['point_' . $i . "_lat"];
					$pointlng = $_POST['point_' . $i . "_lng"];
					$pointdate = convertStringToDate($_POST['pointdate_' . $i]);
					$pointtime = $_POST['pointtime_' . $i];
					$pointdate = $pointdate . " " . $pointtime;
					$phone = $_POST['point_' . $i . "_phone"];
					$reference = $_POST['point_' . $i . "_ref"];
					
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg 
							(
							 	bookingid, place, place_lng, place_lat, departuretime, phone, reference
							)
							VALUES
							(
							 	$id, '$point', $pointlng, $pointlat, '$pointdate', '$phone', '$reference'
							)";
					$result = mysql_query($sql);
						
					if (! $result) {
						logError($sql . " - " . mysql_error());
					}
						
				} else {
					break;
				}
			}
			
			$legsummary = getJourneyDescription($id);
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
					legsummary = '$legsummary' 
					WHERE id = $id";
			
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
		}
		
		public function postScriptEvent() {
?>
			var counter = 1;
			var currentID = null;
			var directionsService;
			var directionsDisplay;
		
			var map = null;
		      
			function editDocuments(node) {
				viewDocument(node, "addbookingdocument.php", node, "bookingdocs", "bookingid");
			}
		    
			function saveMapToDataUrl() {
			
			    var element = $("#map_canvas");
			
			    html2canvas(element, {
			        useCORS: true,
			        onrendered: function(canvas) {
			            var dataUrl= canvas.toDataURL("image/png");
			
			            window.open(dataUrl);
			        }
			    });
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
						        totalDuration += <?php echo getSiteConfigData()->averagewaittime * 60; ?>;
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
      	    
      	    function calculateRate2() {
      	    	calculateRate(
      	    			<?php echo getSiteConfigData()->defaultwagesmargin; ?>, 
      	    			<?php echo getSiteConfigData()->defaultprofitmargin; ?>
      	    		);
          	}
		    
			$(document).ready(function() {
					$("#crudaddbutton span").html("New Booking");

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
					
					$("#switchdate").change(
							function() {
								navigate("<?php echo $_SERVER['PHP_SELF']; ?>?date=" + $(this).val().replace(/\//g, '-'));
							}
						);
						
					$("#cleardate").click(
							function() {
								navigate("<?php echo $_SERVER['PHP_SELF']; ?>");
							}
						);
			
					$("#mapDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Map",
							buttons: {
								"Close": function() {
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
										post("editform", "copybooking", "submitframe", 
												{ 
													copy_id: currentID, 
													copy_date: $("#copydate").val(),
													copy_time: $("#copytime").val(),
													copy_vehicleid: $("#copyvehicleid").val()
												}
											);
																				
										$(this).dialog("close");
									}
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
				});
				
			function printDeliveryNote(id) {
				window.open("deliverynotereport.php?id=" + id);
			}
			
			function copy(id) {
				currentID = id;
				
				$("#copyDialog").dialog("open");
			}
			
			function route(id) {
				$("#mapDialog").dialog("open");
			}
			
			function convertDate(str) {
				return str.substring(6, 10) + "-" + str.substring(3, 5) + "-" + str.substring(0, 2);
			}
		
			function validateForm() {
				var sql;
				var isvalid = true;
				var ucstartdate = ($("#startdatetime").val()) + " " + $("#startdatetime_time").val();
				var ucenddate = ($("#enddatetime").val()) + " " + $("#enddatetime_time").val();
				var startdate = convertDate($("#startdatetime").val()) + " " + $("#startdatetime_time").val();
				var enddate = convertDate($("#enddatetime").val()) + " " + $("#enddatetime_time").val();

				if ($("#vehicleid").val() != "0") {
					if ($("#editform #crudcmd").val() == "update") {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.id != '" + $("#crudid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND   A.vehicleid = '" + $("#vehicleid").val() + "' " +
							  "AND ((A.startdatetime >= '" + enddate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + enddate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + enddate + "' AND A.enddatetime >= '" + enddate + "')) ";
								  
					} else {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.vehicleid = '" + $("#vehicleid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
					}
	
					callAjax(
							"finddata.php", 
							{ 
								sql: sql
							},
							function(data) {
								if (data.length > 0) {
									pwAlert("Warning: Vehicle already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
								}
							},
							false
						);
						
					if (! isvalid) {
						return false;
					}
				}
				
				if ($("#driverid").val() != "0") {
					if ($("#editform #crudcmd").val() == "update") {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.id != '" + $("#crudid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND   A.driverid = '" + $("#driverid").val() + "' " +
							  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
								  
					} else {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.driverid = '" + $("#driverid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
					}
					
					callAjax(
							"finddata.php", 
							{ 
								sql: sql
							},
							function(data) {
								if (data.length > 0) {
									pwAlert("Warning: Driver already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
								}
							},
							false
						);
						
					if (! isvalid) {
						return false;
					}
				}
				
				if ($("#trailerid").val() != "0" &&  $("#trailerid option[value=" +  $("#trailerid").val() + "]"). text() != "N/A") {
					if ($("#editform #crudcmd").val() == "update") {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.id != '" + $("#crudid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND   A.trailerid = '" + $("#trailerid").val() + "' " +
							  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
								  
					} else {
						sql = "SELECT A.startdatetime, A.enddatetime FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							  "WHERE A.trailerid = '" + $("#trailerid").val() + "' " +
							  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
							  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
							  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
							  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
					}
					
					callAjax(
							"finddata.php", 
							{ 
								sql: sql
							},
							function(data) {
								if (data.length > 0) {
									pwAlert("Warning: Trailer already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
									isvalid = false;
								}
							},
							false
						);
						
					if (! isvalid) {
						return false;
					}
				}
					
				return true;
			}
			
			function bookingReference(node) {
				return "<?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.id, 6);
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
								
								for (var i = 0; i < data.length; i++) {
									var node = data[i];
									
									if (node.place != null) {
										waypoints.push({
												stopover: true,
												location: node.place
											});
									}
								}
								
								initializeMap(node.fromplace, node.toplace, waypoints, 0);
								  
								$("#mapDialog").dialog("open");
								
//								saveMapToDataUrl();
							}
						
						}
					);
			
			}
<?php			
		}
	}
?>
