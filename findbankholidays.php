<?php
	//Include database connection details
	require_once("sqlprocesstoarray.php");
	
	$json = new SQLProcessToArray();

	$startDate = convertStringToDate($_POST['startdate']) ;
	$endDate = convertStringToDate($_POST['enddate']);
	
	$qry = "SELECT * FROM {$_SESSION['DB_PREFIX']}bankholiday A " .
			"WHERE (startdate >= '$startDate' AND startDate <= '$endDate') " .
			"OR (enddate >= '$startDate' AND endDate <= '$endDate')";
	
	echo json_encode($json->fetch($qry));
?>