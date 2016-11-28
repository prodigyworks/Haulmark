<?php
	require_once("crud.php");
	require_once("bookingshared.php");
	require_once("bookingscopy.php");
	require_once("businessobjects/BookingLegClass.php");
	
	function confirmBooking() {
		$id = $_POST['copy_id'];
		$sql = "SELECT customerid 
				FROM {$_SESSION['DB_PREFIX']}booking
				WHERE id = $id";
		
		$result = mysql_query($sql);
		
		if (! $result) {
			throw new Exception(mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			$message = "Booking " . getBookingReference($id) . " has been confirmed";
			
			sendCustomerMessage($member['customerid'], "Booking acknowledgement", $message);
		}
		
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
				confirmed = 'Y'
				WHERE id = $id";
		
		if (! mysql_query($sql)) {
			throw new Exception(mysql_error());
		}
	}
	
	function copybooking() {
		createNewBookingFromExisting(
				$_POST['copy_id'], 
				convertStringToDate($_POST['copy_date']) . " " . $_POST['copy_time'],
				$_POST['copy_vehicleid'],
				$_POST['copy_ordernumber']
			);
	}
	
	class BookingCrud extends Crud {
		
		public function __construct() {
			parent::__construct();
			
			$and = "";
			
			if (isset($_GET['date'])) {
				$date = convertStringToDate($_GET['date']);
				$and = "AND (DATE(A.startdatetime) = '$date' OR DATE(A.enddatetime) = '$date') ";
			}
			
			$this->validateForm = "validateCrudForm";
			$this->onClickCallback = "checkClick";
			$this->title = "Bookings";
			$this->table = "{$_SESSION['DB_PREFIX']}booking";
			$this->allowView = true;
			$this->dialogwidth = 1050;
			$this->document = array(
					'primaryidname'	 => 	"bookingid",
					'tablename'		 =>		"bookingdocs"
				);
			$this->sql = 
				   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
				    E.registration AS vehiclename, F.name AS vehicletypename, 
				    H.name AS statusname, I.fullname, J.name AS worktypename,
				    L.name AS nominalledgercodename,
				    (
				    	SELECT M.arrivaltime 
				    	FROM {$_SESSION['DB_PREFIX']}bookingleg M  
				    	WHERE M.bookingid = A.id
				    	ORDER BY M.id
				    	LIMIT 1
				    ) AS startlegdatetime
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
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}nominalledgercode L 
					ON L.id = A.nominalledgercodeid
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
						'filter'	 => false,
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
						'name'       => 'nominalledgercodeid',
						'type'       => 'DATACOMBO',
						'length' 	 => 20,
						'showInView' => false,	
						'required'   => true,
						'label' 	 => 'Nominal Ledger Code',
						'table'		 => 'taxcode',
						'table_id'	 => 'id',
						'alias'		 => 'nominalledgercodename',
						'table_name' => 'name'
					),
					array(
						'name'       => 'drivername',
						'showInView' => false,
						'length' 	 => 20,
						'label' 	 => 'Driver Name'
					),
					array(
						'name'       => 'agencyvehicleregistration',
						'showInView' => false,
						'length' 	 => 20,
						'label' 	 => 'Registration'
					),
					array(
						'name'       => 'driverphone',
						'showInView' => false,
						'length' 	 => 20,
						'label' 	 => 'Driver Phone'
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
						'name'       => 'collectionRef',
						'function'   => 'collectionReference',
						'sortcolumn' => 'A.startdatetime',
						'type'		 => 'DERIVED',
						'length' 	 => 18,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Collection Date'
					),
					array(
						'name'       => 'startdatetime',
						'datatype'	 => 'datetime',
						'hidden'	 => true,
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
						'name'       => 'confirmed',
						'length' 	 => 20,
						'label' 	 => 'Confirmed',
						'editable'	 => false,
						'type'       => 'COMBO',
						'options'    => array(
								array(
									'value'		=> 'Y',
									'text'		=> 'Yes'
								),
								array(
									'value'		=> 'N',
									'text'		=> 'No'
								)
							)
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
						'label' 	 => 'Our Day Rate'
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
						'name'       => 'pallets',
						'length' 	 => 12,
						'required'   => false,
						'datatype'	 => 'integer',
						'align'		 => 'right',
						'label' 	 => 'Pallets'
					),
					array(
						'name'       => 'weight',
						'length' 	 => 12,
						'required'   => false,
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
						'name'       => 'fixedprice',
						'type'		 => 'CHECKBOX',
						'length' 	 => 12,
						'showInView' => false,
						'label' 	 => 'Fixed Price'
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
						'name'       => 'ordernumber2',
						'length' 	 => 15,
						'required'	 => false,
						'label' 	 => 'Order Number 2'
					),
					array(
						'name'       => 'bookingtype',
						'length' 	 => 20,
						'label' 	 => 'Source',
						'editable'	 => false,
						'bind'		 => false,
						'type'       => 'COMBO',
						'options'    => array(
								array(
									'value'		=> 'W',
									'text'		=> 'Online Booking'
								),
								array(
									'value'		=> 'M',
									'text'		=> 'Manual Booking'
								)
							)
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
						'id'		  => 'deliverynotebutton',
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
						'title'		  => 'Confirm',
						'imageurl'	  => 'images/thumbs_up.gif',
						'script' 	  => 'confirmBooking'
					)
				);
				
			$this->messages = array(
					array('id'		  => 'copy_id'),
					array('id'		  => 'copy_date'),
					array('id'		  => 'copy_time'),
					array('id'		  => 'copy_vehicleid'),
					array('id'		  => 'copy_ordernumber')
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
			$(".agencyvehiclerow").hide();
			$("#memberid").val("<?php echo getLoggedOnMemberID(); ?>");
			$("#memberid").attr("disabled", true);
			$("#statusid").val("1");
			$("#worktypeid").val("<?php echo getSiteConfigData()->defaultworktype; ?>").trigger("change");
			$("#weight").val("0.00");
			$(".address").html("");
<?php
		}
		
		public function postEditScriptEvent() {
?>
			$(".pointcontainer").remove();
			$("#memberid").attr("disabled", true);
			
			driverid_onchange();
			vehicletypeid_onchange();
			
			$("#editpanel #driverid").val(node.driverid);
			$("#editpanel #vehicleid").val(node.vehicleid);
			
			loadAddress($("#customerid").val());
			
			counter = 1;
			
			loadLegs(id);
<?php			
		}
		
		public function afterInsertRow() {
?>
			var status = rowData['confirmed'];

			if (status == "No") {
				$(this).jqGrid('setRowData', rowid, false, { 'color': 'red' });
		   	}
<?php
		}
		
		/* Post header event. */
		public function postHeaderEvent() {
?>
			<script type="text/javascript" src="js/html2canvas.js"></script>
			<script type="text/javascript" src="bookingscriptlibrary-20161123.js"></script>
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
				<div id="prevPriceDiv" class="crudentryform">
				</div>
			</div>
			<div id="rateCardDialog" class="modal">
				<div>
					<iframe id="rateCardIframe" src="about:blank" frameborder=1 style='width:760px;height:510px'></iframe>
				</div>
			</div>
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
<?php
		}

		public function postInsertEvent($id) {
			for ($i = 1; ; $i++) {
				if (isset($_POST['point_' . $i])) {
					$leg = new BookingLegClass();
					
			    	$leg->bookingid = $id;
					$leg->place = $_POST['point_' . $i];
					$leg->place_lat = $_POST['point_' . $i . "_lat"];
					$leg->place_lng = $_POST['point_' . $i . "_lng"];
					$leg->reference = $_POST['point_' . $i . "_ref"];
					$leg->phone = $_POST['point_' . $i . "_phone"];
					$leg->arrivaltime = convertStringToDate($_POST['pointarrivaldate_' . $i]) . " " . $_POST['pointarrivaltime_' . $i];
					$leg->departuretime = convertStringToDate($_POST['pointdeparturedate_' . $i]) . " " . $_POST['pointdeparturetime_' . $i];
					$leg->insert();
					
				} else {
					break;
				}
			}
			
			$legsummary = getJourneyDescription($id);
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
					legsummary = '$legsummary',
					bookingtype = 'M'
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
					$leg = new BookingLegClass();
					
			    	$leg->bookingid = $id;
					$leg->place = $_POST['point_' . $i];
					$leg->place_lat = $_POST['point_' . $i . "_lat"];
					$leg->place_lng = $_POST['point_' . $i . "_lng"];
					$leg->reference = $_POST['point_' . $i . "_ref"];
					$leg->phone = $_POST['point_' . $i . "_phone"];
					$leg->arrivaltime = convertStringToDate($_POST['pointarrivaldate_' . $i]) . " " . $_POST['pointarrivaltime_' . $i];
					$leg->departuretime = convertStringToDate($_POST['pointdeparturedate_' . $i]) . " " . $_POST['pointdeparturetime_' . $i];
					$leg->insert();
						
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

			function checkClick(node) {
				if (node.statusid == "New Booking") {
					$("#deliverynotebutton").hide();
					
				} else {
					$("#deliverynotebutton").show();
				}				
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
				    
				    var mapOptions = { 
				    		mapTypeId: google.maps.MapTypeId.ROADMAP, 
				    		disableDefaultUI: true
				    	};
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
						var totalDistance = 0;
					    var totalDuration = 0;
					    var duration = 0;
					    var lastLegDuration = 0;
					    var cnt = 0;
					    var prevTime = $("#startdatetime_time"); 
					    var prevDate = $("#startdatetime");
						    
		        		if (status == google.maps.DirectionsStatus.NOT_FOUND) {
		        			pwAlert("Location not found, please provide more detail, e.g. Post code");
		        			
		        		} else if (status == google.maps.DirectionsStatus.OK) {
			        		directionsDisplay.setDirections(response);
							google.maps.event.trigger(map, "resize");
							
						    var legs = response.routes[0].legs;
							var METERS_TO_MILES = 0.000621371;
							
						    for(var i=0; i < legs.length; ++i) {
						        totalDistance += legs[i].distance.value;
						        totalDuration += (legs[i].duration.value / 0.9);
							}
							
							lastLegDuration = legs[legs.length - 1].duration.value;
<?php 
	if (getSiteConfigData()->autotimecalculation == "Y") {
?>					    
							$(".pointcontainer").each(
									function() {
										var location = $(this).find(".point").val();

										if (location != "") {
											var index = parseInt($(this).attr("index"));
											var startdate = $(this).find(".arrivaldate");
											var starttime = $(this).find(".arrivaltime");
											var departuredate = $(this).find(".departuredate");
											var departuretime = $(this).find(".departuretime");
	
											duration = legs[cnt].duration.value;
											
											if (startIndex <= index) {
		    				    				getJourneyTime(
		    				    						prevTime.val(), 
		    				    						prevDate.val(), 
		    				    						startdate.attr("id"), 
		    				    						starttime.attr("id"), 
		    				    						departuredate.attr("id"),
		    				    						departuretime.attr("id"),
		    				    						(duration / 0.9)
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
	        						(lastLegDuration / 0.9)
	    		    			);
<?php 
	}
?>					    
	    		    			
							$('#miles').val((Math.round( totalDistance * METERS_TO_MILES * 10 ) / 10));						    
							$('#duration').val(
									getTotalDuration(
			    		    				$("#startdatetime").val(), 
			    		    				$("#startdatetime_time").val(),
			    		    				$("#enddatetime").val(), 
			    		    				$("#enddatetime_time").val()
										)
								);	
							
							fetchOverHeadRates();					    
						}
					       	
		      		});
		      		
      	    }	
      	    
      	    function getAverageWaitTime() {
      	    	return <?php echo getSiteConfigData()->averagewaittime * 60; ?>;
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
					$("#worktypeid").change(worktypeid_onchange);

					setupEvents();

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
								"Instructions": function() {
									$("#map_steps").slideToggle();
								},
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
													copy_ordernumber: $("#copyordernumber").val(),
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
				
				$("#copyordernumber").val("");
				$("#copyvehicleid").val("0");
				
				$("#copyDialog").dialog("open");
			}
			
			function confirmBooking(id) {
				post("editform", "confirmbooking", "submitframe", 
						{ 
							copy_id: id, 
						}
					);
			}			
			
			function copy(id) {
				currentID = id;
				
				$("#copyordernumber").val("");
				$("#copyvehicleid").val("0");
				
				$("#copyDialog").dialog("open");
			}
			
			function route(id) {
				$("#mapDialog").dialog("open");
			}
		
			function validateCrudForm() {
				return validateForm(
						$("#editform #crudcmd").val() == "update" 
							? $("#crudid").val()
							: null
					);
			}
			
			function collectionReference(node) {
				if (node.fromplace == "<?php echo getSiteConfigData()->basepostcode; ?>" && node.startlegdatetime != null) {
					return node.startlegdatetime;
				}
				
				return node.startdatetime;
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
								var node = data[0];
								
								for (var i = 0; i < data.length - 1; i++) {
									node = data[i];
									
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
<?php			
		}
	}
?>
