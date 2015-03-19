<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$table = $_POST['table'];
	$pkname = $_POST['pkname'];
	$id = $_POST['id'];
	
	$qry = "DELETE FROM $table WHERE $pkname = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error(), false);
	}
	
	mysql_query("COMMIT");
?>