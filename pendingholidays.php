<?php
	require_once("holidaylib.php");
	
	$crud = new HolidayCrud();
	
	if (! isUserInRole("ADMIN")) {
		$memberid = getLoggedOnMemberID();
		
		$crud->sql = 
			"SELECT A.*, 
			 B.prorataholidayentitlement,
			 B.fullname, 
			 (
			 	SELECT SUM(D.daystaken) 
			 	FROM {$_SESSION['DB_PREFIX']}holiday D 
			 	WHERE YEAR(D.startdate) = YEAR(A.startdate) 
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
			 	WHERE YEAR(D.startdate) = YEAR(A.startdate) 
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