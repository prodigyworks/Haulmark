<?php 
	require_once("system-db.php"); 
	require_once("viewholiday.php"); 
	
	start_db();
	
	$holidayClass = new HolidayAdminClass();
	
	viewHoliday(
			"SELECT A.*, " .
			"DATE_FORMAT(A.requesteddate, '%d/%m/%Y') AS requesteddate, " .
			"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
			"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate, " .
			"(CURDATE() >= A.startdate) AS taken, " .
			"B.prorataholidayentitlement - IFNULL((SELECT SUM(C.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday C WHERE C.memberid = B.member_id AND YEAR(C.startdate) = YEAR(CURDATE()) AND C.startdate <= NOW() AND C.acceptedby IS NOT NULL), 0) AS daysremaining, " .
			"B.firstname, B.lastname " .
			"FROM {$_SESSION['DB_PREFIX']}holiday A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.memberid = " . $_GET['id'] . " " .
			"ORDER BY A.requesteddate"
		);
?>
