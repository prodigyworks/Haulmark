<?php
	require_once("system-db.php");
	require_once("bookingscopy.php");
	
	start_db();
	
	createNewBookingFromExisting(
			$_POST['id'], 
			convertStringToDate($_POST['date']) . " " . $_POST['time'],
			$_POST['vehicleid'],
			$_POST['ordernumber']
		);
?>
