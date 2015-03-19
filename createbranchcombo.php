<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$customerid = $_GET['id'];
	
	createComboOptions("id", "name", "{$_SESSION['DB_PREFIX']}branch", "WHERE customerid = $customerid", true);
?>