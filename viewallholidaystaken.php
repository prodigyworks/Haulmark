<?php 
	require_once("holidaylib.php"); 
	
	$crud = new HolidayCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	$crud->subapplications = array();
	$crud->sql = 
		"SELECT A.*, " .
		"B.prorataholidayentitlement," .
		"B.name, " .
		"(SELECT SUM(D.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday D WHERE YEAR(D.startdate) = YEAR(A.startdate) AND D.memberid = A.memberid AND D.acceptedby IS NOT NULL) AS daysremaining " .
		"FROM {$_SESSION['DB_PREFIX']}holiday A " .
		"INNER JOIN {$_SESSION['DB_PREFIX']}driver B " .
		"ON B.id = A.memberid " .
		"WHERE A.acceptedby IS NOT NULL AND A.startdate <= CURDATE() ";
	
	$crud->run();
?>
