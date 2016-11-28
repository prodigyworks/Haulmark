<?php
	require_once("../system-db.php");
	require_once("../businessobjects/BookingClass.php");
	require_once("../businessobjects/BookingLegClass.php");
	
	start_db();
	
	try {
		testInsertBookingLeg();
		
		mysql_query("ROLLBACK");
		
	} catch (Exception $e) {
		echo "Test failed : " . $e->getMessage();
	}
	
	function testInsertBookingLeg() {
		$bookingLegClass = new BookingLegClass();
		$bookingLegClass->bookingid = 2;
		$bookingLegClass->status = "Y";
		$bookingLegClass->arrivaltime = date("Y-m-d");
		$bookingLegClass->insert();
		
		assert($bookingLegClass->id != null);
	}
?>