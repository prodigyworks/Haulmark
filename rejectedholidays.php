<?php 
	require_once("holidaylib.php"); 
	require_once("businessobjects/HolidayAdminClass.php");
	
	$crud = new HolidayCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	$crud->subapplications = array();
	$crud->subapplications = array(
			array(
				'id'		  => 'approvebutton',
				'title'		  => 'Approve',
				'imageurl'	  => 'images/approve.png',
				'script' 	  => 'approveHoliday'
			)
		);
		
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
			 AND A.rejectedby IS NOT NULL";
		
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
			 WHERE A.rejectedby IS NOT NULL";
	}

	$crud->run();
?>
