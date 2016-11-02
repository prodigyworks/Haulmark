<?php
	require_once("absencelib.php");
	
	$crud = new AbsenceCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	
	if (! isUserInRole("ADMIN")) {
		$memberid = getLoggedOnMemberID();
		
		$crud->sql = 
			"SELECT A.*,
			 B.fullname
			 FROM {$_SESSION['DB_PREFIX']}absence A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid 
			 WHERE A.memberid = $memberid
			 AND A.absencetype = 'Sick'";
		
	} else {
		$crud->sql = 
			"SELECT A.*, B.fullname
			 FROM {$_SESSION['DB_PREFIX']}absence A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			 ON B.member_id = A.memberid
			 WHERE A.absencetype = 'Sick'";
	}
	
	$crud->run();
?>