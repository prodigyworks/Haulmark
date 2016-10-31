<?php
	include("system-db.php");
	
	start_db();
	
	if (isMobileUserAgent()) {
		header("location: m.index.php");
		
	} else {
		if (isUserInRole("CUSTOMER")) {
			header("location: pod.php");
	
		} else if (isUserInRole("ALLEGRO") || isUserInRole("ADMIN")) {
			header("location: booking.php");
	
		} else if (isUserInRole("MAINTENANCE")) {
			header("location: maintenance.php");
			
		} else {
			header("location: booking.php");
		}
	}
?>
