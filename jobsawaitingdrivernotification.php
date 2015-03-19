<?php
	require_once("bookingslib.php");
	require_once("allocationfunctions.php");
	
	function notify() {
		sendNotification(
				$_POST['notifyid'], 
				$_POST['notifystatusid'], 
				"Driver Notification", 
				"Notification of job {$id}"
			);
	}
	
	$crud = new AllocateBookingCrud();
	$crud->run();
?>
