<?php
	require_once("holidaylib.php");
	require_once("businessobjects/HolidayAdminClass.php");
	
	$crud = new HolidayCrud();
	$holidayClass = new HolidayAdminClass();
	
	if (! isUserInRole("ADMIN")) {
		$memberid = getLoggedOnMemberID();
		
		$crud->sql = 
			"SELECT A.*, 
			 B.prorataholidayentitlement,
			 B.fullname, 
			 (
			 	SELECT SUM(D.daystaken) 
			 	FROM {$_SESSION['DB_PREFIX']}holiday D 
				WHERE D.startdate >= '{$holidayClass->getStart()}'
			  	AND   D.startdate <  '{$holidayClass->getEnd()}'
			 	AND D.memberid = A.memberid 
			 	AND D.acceptedby IS NOT NULL
			 ) AS daysremaining 
			 FROM {$_SESSION['DB_PREFIX']}holiday A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid 
			 WHERE B.member_id = $memberid
			 AND A.acceptedby IS NULL 
			 AND A.rejectedby IS NULL";
		
	} else {
		$crud->sql = 
			"SELECT A.*, 
			 B.prorataholidayentitlement,
			 B.fullname, 
			 (
			 	SELECT SUM(D.daystaken) 
			 	FROM {$_SESSION['DB_PREFIX']}holiday D 
				WHERE D.startdate >= '{$holidayClass->getStart()}'
			  	AND   D.startdate <  '{$holidayClass->getEnd()}'
			 	AND D.memberid = A.memberid 
			 	AND D.acceptedby IS NOT NULL
			 ) AS daysremaining 
			 FROM {$_SESSION['DB_PREFIX']}holiday A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid
			 WHERE A.acceptedby IS NULL 
			 AND A.rejectedby IS NULL";
	}

	$crud->run();
?>