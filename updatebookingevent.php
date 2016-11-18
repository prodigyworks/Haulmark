<?php
	require_once("system-db.php");
	require_once("bookingshared.php");
	require_once("invoiceemail.php");
	
	start_db();
	
	$id = $_POST['id'];
	$startdatetime = convertStringToDateTime($_POST['startdate']);
	$enddatetime = convertStringToDateTime($_POST['enddate']);
	$sectionid = $_POST['sectionid'];
	$mode = $_POST['mode'];
	
	if ($mode == "V") {
		$column = "vehicleid";

	} else if ($mode == "D") {
		$column = "driverid";

	} else if ($mode == "T") {
		$column = "trailerid";
	}
	
	$sql = "SELECT TIMESTAMPDIFF(MINUTE, startdatetime, '$startdatetime') AS minutes
			FROM {$_SESSION['DB_PREFIX']}booking 
			WHERE id = $id";
	
	$result = mysql_query($sql);
	
	if(! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$minutes = $member['minutes'];
	}
	
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			startdatetime = DATE_ADD(startdatetime, INTERVAL $minutes MINUTE),
			enddatetime = '$enddatetime',
			$column = $sectionid
			WHERE id = $id";

	$result = mysql_query($sql);
	
	if (! $result) {
		logError($sql . " = " . mysql_error());
	}
	
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}bookingleg SET 
			departuretime = DATE_ADD(departuretime, INTERVAL $minutes MINUTE),
			arrivaltime = DATE_ADD(arrivaltime, INTERVAL $minutes MINUTE)
			WHERE bookingid = $id ";

	$result = mysql_query($sql);
	
	if (! $result) {
		logError($sql . " = " . mysql_error());
	}
		
	mysql_query("COMMIT");
	
	$sql ="SELECT (TIMEDIFF(A.enddatetime, A.startdatetime) / 10000) AS totalhours,
		   B.registration AS vehiclename,
		   C.registration AS trailername,
		   D.name AS drivername
		   FROM {$_SESSION['DB_PREFIX']}booking A
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle B
		   ON B.id = A.vehicleid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer C
		   ON C.id = A.trailerid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver D
		   ON D.id = A.driverid
		   WHERE A.id = $id";
	$result = mysql_query($sql);
	
	$totalhours = 0;
	$utilisationhours = 0;
	$vehiclename = "";
	$trailername = "";
	$drivername = "";
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$totalhours = $member['totalhours'];
			$vehiclename = $member['vehiclename'];
			$trailername = $member['trailername'];
			$drivername = $member['drivername'];
		}
	}
	
	$sql ="SELECT (TIMEDIFF(B.enddatetime, A.departuretime) / 10000) AS utilisationhours
		   FROM {$_SESSION['DB_PREFIX']}bookingleg A 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}booking B 
		   ON B.id = A.bookingid
		   WHERE A.bookingid = $id
		   ORDER BY A.id DESC
		   LIMIT 1";
	$itemresult = mysql_query($sql);
	
	//Check whether the query was successful or not
	if($itemresult) {
		while (($itemmember = mysql_fetch_assoc($itemresult))) {
			$utilisationhours = $itemmember['utilisationhours'];
		}
	}
	
	$hours = number_format(($utilisationhours * (100 / $totalhours)), 0);
	$lefthours = 100 - $hours;
	
	$tooltip = "Booking: " . getBookingReference($id) . "\n" . 
			   "Vehicle: " . $vehiclename . "\n" . 
			   "Trailer: " . $trailername . "\n" .
			   "Driver: " . $drivername;
	
	echo json_encode(
			array(
					"tooltip" => $tooltip,
					"lefthours" => $lefthours,
					"hours" => $hours,
					"utilisationhours" => $utilisationhours
				)
		);
?>
