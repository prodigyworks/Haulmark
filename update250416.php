<?php
	require_once("system-db.php");
	
	start_db();

	$sql = "ALTER TABLE {$_SESSION['DB_PREFIX']}chat ADD COLUMN completeddatetime DATETIME NULL DEFAULT NULL";
	$result = mysql_query($sql);
	
	if (! $result) {
		logError(mysql_error());
	}
?>
