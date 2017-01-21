<?php
	require_once("system-db.php");
	require_once("businessobjects/BookingCollectionClass.php");
	
	$bookings = new BookingCollectionClass();
	$bookings->loadOverdueBookings();
	
	$index = 0;
	$error = "";
	
	foreach ($bookings->getBookings() AS $booking) {
		$alternate = ($index++ % 2 ? "alternate" : "");
		$error .= "<span class='$alternate'>Booking: <a href='javascript:openBooking({$booking->getId()})'>{$booking->getFormattedID()} - {$booking->getLegsummary()}</a> is overdue, </span>";
	}
	
	$bookings->loadLateBookings();
	
	foreach ($bookings->getBookings() AS $booking) {
		$alternate = ($index++ % 2 ? "alternate" : "");
		$error .= "<span class='$alternate'>Booking: <a href='javascript:openBooking({$booking->getId()})'>{$booking->getFormattedID()} - {$booking->getLegsummary()}</a> is late starting, </span>";
	}
	
	if ($index == 0) {
		return "";
	}

	echo "<marquee>$error</marquee>";
?>