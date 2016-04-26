<?php
	require_once("system-db.php");
	
	start_db();
	
	set_time_limit(0);
	
	$sql = "SELECT id, filename
			FROM {$_SESSION['DB_PREFIX']}documents
			WHERE compressed = 0";
	$result = mysql_query($sql);
	$rows = 0;
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$id = $member['id'];
			$filename = $member['filename'];
			$rows++;
			echo "<div>$rows - File:$filename</div>";
			ob_flush();
			
			$sql = "SELECT image
					FROM {$_SESSION['DB_PREFIX']}documents
					WHERE id = $id";
			$itemresult = mysql_query($sql);
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$compressed = mysql_escape_string(gzcompress($itemmember['image'], 9));
				}
				
			} else {
				logError("ERROR: $sql - " . mysql_error());
			}
							
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}documents SET
					image = '$compressed',
					compressed = 1
					WHERE id = $id
					AND compressed = 0";
			
			if (! mysql_query($sql)) {
				logError("$sql - " . mysql_error());
			}
		}
		
	} else {
		logError("ERROR: $sql - " . mysql_error());
	}
	
	echo "<div>rows:$rows</div>";
	
?>
