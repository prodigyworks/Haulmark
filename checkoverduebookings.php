<?php
	require_once("system-db.php");
	
	start_db();
	
	$index = 0;
	$error = "";
	$sql = "SELECT A.id, A.legsummary, B.name
			FROM {$_SESSION['DB_PREFIX']}booking A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE A.statusid IN (4, 5, 6)
			AND A.enddatetime < NOW()
			AND A.enddatetime >= DATE_SUB(NOW(), INTERVAL 24 hour)
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$alternate = ($index++ % 2 ? "alternate" : "");
		$error .= "<span class='$alternate'>Booking: <a href='javascript:openBooking(" . $member['id'] . ")'>" . getBookingReference($member['id']) . " - " . $member['legsummary'] . "</a> is overdue, </span>";
	}
	
	$sql = "SELECT A.id, A.legsummary, B.name
			FROM {$_SESSION['DB_PREFIX']}booking A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE A.statusid IN (1, 2, 3, 4, 5)
			AND A.startdatetime < NOW()
			AND A.startdatetime >= DATE_SUB(NOW(), INTERVAL 24 hour)
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$alternate = ($index++ % 2 ? "alternate" : "");
		$error .= "<span class='$alternate'>Booking: <a href='javascript:openBooking(" . $member['id'] . ")'>" . getBookingReference($member['id']) . " - " . $member['legsummary'] . "</a> is late starting, </span>";
	}
	
	if ($index == 0) {
		return "";
	}

	echo "<marquee>$error</marquee>";
?>