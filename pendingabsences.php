<?php
	require_once("absencelib.php");
	
	$crud = new AbsenceCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	
	if (isUserInRole("ADMIN") || isUserInRole("DIRECTOR")) {
		$crud->allowEdit = true;
	}
	
	if (! isUserInRole("ADMIN")) {
		$memberid = getLoggedOnMemberID();
		
		$crud->sql = 
			"SELECT A.*,
			 B.fullname
			 FROM {$_SESSION['DB_PREFIX']}absence A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid 
			 WHERE A.memberid = $memberid
			 AND A.acceptedby IS NULL 
			 AND A.rejectedby IS NULL";
		
	} else {
		$crud->sql = 
			"SELECT A.*, B.fullname
			 FROM {$_SESSION['DB_PREFIX']}absence A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid
			 WHERE A.acceptedby IS NULL 
			 AND A.rejectedby IS NULL";
	}
		
	$crud->run();
?>