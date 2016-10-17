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
			departuretime = DATE_ADD(departuretime, INTERVAL $minutes MINUTE)
			WHERE bookingid = $id ";

	$result = mysql_query($sql);
	
	if (! $result) {
		logError($sql . " = " . mysql_error());
	}
	
	mysql_query("COMMIT");
?>
