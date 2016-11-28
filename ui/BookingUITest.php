<?php
	require_once("businessobjecttojsoncall.php");
	
	try {
		testLoadBooking();
		
		mysql_query("ROLLBACK");
		
	} catch (Exception $e) {
		echo "Test failed : " . $e->getMessage();
	}
	
	function testLoadBooking() {
		echo call("BookingUIClass", "load", array("id" => 171));
	}
?>