<?php
	require_once("system-db.php");
	
	start_db();

	$sql = "SELECT * FROM hallmark_images;";
	$result = mysql_query($sql);
	
	if (! $result) {
		logError(mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$id = $member['id'];
		$uuid = uniqid();
		$sql = "UPDATE hallmark_images SET uuid = '$uuid' WHERE id = $id and uuid IS NULL";
		
		echo $sql . "<br>";
		
		if (!mysql_query($sql)) {
			logError(mysql_error());
		}
	}
	
	mysql_query("ALTER TABLE hallmark_images ADD UNIQUE INDEX uuid (uuid)");
	
	mysql_query("COMMIT");
?>
