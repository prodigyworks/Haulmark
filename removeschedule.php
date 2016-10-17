<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$id = $_POST['id'];
	
	$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}bookingleg 
			WHERE bookingid = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error());
	}
	
	$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}booking
			WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error());
	}
	
	mysql_query("COMMIT");
?>