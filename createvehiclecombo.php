<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$vehicletypeid = $_POST['vehicletypeid'];

	if ($vehicletypeid == 0) {
		$where = "WHERE active = 'Y'";
		
	} else {
		$where = "WHERE vehicletypeid = $vehicletypeid AND active = 'Y'";
	}
	
	createComboOptions("id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", $where);
?>