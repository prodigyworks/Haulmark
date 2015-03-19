<?php
	require_once("crud.php");
	
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
					ordernumber, ordernumber2, drivername, storename, fromplace, toplace, 
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
					ordernumber, ordernumber2, drivername, storename, fromplace, toplace, 
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
						'name'       => 'customerid',
						'type'       => 'DATACOMBO',
						'length' 	 => 35,
						'label' 	 => 'Customer',
						'table'		 => 'customer',
						'table_id'	 => 'id',
						'alias'		 => 'customername',
						'table_name' => 'name'
					),
					array(
						'name'       => 'driverid',
						'type'       => 'DATACOMBO',
						'length' 	 => 34,
						'label' 	 => 'Driver / Agency',
						'table'		 => 'driver',
						'table_id'	 => 'id',
						'required'	 => false,
						'alias'		 => 'driversname',
						'table_name' => 'name'
					),
					array(
						'name'       => 'vehicletypeid',
						'type'       => 'DATACOMBO',
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
						'name'       => 'vehicleid',
						'type'       => 'DATACOMBO',
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
						'name'       => 'trailerid',
						'type'       => 'DATACOMBO',
						'length' 	 => 10,
						'required'   => false,
						'label' 	 => 'Trailer',
						'table'		 => 'trailer',
						'required'	 => false,
						'table_id'	 => 'id',
						'alias'		 => 'trailername',
						'table_name' => 'registration'
					),
					array(
						'name'       => 'drivername',
						'length' 	 => 20,
						'label' 	 => 'Driver Name'
					),
					array(
						'name'       => 'storename',
						'length' 	 => 10,
						'label' 	 => 'Store Name'
					),
					array(
						'name'       => 'worktypeid',
						'type'       => 'DATACOMBO',
						'length' 	 => 19,
						'label' 	 => 'Work Type',
						'table'		 => 'worktype',
						'required'	 => true,
						'table_id'	 => 'id',
						'alias'		 => 'worktypename',
						'table_name' => 'name'
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
						'label' 	 => 'Charge'
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
			$("#drivernamerow").hide();
			$("#driverstorerow").hide();
			$("#memberid").val("<?php echo getLoggedOnMemberID(); ?>");
			$("#statusid").val("1");
			$("#worktypeid").val("1");
			$("#weight").val("0.00");
<?php
		}
		
		public function postEditScriptEvent() {
?>
			$(".pointcontainer").remove();
			
			driverid_onchange();
			
			counter = 1;
			
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT B.id, A.fromplace, A.fromplace_ref, A.fromplace_phone, A.toplace, A.toplace_ref, A.toplace_phone, B.place, B.place_lng, place_lat, B.reference, B.phone, " +
							 "DATE_FORMAT(B.departuretime, '%d/%m/%Y') AS departuredate, " +
							 "DATE_FORMAT(B.departuretime, '%H:%i') AS departuretime " + 
							 "FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
							 "INNER JOIN <?php echo $_SESSION['DB_PREFIX'];?>bookingleg B " + 
							 "ON B.bookingid = A.id " +
							 "WHERE A.id = " + id + " " +
							 "ORDER BY B.id"
					},
					function(data) {
						if (data.length > 0) {
							for (var i = 1; i <= data.length; i++) {
								var node = data[i - 1];
								
								addPoint();
								
								$("#point_" + i).val(node.place);
								$("#point_" + i + "_lat").val(node.place_lat);
								$("#point_" + i + "_lng").val(node.place_lng);
								$("#point_" + i + "_ref").val(node.reference);
								$("#point_" + i + "_phone").val(node.phone);
								$("#pointdate_" + i).val(node.departuredate);
								$("#pointtime_" + i).val(node.departuretime.trim());
							}
						}
					},
					false
				);
