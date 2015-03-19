<?php
	require_once("bookingslib.php");
	
	$crud = new BookingCrud();
	$crud->allowView = false;
	$crud->run();
?>
