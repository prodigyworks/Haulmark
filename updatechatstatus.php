<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$value = $_POST['value'];
	$id = $_POST['id'];

	if ($value == "N") {
		$completedtime = "NULL";

	} else {
		$completedtime = "NOW()";
	}
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}chat
			SET status = '$value', 
			metamodifieddate = NOW(),
			completeddatetime = " . $completedtime . "
			WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error(), false);
	}
	
	mysql_query("COMMIT");
?>