<?php			
		}
	
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
?>
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
								<?php createCombo("copyvehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "", false); ?>
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
			$legsummary = $_POST['fromplace'];
			
			for ($i = 1; ; $i++) {
				if (isset($_POST['point_' . $i])) {
					$point = $_POST['point_' . $i];
					$pointlat = $_POST['point_' . $i . "_lat"];
					$pointlng = $_POST['point_' . $i . "_lng"];
					$pointdate = convertStringToDate($_POST['pointdate_' . $i]);
					$pointtime = $_POST['pointtime_' . $i];
					$pointdate = $pointdate . " " . $pointtime;
					
					$legsummary .= " -> ";
					$legsummary .= $point;
						
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
			$legsummary = $_POST['fromplace'];
			
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
					
					$legsummary .= " -> ";
					$legsummary .= $point;
					
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
		    var pointoptions = {
		    		types: ['(cities)'],
		    		componentRestrictions: {country: ["uk"]}       
		    	};
		
			var directionsService,
		      directionsDisplay,
		      map = null;
		      
			function editDocuments(node) {
				viewDocument(node, "addbookingdocument.php", node, "bookingdocs", "bookingid");
			}
			
		    function getJourneyTime(startTime, startDate, nextDate, elapsedTime) {
			    var pointmin2 = Math.round( elapsedTime / 60 ) % 60;
			    var pointhr2 = Math.floor( elapsedTime / 3600 );
		    	var prevhr = startTime.trim().substr(0, 2);
		    	var prevmin = startTime.trim().substr(3, 5);
		    	var legtotal = (prevhr * 3600) + (prevmin * 60);
		    	var timeTaken = elapsedTime + parseInt(legtotal);
			    var pointmin = Math.round( timeTaken / 60 ) % 60;
			    var pointhr = (Math.floor( timeTaken / 3600 ) % 24);
			    var dateadd = (Math.floor( (timeTaken / 3600 ) / 24));
			    
			    if (dateadd > 0) {
				    var strDate = startDate.split('/');
			    	var date = new Date(strDate[2], strDate[1] - 1, strDate[0]);
			    	
			    	date.setTime(date.getTime() + (dateadd * 24 * 60 * 60 * 1000));
			    	
			    	$("#" + nextDate).val(
			    			padZero(date.getDate()) + "/" + 
			    			padZero(date.getMonth() + 1) + "/" + 
			    			date.getFullYear()
			    		);
			    	
			    } else {
			    	$("#" + nextDate).val(startDate);
			    }
			    
		    	return padZero(pointhr) + ":" + padZero(pointmin);
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
						        totalDuration += legs[i].duration.value;
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
		    					    						legs[cnt++].duration.value + <?php echo getSiteConfigData()->averagewaittime * 60; ?>
		    					    					)
		    					    			);
										}
										
									    prevTime = starttime; 
									    prevDate = startdate;
									}
								);
						    
						       	
    			    		$('#enddatetime_time').val(
    			    				getJourneyTime(
    			    						prevTime.val(), 
    			    						prevDate.val(), 
    			    						"enddatetime", 
    			    						legs[legs.length - 1].duration.value
    			    					)
    			    			);
							
							var mins = Math.round( totalDuration / 60 ) % 60;
							var hours = Math.round( totalDuration / 3600 );
							
							$('#miles').val((Math.round( totalDistance * METERS_TO_MILES * 10 ) / 10));						    
							$('#duration').val(hours + "." + mins);	
							
							vehicletypeid_onchange();					    
		        		}
		      		});
		      		
      	    }			
		    
			$(document).ready(function() {
					$("#customerid").change(customerid_onchange);
					$("#vehicleid").change(vehicleid_onchange);
					$("#vehicletypeid").change(vehicletypeid_onchange);
					$("#agencydayrate").change(calculateRate);
					$("#allegrodayrate").change(calculateRate);
					$("#vehiclecostoverhead").change(calculateRate);
					$("#fuelcostoverhead").change(calculateRate);
					$("#maintenanceoverhead").change(calculateRate);
					$("#wages").change(calculateRate);
					$("#driverid").change(driverid_onchange);
			
					$("#mapDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Map",
							buttons: {
								Ok: function() {
									$(this).dialog("close");
								}
							}
						});
			
					$("#copyDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 400,
							title: "Copy",
							buttons: {
								Ok: function() {
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
		
			function removePoint(node) {
				var parent = $(node).parent();
				var grandparent = parent.parent();
		
				$("#" + parent.attr("id")).remove();
			    $("#bookingpoints").val($(".pointcontainer").length);
			}
			
			function calculatePoint() {
				calculateTimeNode(this, 0);
			}
		
			function calculateTime() {
				calculateTimeNode(this, 1);
			}
			
			function calculateTimeNode(node, addition) {
				node = $(node);

				setTimeout(
						function() { 
							getLatLng(node.attr("id"), node.val());
		
							var fromIndex = parseInt(node.attr("index"));
							var waypoints = [];
							  
							$(".pointcontainer .point").each(
									function() {
										waypoints.push({
											stopover: true,
											location: $(this).val()
										});
									}
								);
								
							initializeMap($("#fromplace").val(), $("#toplace").val(), waypoints, fromIndex + addition);
							
						},
						1000
					);
		
			}
			
			function addPointBetweenNodes() {
				var prevTime = $("#startdatetime_time").val();
				var prevDate = $("#startdatetime").val();
				
				$(".pointcontainer:last").each(
						function() {
							prevTime = $(this).find(".timepicker").val();
							prevDate = $(this).find(".datepicker").val();
						}
					);
					
				var index = addPoint();
				
				$("#pointdate_" + index).val(prevDate);
				$("#pointtime_" + index).val(prevTime);
			}
			
			function addPoint() {
				var html = "<div id='container_" + counter + "' class='pointcontainer' index='" + counter + "' style='padding-top:3px'>\n" +
				   		   "	<input class='point' id='point_" + counter  + "' index='" + counter + "' required='true' type='text' style='width:300px' name='point_" + counter + "'>&nbsp;\n" +
						   "	<div class='bubble' title='Required field'></div>\n" +
						   "	<input class='datepicker' required='true' index='" + counter + "' type='text' id='pointdate_" + counter +  "' name='pointdate_" + counter + "'>\n" +
						   "	<div class='bubble' title='Required field'></div>\n" +
						   "	<input class='timepicker' required='true' index='" + counter + "' type='text' id='pointtime_" + counter + "' name='pointtime_" + counter + "'>\n" +
						   "	<div class='bubble' title='Required field'></div>\n" +
				   		   "	<input id='point_" + counter  + "_lng' type='hidden' name='point_" + counter + "_lng'>\n" +
				   		   "	<input id='point_" + counter  + "_lat' type='hidden' name='point_" + counter + "_lat'>\n" +
				   		   "    <input type='text' style='width:200px' id='point_" + counter + "_ref' name='point_" + counter + "_ref'>\n" +
						   "	<input type='text' style='width:80px' id='point_" + counter + "_phone' name='point_" + counter + "_phone'>\n" +
						   "	<img src='images/minus.gif' onclick='removePoint(this)'></img>" +
						   "</div>";
		
				$("#tolocationdiv").append(html);
				$("#pointdate_" + counter).datepicker({dateFormat: "dd/mm/yy"});
				$("#pointtime_" + counter).timepicker();
				
			    var input = document.getElementById('point_' + counter);
			    new google.maps.places.Autocomplete(input, pointoptions);
			    var pacContainerInitialized = false; 
			    
		        $('#point_' + counter).keypress(function() { 
		    	        if (! pacContainerInitialized) { 
		       	            $('.pac-container').css('z-index', '9999'); 
		           	        pacContainerInitialized = true; 
			           	} 
		    		}); 
		
		
				$('#point_' + counter).change(calculatePoint);
				$('#pointdate_' + counter).change(calculateTime);
				$('#pointtime_' + counter).change(calculateTime);
		
			    counter++;
			    
			    $("#bookingpoints").val($(".pointcontainer").length);
			    
			    return counter - 1;
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
				
				if ($("#trailerid").val() != "0") {
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
			
			function calculateRate() {
				var duration = $("#duration").val();
				var dayrate;
				
				if ($("#agencydriver").val() == "Y") {
					dayrate = parseFloat($("#agencydayrate").val());
					
				} else {
					dayrate = parseFloat($("#allegrodayrate").val());
				}
				
				var wages = (duration * dayrate) * 1.<?php echo str_replace(".", "", getSiteConfigData()->defaultwagesmargin); ?>;
				var miles = parseFloat($("#miles").val());
				var totalcost = 
						wages + 
						((parseFloat($("#vehiclecostoverhead").val()) + 
						  parseFloat($("#fuelcostoverhead").val()) + 
						  parseFloat($("#maintenanceoverhead").val())) * 
						  miles
						);
				
				$("#wages").val(wages);

				if ($("#customercostpermile").val() != "0.00" && $("#customercostpermile").val() != "") {
					totalcost = parseFloat($("#customercostpermile").val()) * miles;
					
					if (isNaN(totalcost)) {
						totalcost = 0;
					}
					
					$("#charge").val(new Number(totalcost).toFixed(2));
					
				} else {
					if (isNaN(totalcost)) {
						totalcost = 0;
					}
					
					$("#charge").val(new Number(totalcost * 1.<?php echo str_replace(".", "", getSiteConfigData()->defaultprofitmargin); ?>).toFixed(2));
				}
				
				$("#rate").val(new Number(totalcost).toFixed(2));
				$("#charge").val(new Number(totalcost * 1.<?php echo str_replace(".", "", getSiteConfigData()->defaultprofitmargin); ?>).toFixed(2));
			}
			
			function driverid_onchange() {
				$("#drivernamerow").hide();
				$("#driverstorerow").hide();

				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT agencydriver, usualvehicleid, usualtrailerid FROM <?php echo $_SESSION['DB_PREFIX'];?>driver WHERE id = " + $("#driverid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$("#agencydriver").val(node.agencydriver);
								
								if (node.usualvehicleid != null && node.usualvehicleid != 0) {
									$("#vehicleid").val(node.usualvehicleid).trigger("change");
								}
								
								if (node.usualtrailerid != null && node.usualtrailerid != 0) {
									$("#trailerid").val(node.usualtrailerid).trigger("change");
								}
								
								calculateRate();
								
								if (node.agencydriver == "Y") {
									$("#drivernamerow").show();
									$("#driverstorerow").show();
				
								} else {
									$("#drivernamerow").hide();
									$("#driverstorerow").hide();
									$("#drivername").val("");
									$("#storename").val("");
								}				
							}
						},
						false
					);

			}

			function vehicletypeid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.* FROM <?php echo $_SESSION['DB_PREFIX'];?>vehicletype A " + 
								 "WHERE A.id = " + $("#vehicletypeid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$("#allegrodayrate").val(node.allegrodayrate);
								$("#agencydayrate").val(node.agencydayrate);
								$("#vehiclecostoverhead").val(node.vehiclecostpermile);
								$("#fuelcostoverhead").val(node.fuelcostpermile);
								$("#maintenanceoverhead").val(node.overheadcostpermile);
								
								$.ajax({
										url: "createvehiclecombo.php",
										dataType: 'html',
										async: false,
										data: { 
											vehicletypeid: $("#vehicletypeid").val()
										},
										type: "POST",
										error: function(jqXHR, textStatus, errorThrown) {
											pwAlert("ERROR :" + errorThrown);
										},
										success: function(data) {
											$("#vehicleid").parent().html(data);
										}
									});
								
								calculateRate();
							}
						
						}
					);
			
				
			}
			
			function vehicleid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.vehicletypeid FROM <?php echo $_SESSION['DB_PREFIX'];?>vehicle A " +
								 "WHERE A.id = " + $("#vehicleid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$("#vehicletypeid").val(node.vehicletypeid).trigger("change");
							}
						
						}
					);
			}
			
			function customerid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.collectionpoint, A.standardratepermile FROM <?php echo $_SESSION['DB_PREFIX'];?>customer A " +
								 "WHERE A.id = " + $("#customerid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$("#point_1").val(node.collectionpoint).trigger("change");
								$("#customercostpermile").val(node.standardratepermile);
								
								calculateRate();
							}
						
						}
					);
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
							}
						
						}
					);
			
			}
<?php			
		}
	}
?>
