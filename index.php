<?php
	include("system-db.php");
	
	start_db();
	
	if (isUserInRole("CUSTOMER")) {
		header("location: pod.php");

	} else {
		header("location: booking.php");
	}
?>
