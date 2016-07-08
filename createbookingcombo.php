<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$customerid = $_POST['customerid'];
	
	createBookingComboOptions("WHERE customerid = $customerid AND statusid = 7");
?>