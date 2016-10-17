<?php
	include("system-db.php");
	
	start_db();
	
	if (isMobileUserAgent()) {
		header("location: m.index.php");
		
	} else {
		if (isUserInRole("CUSTOMER")) {
			header("location: pod.php");
	
		} else {
			header("location: booking.php");
		}
	}
?>
