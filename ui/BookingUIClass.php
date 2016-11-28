<?php
	require_once("../system-db.php");
	require_once("../businessobjects/BookingClass.php");
	
	class BookingUIClass {
		
		public function __construct() {
			start_db();
		}
		
		public function load($array) {
			$booking = new BookingClass();
			$booking->load($array['id']);
			$booking->loadLegs();
			
			return $booking->toJSON();
		}
	}
?>