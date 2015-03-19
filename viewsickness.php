<?php
	require_once("absencelib.php");
	
	$crud = new AbsenceCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	$crud->sql = 
		"SELECT A.*, " .
		"B.name " .
		"FROM {$_SESSION['DB_PREFIX']}absence A " .
		"INNER JOIN {$_SESSION['DB_PREFIX']}driver B " .
		"ON B.id = A.memberid  " .
		"WHERE A.absencetype = 'Sick'";
	
	$crud->sql = ($crud->sql);
	$crud->run();
?>