<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$vehicletypeid = $_POST['vehicletypeid'];

	if ($vehicletypeid == 0) {
		$where = " ";
		
	} else {
		$where = "WHERE vehicletypeid = $vehicletypeid";
	}
	
	createCombo("vehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", $where, false, false, array(), true);
?>