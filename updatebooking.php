<?php
	require_once("system-db.php");
	
	start_db();
	
	$id = $_POST['id'];
	$startDate = convertStringToDateTime($_POST['startdate']);
	$endDate = convertStringToDateTime($_POST['enddate']);
	$sectionid = $_POST['sectionid'];
	
	logError($_POST['startdate'] . " - " . $startDate, false);
	logError($endDate, false);
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET " .
			"startdatetime = '$startDate', " .
			"enddatetime = '$endDate', " .
			"vehicleid = '$sectionid' " .
			"WHERE id = $id ";
	
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
?>
