<?php
	require_once("system-db.php");
	
	start_db();

	$sql = "ALTER TABLE hallmark_bookingleg
			ADD COLUMN sequence INT(10) NOT NULL AFTER pallets;";
		
	echo str_replace("\n", "<br>", $sql);
		
	$result = mysql_query($sql);
	
	if (! $result) {
		logError(mysql_error());
	}
	
	mysql_query("COMMIT");
?>
